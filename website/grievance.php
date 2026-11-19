<?php
/** Complaints & corrections (IT Rules 2021 grievance redressal): officer details + a form that files a grievance. */
require __DIR__ . '/_lib.php';
session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

$cats = [
  'content' => ['వార్తలో తప్పు లేదా హానికరమైన విషయం', 'Wrong or harmful content'], 'defamation' => ['పరువు నష్టం', 'Defamation'],
  'privacy' => ['గోప్యత / నా ఫోటో', 'Privacy / my photo'], 'copyright' => ['కాపీరైట్', 'Copyright'],
  'reporter_conduct' => ['రిపోర్టర్ ప్రవర్తన', 'Reporter conduct'], 'other' => ['ఇతర', 'Other'],
];
$story = preg_match('/^[A-Za-z0-9_-]{1,64}$/', (string)($_GET['story'] ?? $_POST['story'] ?? '')) ? (string)($_GET['story'] ?? $_POST['story']) : '';
$form = ['name' => '', 'contact' => '', 'category' => 'content', 'details' => ''];
$error = ''; $done = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($form as $k => $_) $form[$k] = trim((string)($_POST[$k] ?? ''));
  if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? '')) || ($_POST['website'] ?? '') !== '') {
    $error = 'Please reload the page and try again.';
  } elseif (!isset($cats[$form['category']]) || mb_strlen($form['name']) < 2 || mb_strlen($form['contact']) < 6 || mb_strlen($form['details']) < 20 || mb_strlen($form['details']) > 3000) {
    $error = 'Please fill in your name, a phone number or email, and at least 20 characters of details.';
  } elseif (!sb_ready()) {
    $error = 'Complaints can\'t be sent yet. Please email ' . EMAIL_GRIEVANCE . '.';
  } else {
    [$st, $res] = sb('POST', 'rpc/submit_grievance', ['p_name' => $form['name'], 'p_contact' => $form['contact'], 'p_category' => $form['category'], 'p_details' => $form['details'], 'p_story' => $story ?: null]);
    if ($st === 200 && !empty($res['code'])) { $done = $res; $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
    else $error = is_array($res) && !empty($res['message']) ? $res['message'] : 'Something went wrong. Please email ' . EMAIL_GRIEVANCE . '.';
  }
}

[$st, $o] = sb('GET', 'app_settings?key=eq.grievance_officer&select=value', null, 300);
$officer = $st === 200 && !empty($o[0]['value']['name']) ? $o[0]['value'] : null;

page_start(['title' => 'ఫిర్యాదులు, సవరణలు · Complaints & corrections · Chatimpu']);
?>
<section class="section">
  <div class="wrap narrow">
    <p class="eyebrow"><?= t('నమ్మకం', 'Trust') ?></p>
    <h1 class="h2"><?= t('ఫిర్యాదులు, సవరణలు', 'Complaints & corrections') ?></h1>
    <p class="lead"><?= t(
      'చిన్న తప్పులకు, యాప్‌లో ఆ వార్త కింద "ఫిర్యాదు" నొక్కండి. పరువు నష్టం, గోప్యత, కాపీరైట్ వంటి అధికారిక ఫిర్యాదులకు ఈ ఫారం వాడండి. 24 గంటల్లో అందినట్లు తెలియజేసి, 15 రోజుల్లో పరిష్కరిస్తాం.',
      'For a small mistake, tap "Report" under the story in the app. Use this form for formal complaints such as defamation, privacy or copyright. We acknowledge within 24 hours and resolve within 15 days.') ?></p>

    <?php if ($done): ?>
      <div class="notice notice-good" role="status">
        <b><?= t('ఫిర్యాదు అందింది', 'Complaint received') ?></b>
        <p class="code"><?= e($done['code']) ?></p>
        <p><?= t('ఈ కోడ్‌ను గుర్తుంచుకోండి. మీరు ఇచ్చిన ఫోన్ / ఈమెయిల్‌కు జవాబిస్తాం.', 'Keep this code. We will reply to the phone number or email you gave.') ?></p>
      </div>
    <?php else: ?>
      <form class="form" method="post" action="/grievance" novalidate>
        <?php if ($error): ?><div class="notice notice-bad" role="alert"><?= e($error) ?></div><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
        <input type="hidden" name="story" value="<?= e($story) ?>">
        <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
        <?php if ($story): ?><p class="muted small"><?= t('ఈ ఫిర్యాదు వార్త', 'This complaint is about story') ?> <a href="/s/<?= e(rawurlencode($story)) ?>"><?= e($story) ?></a></p><?php endif; ?>
        <fieldset>
          <legend><?= t('ఫిర్యాదు రకం', 'Type of complaint') ?></legend>
          <div class="radios">
            <?php foreach ($cats as $k => [$te, $en]): ?>
              <label><input type="radio" name="category" value="<?= e($k) ?>" <?= $form['category'] === $k ? 'checked' : '' ?>> <?= t($te, $en) ?></label>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <label><?= t('మీ పేరు', 'Your name') ?><input name="name" required minlength="2" maxlength="100" value="<?= e($form['name']) ?>" autocomplete="name"></label>
        <label><?= t('ఫోన్ లేదా ఈమెయిల్', 'Phone or email') ?><input name="contact" required minlength="6" maxlength="120" value="<?= e($form['contact']) ?>" autocomplete="email">
          <small><?= t('మీకు జవాబు ఇవ్వడానికి మాత్రమే. ఎప్పుడూ ప్రచురించం.', 'Only used to reply to you. Never published.') ?></small></label>
        <label><?= t('వివరాలు', 'Details') ?><textarea name="details" required minlength="20" maxlength="3000" rows="6"><?= e($form['details']) ?></textarea>
          <small><?= t('ఏ వార్త, ఏది తప్పు, మీరు ఏం కోరుతున్నారు.', 'Which story, what is wrong, and what you would like us to do.') ?></small></label>
        <button class="btn btn-red" type="submit"><?= t('ఫిర్యాదు పంపండి', 'Send complaint') ?></button>
      </form>
    <?php endif; ?>

    <div class="officer">
      <h2 class="h3"><?= t('ఫిర్యాదుల అధికారి', 'Grievance officer') ?></h2>
      <?php if ($officer): ?>
        <p><b><?= e($officer['name']) ?></b><br>
          <?php if (!empty($officer['email'])): ?><a href="mailto:<?= e($officer['email']) ?>"><?= e($officer['email']) ?></a><br><?php endif; ?>
          <?php if (!empty($officer['phone'])): ?><?= e($officer['phone']) ?><br><?php endif; ?>
          <?php if (!empty($officer['address'])): ?><span class="muted"><?= e($officer['address']) ?></span><?php endif; ?></p>
      <?php else: ?>
        <p><a href="mailto:<?= e(EMAIL_GRIEVANCE) ?>"><?= e(EMAIL_GRIEVANCE) ?></a></p>
      <?php endif; ?>
      <p class="muted small"><?= t('మా పరిష్కారంతో సంతృప్తి లేకపోతే, డిజిటల్ వార్తా ప్రచురణకర్తల స్వీయ-నియంత్రణ సంస్థకు అప్పీల్ చేయవచ్చు.', 'If you are not satisfied with our decision, you can appeal to the self-regulating body for digital news publishers.') ?></p>
    </div>
  </div>
</section>
<?php page_end();
