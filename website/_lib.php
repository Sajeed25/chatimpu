<?php
/** Shared helpers for the public website (no framework, runs on any PHP 7.4+ host). */
declare(strict_types=1);
require __DIR__ . (is_file(__DIR__ . '/_config.php') ? '/_config.php' : '/_config.example.php');

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/** Both languages in the page; CSS shows the one the reader picked (Telugu by default). */
function t(string $te, string $en): string {
  return '<span class="te" lang="te">' . $te . '</span><span class="en" lang="en">' . $en . '</span>';
}

function sb_ready(): bool { return SUPABASE_URL !== '' && SUPABASE_ANON_KEY !== ''; }

/**
 * Call Supabase (PostgREST) with the public anon key, the same access as a signed-out app user.
 * GET responses can be cached on disk for a few seconds to keep the site fast under share spikes.
 * Returns [status, decoded JSON or null].
 */
function sb(string $method, string $path, ?array $body = null, int $cache = 0): array {
  if (!sb_ready()) return [0, null];
  $url = rtrim(SUPABASE_URL, '/') . '/rest/v1/' . ltrim($path, '/');
  $file = $cache ? sys_get_temp_dir() . '/chatimpu-' . md5($url) . '.json' : '';
  if ($file && is_file($file) && filemtime($file) > time() - $cache) {
    return [200, json_decode((string)file_get_contents($file), true)];
  }
  $headers = ['apikey: ' . SUPABASE_ANON_KEY, 'Authorization: Bearer ' . SUPABASE_ANON_KEY, 'Accept: application/json', 'Content-Type: application/json'];
  $payload = $body === null ? null : json_encode($body);
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => $headers, CURLOPT_TIMEOUT => 8, CURLOPT_CONNECTTIMEOUT => 4]);
    if ($payload !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $raw = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
  } else {
    $ctx = stream_context_create(['http' => ['method' => $method, 'header' => implode("\r\n", $headers), 'content' => $payload ?? '', 'timeout' => 8, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx);
    $status = isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m) ? (int)$m[1] : 0;
  }
  if ($raw === false || $raw === null) return [0, null];
  $data = json_decode((string)$raw, true);
  if ($file && $status >= 200 && $status < 300) @file_put_contents($file, $raw, LOCK_EX);
  return [$status, $data];
}

/** Storage path → public URL (full URLs pass through), same rule as the app. */
function media_url(?string $p): string {
  if (!$p) return '';
  if (preg_match('#^(https?:)?//#', $p)) return $p;
  return rtrim(SUPABASE_URL, '/') . '/storage/v1/object/public/media/' . ltrim($p, '/');
}

/** Pick the Telugu text first, then English, then anything. */
function pick_lang(array $rows): ?array {
  $by = [];
  foreach ($rows as $r) $by[$r['lang']] = $r;
  return $by['te'] ?? $by['en'] ?? (reset($by) ?: null);
}

function ago(?string $iso): string {
  if (!$iso) return '';
  $s = time() - strtotime($iso);
  if ($s < 3600) { $n = max(1, intdiv($s, 60)); return t("$n నిమి. క్రితం", "$n min ago"); }
  if ($s < 86400) { $n = intdiv($s, 3600); return t("$n గం. క్రితం", "$n h ago"); }
  $d = new DateTime($iso); $d->setTimezone(new DateTimeZone('Asia/Kolkata'));
  return e($d->format('j M Y'));
}

function store_buttons(string $variant = ''): string {
  $out = '<div class="stores ' . e($variant) . '">';
  if (PLAY_URL !== '') {
    $out .= '<a class="btn btn-dark" href="' . e(PLAY_URL) . '" rel="noopener">' . icon('play') . t('Google Play లో పొందండి', 'Get it on Google Play') . '</a>';
  } else {
    $out .= '<span class="btn btn-dark is-soon" aria-disabled="true">' . icon('play') . t('Google Play లో త్వరలో', 'Coming soon to Google Play') . '</span>';
  }
  if (APK_URL !== '') $out .= '<a class="btn btn-ghost" href="' . e(APK_URL) . '" download>' . icon('download') . t('APK డౌన్‌లోడ్', 'Download APK') . '</a>';
  return $out . '</div>';
}

/** Small inline icons (no icon font to download). */
function icon(string $name): string {
  $p = [
    'play' => '<path d="M5 3.5v17l14-8.5z"/>',
    'download' => '<path d="M12 3v12m0 0l-5-5m5 5l5-5M4 20h16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
    'pin' => '<path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>',
    'reel' => '<path d="M4 4h16v16H4z" fill="none" stroke="currentColor" stroke-width="2"/><path d="M10 9v6l5-3z"/>',
    'chat' => '<path d="M12 3C7 3 3 6.6 3 11c0 2 .8 3.8 2.2 5.2L4 21l4.9-1.6c1 .4 2 .6 3.1.6 5 0 9-3.6 9-8s-4-9-9-9z"/>',
    'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 1v3M12 20v3M1 12h3M20 12h3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
    'phone' => '<path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1z"/>',
    'people' => '<circle cx="9" cy="8" r="3.5"/><circle cx="17" cy="9" r="2.5"/><path d="M2 20c0-3.9 3.1-6 7-6s7 2.1 7 6zM16 14.2c3.3.2 6 2 6 5.8h-4.5"/>',
    'shield' => '<path d="M12 2l8 3v6c0 5-3.4 9.4-8 11-4.6-1.6-8-6-8-11V5z"/>',
    'pen' => '<path d="M3 17.3V21h3.7L17.8 9.9l-3.7-3.7zM20.7 7c.4-.4.4-1 0-1.4l-2.3-2.3a1 1 0 0 0-1.4 0l-1.8 1.8 3.7 3.7z"/>',
    'school' => '<path d="M12 3L1 9l11 6 9-4.9V17h2V9zM5 13.2v4L12 21l7-3.8v-4L12 17z"/>',
    'wallet' => '<path d="M3 6a2 2 0 0 1 2-2h13v3H5v.5h16V20H5a2 2 0 0 1-2-2zm14 7.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>',
    'flag' => '<path d="M5 21V4h11l-1.5 4L16 12H7v9z"/>',
    'arrow' => '<path d="M5 12h14m-6-6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
  ][$name] ?? '';
  return '<svg class="ic" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">' . $p . '</svg>';
}

