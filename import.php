<?php
// import_md.php — run via browser at http://localhost/import_md/import_md.php
ini_set('display_errors', 1);
date_default_timezone_set('America/Bogota');
set_time_limit(0); // avoid script timeout during batch

require __DIR__ . '/Parsedown.php';
$pd = new Parsedown();

/* ====== CONFIG ====== */
$BASE    = "https://docs.julibe.com";   // WP site (no trailing slash is fine)
$USER    = "julibe";
$PASS    = "m3qy widB bTtm uDks Hl5T Nkao";  // WordPress Application Password
$ROOT    = __DIR__ . "/markdown";       // local markdown tree (recurses)
$STATUS  = "publish";                   // "publish" or "draft"
$USE_DIR_CATEGORIES = true;             // map subfolders -> categories
$BATCH_SIZE = 40;                       // process 20 files per run
$STATE_FILE = __DIR__ . "/import_state.json";
$LOG_TXT = __DIR__ . "/import.log";
$LOG_JSONL = __DIR__ . "/import.ndjson";
/* ==================== */

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function log_line($level, $data, $echo=true){
  global $LOG_TXT, $LOG_JSONL;
  $ts = date('c');
  $msg = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
  if ($echo) echo $ts." | ".$level." | ".$msg."\n";
  @file_put_contents($LOG_TXT, $ts." | ".$level." | ".$msg."\n", FILE_APPEND);
  $rec = is_array($data) ? $data : ['message'=>$data];
  $rec['level']=$level; $rec['ts']=$ts;
  @file_put_contents($LOG_JSONL, json_encode($rec, JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND);
}

/* === HTTP helper === */
function curl_json($method, $url, $user, $pass, $body = null) {
  $ch = curl_init($url);
  $headers = ['Accept: application/json'];
  if ($body !== null) {
    $headers[] = 'Content-Type: application/json; charset=UTF-8';
    $body = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_USERPWD        => "{$user}:{$pass}",
    CURLOPT_TIMEOUT        => 30,
  ]);
  if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  $resp = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);
  $json = null;
  if ($resp !== false) {
    $dec = json_decode($resp, true);
    $json = is_array($dec) ? $dec : ['raw' => mb_substr($resp, 0, 500)];
  }
  return ['code'=>$code?:0,'err'=>$err?:null,'json'=>$json];
}

/* === Content helpers === */
function first_h1_and_body(string $raw): array {
  if (preg_match('/^\xEF\xBB\xBF?\s*#\s+(.+?)\R(.*)$/s', $raw, $m)) {
    return [trim($m[1]), ltrim($m[2])];
  }
  return [null, $raw];
}
function slugify(string $filename): string {
  $text = preg_replace('/\.md$/i', '', $filename);
  $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
  $text = strtolower($text);
  $text = preg_replace('~[^a-z0-9]+~', '-', $text);
  $text = trim($text, '-');
  return $text ?: ('post-'.substr(md5(random_bytes(6)),0,8));
}

/* === Term handling (create only if allowed in preflight) === */
$CAN_CREATE_TERMS = false;
function ensure_terms_or_lookup($base, $user, $pass, $taxonomy, $names) {
  global $CAN_CREATE_TERMS;
  $ids = [];
  foreach ($names as $name) {
    $name = trim($name);
    if ($name === '') continue;
    $q = rtrim($base,'/')."/wp-json/wp/v2/{$taxonomy}?search=".rawurlencode($name)."&per_page=1";
    $res = curl_json('GET', $q, $user, $pass);
    if ($res['code'] < 400 && !empty($res['json'][0]['id']) &&
        mb_strtolower($res['json'][0]['name']) === mb_strtolower($name)) {
      $ids[] = (int)$res['json'][0]['id'];
      continue;
    }
    if ($CAN_CREATE_TERMS) {
      $create = curl_json('POST', rtrim($base,'/')."/wp-json/wp/v2/{$taxonomy}", $user, $pass, ['name'=>$name]);
      if ($create['code'] < 400 && isset($create['json']['id'])) $ids[] = (int)$create['json']['id'];
    }
  }
  return $ids;
}

/* === Existence check by slug === */
function post_exists_by_slug($base, $user, $pass, $slug): bool {
  $url = rtrim($base,'/')."/wp-json/wp/v2/posts?slug=".rawurlencode($slug)."&per_page=1";
  $res = curl_json('GET', $url, $user, $pass);
  return ($res['code'] === 200 && is_array($res['json']) && count($res['json']) > 0);
}

