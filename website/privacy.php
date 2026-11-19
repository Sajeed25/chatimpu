<?php
// Plain-language starting point. Have it reviewed by a lawyer (DPDP Act 2023, IT Rules 2021) before launch.
require __DIR__ . '/_lib.php';
page_start(['title' => 'గోప్యతా విధానం · Privacy policy · Chatimpu']);
?>
<section class="section"><div class="wrap narrow prose">
  <p class="eyebrow"><?= t('గోప్యత', 'Privacy') ?></p>
  <h1 class="h2"><?= t('గోప్యతా విధానం', 'Privacy policy') ?></h1>
  <p class="muted small">Last updated: 19 November 2026</p>

  <div class="summary te" lang="te">
    <p><b>క్లుప్తంగా:</b> వార్తలను మీకు అనుగుణంగా చూపడానికి, రిపోర్టర్లకు చెల్లించడానికి, చట్టపరమైన బాధ్యతల కోసం మాత్రమే మీ సమాచారం వాడుతాం. మీ డేటాను అమ్మం. మీ ఖాతా, డేటా తొలగించమని ఎప్పుడైనా అడగవచ్చు.</p>
  </div>

  <h2>What we collect</h2>
  <ul>
    <li><b>Readers:</b> your chosen language, district / mandal and interests, and what you read, so we can show local news first. If you sign in, your mobile number. If you allow notifications, a device token to send them.</li>
    <li><b>Reporters:</b> name, mobile number, optional email, area, profile photo, a masked identity document (only the last 4 characters are kept after review) and payout details (UPI ID or bank account, stored encrypted). If tax applies, your PAN (encrypted, shown only masked).</li>
    <li><b>Complaints and community posts:</b> what you write, and the contact details you give us so we can reply.</li>
  </ul>

  <h2>How we use it</h2>
  <ul>
    <li>To show news from your area and in your language.</li>
    <li>To verify reporters, review their stories and pay them, including tax deduction where the law requires.</li>
    <li>To answer complaints and keep the service safe (for example, stopping spam and fake accounts).</li>
  </ul>
  <p>We do not sell your personal data. Reporters' source notes and identity documents are seen only by editors and are never published.</p>

  <h2>Who we share it with</h2>
  <ul>
    <li><b>Service providers</b> that run Chatimpu for us: hosting and database (Supabase), SMS for login codes, push notifications (Expo / Google Firebase), payments to reporters (our payout provider and banks), and an AI service that helps editors summarise text.</li>
    <li><b>Authorities</b> when the law requires it.</li>
  </ul>

  <h2>How long we keep it</h2>
  <ul>
    <li>ID document images: deleted 30 days after the verification decision.</li>
    <li>Unsent drafts: deleted after 90 days without changes (with a warning first).</li>
    <li>Notifications: read ones after 180 days, unread after 360 days.</li>
    <li>Earnings, payments and tax records: as long as tax law requires.</li>
  </ul>

  <h2>Your rights</h2>
  <p>You can ask to see, correct or delete your data, or withdraw consent, by writing to <a href="mailto:<?= e(EMAIL_SUPPORT) ?>"><?= e(EMAIL_SUPPORT) ?></a>. Some records (such as payments and tax) must be kept by law even after you delete your account. If you are unhappy with our answer, contact our <a href="/grievance">grievance officer</a>.</p>

  <h2>Children</h2>
  <p>You must be 18 or older to become a reporter. Readers under 18 should use the app with a parent's permission.</p>
</div></section>
<?php page_end();
