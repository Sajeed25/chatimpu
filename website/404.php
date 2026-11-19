<?php
require __DIR__ . '/_lib.php';
http_response_code(404);
page_start(['title' => 'పేజీ దొరకలేదు · Chatimpu', 'noindex' => true]);
?>
<section class="section"><div class="wrap narrow center">
  <p class="big404">404</p>
  <h1 class="h2"><?= t('ఈ పేజీ దొరకలేదు', 'Page not found') ?></h1>
  <p class="lead"><?= t('లింక్ తప్పు కావచ్చు లేదా పేజీ తొలగించబడి ఉండవచ్చు.', 'The link may be wrong, or the page was removed.') ?></p>
  <a class="btn btn-red" href="/"><?= t('హోమ్ పేజీకి వెళ్ళండి', 'Go to the home page') ?></a>
</div></section>
<?php page_end();
