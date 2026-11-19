<?php
/**
 * Shared links from the app: /s/<story-id> (news card) and /s/v/<video-id> (News Reel).
 * Rendered on the server so WhatsApp, Facebook and Google see the real headline and photo.
 */
require __DIR__ . '/_lib.php';

$id = (string)($_GET['id'] ?? '');
$vid = (string)($_GET['v'] ?? '');
$ok = static fn(string $s) => $s !== '' && strlen($s) <= 64 && preg_match('/^[A-Za-z0-9_-]+$/', $s);
$story = null; $video = null;

if ($ok($id)) {
  [$st, $rows] = sb('GET', 'stories?id=eq.' . rawurlencode($id) . '&status=eq.published&limit=1'
    . '&select=id,image_path,published_at,source,source_url,correction_note,corrected_at,districts(name,name_te),mandals(name,name_te),story_translations(lang,headline,summary)', null, 60);
  $story = $st === 200 && !empty($rows[0]['story_translations']) ? $rows[0] : null;
} elseif ($ok($vid)) {
  [$st, $rows] = sb('GET', 'video_feed?id=eq.' . rawurlencode($vid) . '&limit=1', null, 60);
  $video = $st === 200 && !empty($rows[0]) ? $rows[0] : null;
}

if (!$story && !$video) {
  http_response_code(404);
  page_start(['title' => 'వార్త దొరకలేదు · Chatimpu', 'noindex' => true]); ?>
  <section class="section"><div class="wrap narrow center">
    <h1 class="h2"><?= t('ఈ వార్త అందుబాటులో లేదు', 'This story isn\'t available') ?></h1>
    <p class="lead"><?= t('ఇది తొలగించబడి ఉండవచ్చు, లేదా లింక్ తప్పు కావచ్చు. తాజా వార్తలు యాప్‌లో చూడండి.', 'It may have been removed, or the link is wrong. See the latest news in the app.') ?></p>
    <?= store_buttons('center') ?>
    <p><a href="/"><?= t('హోమ్ పేజీ', 'Home page') ?></a></p>
  </div></section>
  <?php page_end(); exit;
}

if ($story) {
  $tr = pick_lang($story['story_translations']);
  $place = trim(implode(', ', array_filter([$story['mandals']['name_te'] ?? null, $story['districts']['name_te'] ?? $story['districts']['name'] ?? null])));
  $img = media_url($story['image_path'] ?? '');
  $headline = $tr['headline']; $text = $tr['summary']; $lang = $tr['lang'];
  $url = SITE_URL . '/s/' . rawurlencode($story['id']);
} else {
  $cap = $video['caption'] ?? []; $hl = $video['headline'] ?? [];
  $headline = (is_array($hl) ? ($hl['te'] ?? $hl['en'] ?? '') : '') ?: (is_array($cap) ? ($cap['te'] ?? $cap['en'] ?? '') : (string)$cap);
  $text = is_array($cap) ? ($cap['te'] ?? $cap['en'] ?? '') : (string)$cap;
  $lang = 'te'; $img = media_url($video['poster'] ?? ''); $place = '';
  $url = SITE_URL . '/s/v/' . rawurlencode($video['id']);
}
$desc = mb_strlen($text) > 160 ? mb_substr($text, 0, 157) . '…' : $text;

page_start(['title' => $headline . ' · చాటింపు', 'description' => $desc, 'image' => $img ?: null, 'url' => $url, 'type' => 'article']);
?>
<section class="section article-wrap">
  <article class="wrap narrow article">
    <?php if ($story && $img): ?><img class="article-img" src="<?= e($img) ?>" alt="" onerror="this.style.display='none'"><?php endif; ?>
    <?php if ($video): ?>
      <video class="article-img" controls playsinline preload="metadata" <?= $img ? 'poster="' . e($img) . '"' : '' ?> src="<?= e(media_url($video['uri'] ?? '')) ?>"></video>
    <?php endif; ?>
    <p class="article-meta">
      <?php if ($place): ?><span><?= icon('pin') . e($place) ?></span><?php endif; ?>
      <span><?= ago($story['published_at'] ?? $video['publishedAt'] ?? null) ?></span>
      <?php if ($story && !empty($story['source'])): ?><span><?= e($story['source']) ?></span><?php endif; ?>
    </p>
    <h1 class="article-head" lang="<?= e($lang) ?>"><?= e($headline) ?></h1>
    <span class="rule"></span>
    <?php if ($text !== $headline): ?><p class="article-text" lang="<?= e($lang) ?>"><?= nl2br(e($text)) ?></p><?php endif; ?>
    <?php if ($story && !empty($story['correction_note'])): ?>
      <aside class="correction"><b><?= t('సవరణ', 'Correction') ?> · <?= e(date('j M Y', strtotime((string)$story['corrected_at']))) ?></b><?= e($story['correction_note']) ?></aside>
    <?php endif; ?>
    <?php if ($story && !empty($story['source_url'])): ?><p class="muted small"><?= t('మూలం', 'Source') ?>: <a href="<?= e($story['source_url']) ?>" rel="nofollow noopener"><?= e(parse_url($story['source_url'], PHP_URL_HOST) ?: $story['source_url']) ?></a></p><?php endif; ?>

    <div class="article-cta">
      <img src="/assets/emblem-192.png" width="56" height="56" alt="">
      <div>
        <b><?= t('మీ ఊరి మరిన్ని వార్తలు చాటింపు యాప్‌లో', 'More news from your town in the Chatimpu app') ?></b>
        <?= store_buttons() ?>
      </div>
    </div>
    <p class="muted small"><a href="/grievance<?= $story ? '?story=' . e(rawurlencode($story['id'])) : '' ?>"><?= icon('flag') . t('ఈ వార్తలో తప్పు ఉందా? తెలియజేయండి', 'Something wrong in this story? Tell us') ?></a></p>
  </article>
</section>
<?php page_end();