/* === Preflight (once or reset) === */
function preflight(&$STATUS, &$CAN_CREATE_TERMS, $BASE, $USER, $PASS, $USE_DIR_CATEGORIES) {
  log_line('INFO', "== Preflight ==");
  $root = curl_json('GET', rtrim($BASE,'/').'/wp-json/', $USER, $PASS);
  if ($root['code'] !== 200) { log_line('ERROR', "API not reachable (HTTP {$root['code']})"); exit; }
  log_line('OK', "API reachable");

  $me = curl_json('GET', rtrim($BASE,'/').'/wp-json/wp/v2/users/me', $USER, $PASS);
  if ($me['code'] !== 200) {
    $snippet = substr(json_encode($me['json'], JSON_UNESCAPED_UNICODE), 0, 200);
    log_line('ERROR', "Auth failed (HTTP {$me['code']}): {$snippet}");
    exit;
  }
  log_line('OK', "Auth OK");

  $probe = curl_json('POST', rtrim($BASE,'/').'/wp-json/wp/v2/posts', $USER, $PASS, [
    'title'=>'__probe__ '.uniqid(),'content'=>'probe','status'=>'draft'
  ]);
  if ($probe['code'] === 201 && !empty($probe['json']['id'])) {
    log_line('OK', "Can create DRAFT (id={$probe['json']['id']})");
    curl_json('DELETE', rtrim($BASE,'/').'/wp-json/wp/v2/posts/'.$probe['json']['id'].'?force=true', $USER, $PASS);
  } else { log_line('ERROR', "Cannot create DRAFT (HTTP {$probe['code']})"); exit; }

  if (strtolower($STATUS) === 'publish') {
    $probe2 = curl_json('POST', rtrim($BASE,'/').'/wp-json/wp/v2/posts', $USER, $PASS, [
      'title'=>'__probe_pub__ '.uniqid(),'content'=>'probe','status'=>'publish'
    ]);
    if ($probe2['code'] === 201) {
      log_line('OK', "Can PUBLISH");
      curl_json('DELETE', rtrim($BASE,'/').'/wp-json/wp/v2/posts/'.$probe2['json']['id'].'?force=true', $USER, $PASS);
    } else {
      log_line('WARN', "Cannot PUBLISH (HTTP {$probe2['code']}) → downgrade to DRAFT");
      $STATUS = 'draft';
    }
  }

  if ($USE_DIR_CATEGORIES) {
    $dummy='__probe_term__'.uniqid();
    $try = curl_json('POST', rtrim($BASE,'/').'/wp-json/wp/v2/categories', $USER, $PASS, ['name'=>$dummy]);
    if ($try['code'] === 201 && !empty($try['json']['id'])) {
      $CAN_CREATE_TERMS = true; log_line('OK', "Can CREATE categories");
      curl_json('DELETE', rtrim($BASE,'/').'/wp-json/wp/v2/categories/'.$try['json']['id'].'?force=true', $USER, $PASS);
    } else { $CAN_CREATE_TERMS = false; log_line('WARN', "Cannot CREATE categories (assign existing only)"); }
  }

  log_line('INFO', "== Preflight OK ==");
}

/* === Build file list (only .md), sorted === */
function build_file_list($root){
  if (!is_dir($root)) return [];
  $list = [];
  $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
  foreach ($it as $f) {
    if ($f->isFile() && strtolower($f->getExtension()) === 'md') $list[] = $f->getPathname();
  }
  sort($list, SORT_NATURAL | SORT_FLAG_CASE);
  return $list;
}

