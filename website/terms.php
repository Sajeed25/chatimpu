<?php
// Plain-language starting point. Have it reviewed by a lawyer before launch.
require __DIR__ . '/_lib.php';
page_start(['title' => 'నిబంధనలు · Terms of use · Chatimpu']);
?>
<section class="section"><div class="wrap narrow prose">
  <p class="eyebrow"><?= t('నిబంధనలు', 'Terms') ?></p>
  <h1 class="h2"><?= t('వినియోగ నిబంధనలు', 'Terms of use') ?></h1>
  <p class="muted small">Last updated: 19 November 2026</p>

  <div class="summary te" lang="te">
    <p><b>క్లుప్తంగా:</b> చాటింపును గౌరవంగా వాడండి. నిజమైన సమాచారం మాత్రమే పంపండి. ఇతరుల ఫోటోలు, నంబర్లు వారి అనుమతి లేకుండా పెట్టకండి. తప్పు కనిపిస్తే మాకు చెప్పండి.</p>
  </div>

  <h2>Using Chatimpu</h2>
  <p>Chatimpu is a free local-news service for Telangana and Andhra Pradesh. By using the app or this website you agree to these terms.</p>

  <h2>What you post</h2>
  <ul>
    <li>Comments, community posts and news you send must be true to the best of your knowledge and must not be hateful, obscene, defamatory or illegal, or inflame religious or caste tension.</li>
    <li>Do not post other people's photos, phone numbers or personal details without their consent. Never identify victims of sexual offences or children in sensitive cases.</li>
    <li>Community posts are checked before they appear and expire automatically. We may remove anything that breaks these rules.</li>
  </ul>

  <h2>Reporters</h2>
  <p>Reporters are independent contributors, not employees. Payment rules, the rate card and your rights over your material are set out in the Contributor Agreement in the <a href="<?= e(PORTAL_URL) ?>">reporter portal</a>. Contributor titles are not press accreditation.</p>

  <h2>Our news</h2>
  <p>We work to be accurate, and when we get something wrong we correct it with a dated note. News is for general information, not professional advice. Content from official sources and agencies is credited to them.</p>

  <h2>Complaints</h2>
  <p>Report a story from the app, or file a complaint on our <a href="/grievance">complaints page</a>. We acknowledge within 24 hours and resolve within 15 days.</p>

  <h2>Changes</h2>
  <p>We may update these terms. The date at the top shows the latest version.</p>
</div></section>
<?php page_end();