/** <head> + site header. $meta: title, description, image, url, type, noindex */
function page_start(array $meta = []): void {
  $title = $meta['title'] ?? 'చాటింపు · Chatimpu: మీ ఊరి వార్తలు 60 పదాల్లో';
  $desc = $meta['description'] ?? 'తెలంగాణ, ఆంధ్రప్రదేశ్ స్థానిక వార్తలు 60 పదాల్లో. Hyperlocal Telugu news from your mandal and district, in 60 words.';
  $img = $meta['image'] ?? SITE_URL . '/assets/og-default.png';
  $url = $meta['url'] ?? SITE_URL . strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
  ?><!doctype html>
<html lang="te" data-lang="te">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<?php if (!empty($meta['noindex'])): ?><meta name="robots" content="noindex"><?php endif; ?>
<link rel="canonical" href="<?= e($url) ?>">
<meta property="og:site_name" content="చాటింపు · Chatimpu">
<meta property="og:type" content="<?= e($meta['type'] ?? 'website') ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:image" content="<?= e($img) ?>">
<meta property="og:url" content="<?= e($url) ?>">
<meta property="og:locale" content="te_IN">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#ED1C24">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32.png">
<link rel="apple-touch-icon" href="/assets/apple-touch-icon.png">
<link rel="preload" href="/assets/fonts/Ramabhadra-Regular.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Noto+Sans+Telugu:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/site.css?v=2">
<script>try{var l=localStorage.getItem('ck-lang');if(l==='en'||l==='te'){document.documentElement.dataset.lang=l;document.documentElement.lang=l}}catch(e){}</script>
</head>
<body>
<a class="skip" href="#main"><?= t('ప్రధాన భాగానికి వెళ్ళండి', 'Skip to content') ?></a>
<header class="site-head">
  <div class="wrap head-row">
    <a class="brand" href="/" aria-label="చాటింపు Chatimpu home">
      <img src="/assets/emblem-192.png" width="40" height="40" alt="">
      <span class="brand-name">చాటింపు</span>
    </a>
    <nav class="nav" aria-label="<?= e('Main') ?>">
      <a href="/#features"><?= t('ఫీచర్లు', 'Features') ?></a>
      <a href="/#reporters"><?= t('రిపోర్టర్లు', 'Reporters') ?></a>
      <a href="/grievance"><?= t('ఫిర్యాదులు', 'Complaints') ?></a>
    </nav>
    <button class="lang" type="button" data-lang-toggle aria-label="Change language / భాష మార్చండి">
      <span class="te">English</span><span class="en">తెలుగు</span>
    </button>
    <a class="btn btn-red btn-sm head-cta" href="/#download"><?= t('యాప్', 'Get the app') ?></a>
  </div>
</header>
<main id="main">
<?php
}

function page_end(): void {
  $y = date('Y'); ?>
</main>
<footer class="site-foot">
  <div class="wrap foot-grid">
    <div>
      <a class="brand brand-foot" href="/"><img src="/assets/emblem-192.png" width="36" height="36" alt=""><span class="brand-name">చాటింపు</span></a>
      <p class="tagline">YOUR LOCALITY. YOUR VOICE. YOUR NEWS.</p>
      <p class="muted"><?= t('తెలంగాణ, ఆంధ్రప్రదేశ్ స్థానిక వార్తలు.', 'Local news for Telangana and Andhra Pradesh.') ?></p>
    </div>
    <div>
      <h2 class="foot-h"><?= t('చాటింపు', 'Chatimpu') ?></h2>
      <a href="/#features"><?= t('ఫీచర్లు', 'Features') ?></a>
      <a href="/#reporters"><?= t('రిపోర్టర్‌గా చేరండి', 'Become a reporter') ?></a>
      <a href="<?= e(PORTAL_URL) ?>"><?= t('రిపోర్టర్ పోర్టల్', 'Reporter portal') ?></a>
    </div>
    <div>
      <h2 class="foot-h"><?= t('నమ్మకం', 'Trust') ?></h2>
      <a href="/grievance"><?= t('ఫిర్యాదులు, సవరణలు', 'Complaints & corrections') ?></a>
      <a href="/privacy"><?= t('గోప్యతా విధానం', 'Privacy policy') ?></a>
      <a href="/terms"><?= t('నిబంధనలు', 'Terms of use') ?></a>
    </div>
    <div>
      <h2 class="foot-h"><?= t('సంప్రదించండి', 'Contact') ?></h2>
      <a href="mailto:<?= e(EMAIL_SUPPORT) ?>"><?= e(EMAIL_SUPPORT) ?></a>
      <a href="mailto:<?= e(EMAIL_REPORTERS) ?>"><?= e(EMAIL_REPORTERS) ?></a>
      <a href="mailto:<?= e(EMAIL_GRIEVANCE) ?>"><?= e(EMAIL_GRIEVANCE) ?></a>
    </div>
  </div>
  <div class="wrap foot-base">© <?= $y ?> Chatimpu · Made in India</div>
</footer>
<script src="/assets/site.js?v=1" defer></script>
</body>
</html>
<?php
}