/* === UI control === */
$reset = isset($_GET['reset']);
$run   = isset($_GET['run']);

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><title>Markdown → WordPress Importer</title>
<style>
body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;background:#0b0f14;color:#e6edf3;margin:0;padding:24px}
.card{max-width:980px;margin:0 auto;background:#0f1620;border:1px solid #1e2631;border-radius:12px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,.25)}
.btn{display:inline-block;background:#1f6feb;color:#fff;border:none;border-radius:8px;padding:10px 14px;text-decoration:none;font-weight:600;margin-right:8px}
.ok{color:#7ee787}.fail,.err{color:#ff7b72}.warn{color:#ffce6b}.folder{color:#89b4fa}
pre.inline{white-space:pre-wrap;background:#091018;padding:10px;border-radius:8px;border:1px solid #1e2631}
</style>
</head><body><div class="card">
<h1>Markdown → WordPress Importer (Batch = <?=$BATCH_SIZE?>)</h1>
<div>Site: <code><?=h($BASE)?></code> | User: <code><?=h($USER)?></code> | Root: <code><?=h($ROOT)?></code></div>
<div style="margin:12px 0">
  <a class="btn" href="<?=h($_SERVER['PHP_SELF'])?>?run=1">Run batch</a>
  <a class="btn" style="background:#30363d" href="<?=h($_SERVER['PHP_SELF'])?>?reset=1">Reset</a>
</div>
<pre class="inline">
<?php
// Reset or initialize state
if ($reset || !file_exists($STATE_FILE)) {
  preflight($STATUS, $CAN_CREATE_TERMS, $BASE, $USER, $PASS, $USE_DIR_CATEGORIES);
  $files = build_file_list($ROOT);
  file_put_contents($STATE_FILE, json_encode([
    'status' => $STATUS,
    'can_create_terms' => $CAN_CREATE_TERMS,
    'files' => $files,
    'i' => 0
  ], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
  log_line('INFO', "Indexed ".count($files)." markdown files");
  echo "State reset. Ready to run.\n";
}

// Load state
$st = json_decode(@file_get_contents($STATE_FILE), true);
if (!$st) { echo "State file missing or invalid. Click Reset.\n"; }
else {
  $STATUS = $st['status'];
  $CAN_CREATE_TERMS = $st['can_create_terms'];
  $files = $st['files'];
  $i = $st['i'];

  $total = count($files);
  echo "Progress: $i / $total files processed.\n";

  if ($run && $i < $total) {
    $end = min($i + $BATCH_SIZE, $total);
    echo "Processing files ".($i+1)." to ".$end." …\n\n";

    for ($k = $i; $k < $end; $k++) {
      $path = $files[$k];
      $rel  = str_replace('\\','/', substr($path, strlen($ROOT)+1));
      $filenameTitle = pathinfo($path, PATHINFO_FILENAME);
      $slug = slugify($filenameTitle);

      echo "→ $rel (slug: $slug)\n";

      // Skip if post (slug) already exists
      if (post_exists_by_slug($BASE, $USER, $PASS, $slug)) {
        log_line('SKIP', ['file'=>$rel,'reason'=>'slug exists','slug'=>$slug], true);
        echo "   SKIP (post exists)\n\n";
        continue;
      }

      try {
        $raw = file_get_contents($path);
        [$h1, $mdBody] = first_h1_and_body($raw);
        $title = $h1 ?: $filenameTitle;
        $html  = $pd->text($mdBody);

        $payload = ['title'=>$title,'slug'=>$slug,'content'=>$html,'status'=>$STATUS];

        if ($USE_DIR_CATEGORIES) {
          $parts = explode('/', $rel);
          array_pop($parts);
          if (!empty($parts)) {
            $catIds = ensure_terms_or_lookup($BASE, $USER, $PASS, 'categories', $parts);
            if ($catIds) $payload['categories'] = $catIds;
          }
        }

        $res = curl_json('POST', rtrim($BASE,'/').'/wp-json/wp/v2/posts', $USER, $PASS, $payload);
        if ($res['code'] === 201 && isset($res['json']['id'])) {
          log_line('OK', ['file'=>$rel,'title'=>$title,'slug'=>$slug,'wp_id'=>$res['json']['id'],'status_code'=>$res['code']], true);
          echo "   OK (id=".$res['json']['id'].")\n\n";
        } else {
          $snippet = substr(json_encode($res['json'], JSON_UNESCAPED_UNICODE), 0, 300);
          log_line('FAIL', ['file'=>$rel,'title'=>$title,'slug'=>$slug,'status_code'=>$res['code'],'error'=>$res['json'] ?? null], true);
          echo "   FAIL [".$res['code']."] ".$snippet."\n\n";
        }
      } catch (Throwable $e) {
        log_line('ERROR', ['file'=>$rel,'title'=>$filenameTitle,'exception'=>get_class($e),'message'=>$e->getMessage()], true);
        echo "   ERROR ".$e->getMessage()."\n\n";
      }
    }

    // advance pointer and save state
    $st['i'] = $end;
    file_put_contents($STATE_FILE, json_encode($st, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    echo "Batch done. Progress: $end / $total\n";
    if ($end < $total) echo "Click 'Run batch' again to continue.\n";
    else echo "All files processed.\n";
  } else {
    if ($i >= $total) echo "All files processed.\n";
    else echo "Click 'Run batch' to import the next $BATCH_SIZE files.\n";
  }
}
?>
</pre>
<div style="margin-top:12px">
  <a class="btn" href="<?=h($_SERVER['PHP_SELF'])?>?run=1">Run batch</a>
  <a class="btn" style="background:#30363d" href="<?=h($_SERVER['PHP_SELF'])?>?reset=1">Reset</a>
</div>
</div></body></html>
