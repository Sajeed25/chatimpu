<?php
require __DIR__ . '/_lib.php';

// Latest published stories (cached for 2 minutes). Empty when Supabase isn't configured yet: the section is hidden.
[$st, $latest] = sb('GET', 'stories?status=eq.published&order=published_at.desc&limit=6'
  . '&select=id,image_path,published_at,category_id,districts(name,name_te),story_translations(lang,headline,summary)', null, 120);
$latest = $st === 200 && is_array($latest) ? array_values(array_filter($latest, fn($s) => !empty($s['story_translations']))) : [];

page_start();
?>
<section class="hero">
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <p class="kicker">YOUR LOCALITY. YOUR VOICE. YOUR NEWS.</p>
      <h1><?= t('మీ ఊరి వార్తలు,<br><em>60 పదాల్లో.</em>', 'Your town\'s news,<br><em>in 60 words.</em>') ?></h1>
      <p class="lead"><?= t(
        'మీ మండలం, మీ జిల్లాలో ఏం జరుగుతోందో, చిన్న చిన్న కార్డుల్లో తెలుగులో. పైకి స్వైప్ చేస్తే తదుపరి వార్త. మీ ఊరి రిపోర్టర్లు పంపిన, ఎడిటర్లు నిర్ధారించిన వార్తలు.',
        'What\'s happening in your mandal and district, in short swipeable cards. Reported by local people, checked by editors, in Telugu and English.') ?></p>
      <div class="hero-ctas">
        <a class="btn btn-red" href="#download"><?= icon('download') . t('యాప్ పొందండి', 'Get the app') ?></a>
        <a class="btn btn-ghost" href="#reporters"><?= icon('pen') . t('రిపోర్టర్‌గా చేరండి', 'Become a reporter') ?></a>
      </div>
      <ul class="chips" aria-label="<?= e('Highlights') ?>">
        <li><?= t('ఉచితం', 'Free') ?></li><li><?= t('తెలుగు · English', 'Telugu · English') ?></li><li><?= t('తెలంగాణ · ఆంధ్రప్రదేశ్', 'Telangana · Andhra Pradesh') ?></li>
      </ul>
    </div>

    <div class="hero-art" aria-hidden="true">
      <div class="phone">
        <div class="phone-screen">
          <div class="card-img">
            <span class="pill pill-red">బ్రేకింగ్</span>
            <svg viewBox="0 0 300 170" class="rain"><defs><linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3B4A8F"/><stop offset="1" stop-color="#7C8BC9"/></linearGradient></defs>
              <rect width="300" height="170" fill="url(#sky)"/>
              <g fill="#E8ECFA"><ellipse cx="110" cy="62" rx="52" ry="26"/><ellipse cx="160" cy="52" rx="44" ry="30"/><ellipse cx="196" cy="68" rx="38" ry="22"/></g>
              <g stroke="#E8ECFA" stroke-width="3" stroke-linecap="round" opacity=".85"><line x1="100" y1="100" x2="92" y2="122"/><line x1="130" y1="104" x2="122" y2="126"/><line x1="160" y1="98" x2="152" y2="120"/><line x1="190" y1="104" x2="182" y2="126"/><line x1="115" y1="132" x2="107" y2="154"/><line x1="175" y1="134" x2="167" y2="156"/></g>
              <rect y="150" width="300" height="20" fill="#2B356B"/>
            </svg>
          </div>
          <div class="card-body">
            <p class="card-meta"><b>Chatimpu Desk</b> · బంజారాహిల్స్, హైదరాబాద్ · 12 నిమి.</p>
            <p class="card-head">హైదరాబాద్‌కు ఆరెంజ్ అలర్ట్: రాత్రి వరకు భారీ వర్షాలు</p>
            <span class="card-rule"></span>
            <p class="card-text">ఖైరతాబాద్, బంజారాహిల్స్, అమీర్‌పేట ప్రాంతాల్లో భారీ వర్షపాతం నమోదయ్యే అవకాశం ఉందని వాతావరణ శాఖ తెలిపింది. లోతట్టు కాలనీల ప్రజలు జాగ్రత్తగా ఉండాలని సూచించింది.</p>
            <div class="card-actions"><span>🎧 వినండి</span><span>👍 1.2K</span><span class="wa"><?= icon('chat') ?></span></div>
          </div>
        </div>
      </div>
      <div class="status-card"><span class="status-logo">చాటింపు</span><span class="status-kind">శుభోదయం</span><span class="status-text">కొత్త రోజు, కొత్త అవకాశం. చిరునవ్వుతో మొదలుపెట్టండి.</span><span class="status-url">www.chatimpu.com</span></div>
    </div>
  </div>
</section>

<section id="features" class="section">
  <div class="wrap">
    <p class="eyebrow"><?= t('యాప్‌లో ఏముంది', 'What\'s inside') ?></p>
    <h2 class="h2"><?= t('మీ ఊరికి కావాల్సినవన్నీ ఒకే చోట', 'Everything your town needs, in one place') ?></h2>
    <div class="features">
      <?php
      $f = [
        ['pin', 'మీ మండలం వార్తలు ముందుగా', 'Your mandal first', 'మీ ప్రాంతం ఎంచుకోండి. దగ్గరి వార్తలు ముందు, మ్యాప్‌లో కూడా.', 'Pick your area and see the closest news first, on a list or a map.'],
        ['reel', 'న్యూస్ రీల్స్', 'News Reels', 'ముఖ్యమైన వార్తలు చిన్న నిలువు వీడియోల్లో, శీర్షికతో సహా.', 'Short vertical news videos with the headline on screen.'],
        ['chat', 'రోజూ వాట్సాప్ స్టేటస్', 'Daily WhatsApp status', 'శుభోదయం, సామెతలు, భక్తి కార్డులు. ఒక్క ట్యాప్‌తో షేర్.', 'Good-morning wishes, proverbs and devotional cards to share in one tap.'],
        ['sun', 'ఉదయం, సాయంత్రం ఎడిషన్లు', 'Morning & evening editions', 'ఉదయం 6, సాయంత్రం 6 గంటలకు తాజా స్థానిక వార్తలు.', 'Fresh local news every day at 6 am and 6 pm.'],
        ['phone', 'హెల్ప్‌లైన్లు, ఈ రోజు ధరలు', 'Helplines & today\'s prices', '112, 108 వంటి నంబర్లకు ఒక్క ట్యాప్‌తో కాల్. బంగారం, కూరగాయల ధరలు.', 'Tap-to-call emergency numbers, plus gold, fuel and vegetable prices.'],
        ['people', 'కమ్యూనిటీ బోర్డు', 'Community board', 'ఉద్యోగాలు, శుభాకాంక్షలు, శ్రద్ధాంజలి, అమ్మకాలు. ప్రతి పోస్ట్ తనిఖీ చేశాకే.', 'Jobs, wishes, obituaries and buy & sell, every post checked first.'],
      ];
      foreach ($f as [$ic, $hte, $hen, $pte, $pen]): ?>
        <article class="feature">
          <span class="feature-ic"><?= icon($ic) ?></span>
          <h3><?= t($hte, $hen) ?></h3>
          <p><?= t($pte, $pen) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if ($latest): ?>
<section class="section section-alt">
  <div class="wrap">
    <p class="eyebrow"><?= t('ఇప్పుడే', 'Right now') ?></p>
    <h2 class="h2"><?= t('తాజా వార్తలు', 'Latest stories') ?></h2>
    <div class="stories">
      <?php foreach ($latest as $s): $tr = pick_lang($s['story_translations']); $img = media_url($s['image_path'] ?? ''); ?>
        <a class="story" href="/s/<?= e(rawurlencode($s['id'])) ?>">
          <?php if ($img): ?><img src="<?= e($img) ?>" alt="" loading="lazy" onerror="this.style.visibility='hidden'"><?php endif; ?>
          <span class="story-meta"><?= e($s['districts']['name_te'] ?? $s['districts']['name'] ?? '') ?> · <?= ago($s['published_at'] ?? null) ?></span>
          <span class="story-head" lang="<?= e($tr['lang']) ?>"><?= e($tr['headline']) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section id="reporters" class="section band">
  <div class="wrap band-grid">
    <div>
      <p class="eyebrow eyebrow-light"><?= t('చాటింపు రిపోర్టర్', 'Chatimpu reporters') ?></p>
      <h2 class="h2 h2-light"><?= t('మీ ఊరి వార్త మీరే చెప్పండి. సంపాదించండి.', 'Report your town. Get paid.') ?></h2>
      <p class="band-lead"><?= t(
        'ఎడిటర్ ఆమోదించిన ప్రతి వార్తకు రేట్ కార్డ్ ప్రకారం డబ్బు మీ వాలెట్‌లో. కనీస మొత్తం చేరగానే UPI లేదా బ్యాంక్‌కు తీసుకోండి.',
        'Every story an editor approves earns money at the published rate card. Withdraw to UPI or your bank once you reach the minimum.') ?></p>
      <ol class="steps">
        <li><span>1</span><div><b><?= t('నమోదు చేసుకోండి', 'Sign up') ?></b><?= t('మొబైల్ నంబర్, మీ ప్రాంతం, గుర్తింపు పత్రం.', 'Mobile number, your area and an ID document.') ?></div></li>
        <li><span>2</span><div><b><?= t('రిపోర్టర్ అకాడమీ', 'Reporter Academy') ?></b><?= t('5 చిన్న పాఠాలు: రాయడం, నిర్ధారించడం, ఫోటోలు, చట్టం, భద్రత.', '5 short lessons: writing, fact-checking, photos, the law and safety.') ?></div></li>
        <li><span>3</span><div><b><?= t('వార్తలు పంపండి', 'Send news') ?></b><?= t('ఫోటో, వీడియోతో. ప్రతి వార్తకు ట్రాకింగ్ కోడ్, ఎడిటర్ నిర్ణయం మీకు తెలుస్తుంది.', 'With photos or video. Every story gets a tracking code and you see the editor\'s decision.') ?></div></li>
      </ol>
      <div class="hero-ctas">
        <a class="btn btn-white" href="<?= e(PORTAL_URL) ?>"><?= t('రిపోర్టర్‌గా చేరండి', 'Join as a reporter') . icon('arrow') ?></a>
        <a class="btn btn-ghost-light" href="mailto:<?= e(EMAIL_REPORTERS) ?>"><?= e(EMAIL_REPORTERS) ?></a>
      </div>
    </div>
    <ul class="promises">
      <li><?= icon('wallet') ?><div><b><?= t('పారదర్శక వాలెట్', 'An open wallet') ?></b><?= t('ప్రతి రూపాయికి లెక్క: సంపాదన, ఉపసంహరణ, UTR నంబర్, TDS సర్టిఫికెట్.', 'Every rupee accounted for: earnings, withdrawals, bank UTR numbers and TDS certificates.') ?></div></li>
      <li><?= icon('shield') ?><div><b><?= t('మీ సోర్సెస్ సురక్షితం', 'Your sources stay private') ?></b><?= t('ఆధారాలు, గుర్తింపు పత్రాలు ఎడిటర్లకు మాత్రమే. ఎప్పుడూ ప్రచురించం.', 'Source notes and ID documents are seen only by editors and never published.') ?></div></li>
      <li><?= icon('flag') ?><div><b><?= t('ముందు వచ్చిన వార్తకే గుర్తింపు', 'First report wins') ?></b><?= t('ఒకే సంఘటనను ఇద్దరు పంపితే, ముందు పంపినవారికే క్రెడిట్.', 'If two people send the same incident, the one who sent it first gets the credit.') ?></div></li>
    </ul>
  </div>
</section>

<section class="section">
  <div class="wrap trust">
    <div>
      <p class="eyebrow"><?= t('నమ్మకం', 'Trust') ?></p>
      <h2 class="h2"><?= t('తప్పు జరిగితే, బహిరంగంగా సరిదిద్దుతాం', 'When we get it wrong, we correct it in public') ?></h2>
      <p class="lead"><?= t(
        'ప్రతి వార్తలో "ఫిర్యాదు" బటన్ ఉంటుంది. సవరిస్తే, తేదీతో కూడిన సవరణ నోట్ వార్తలోనే కనిపిస్తుంది. అధికారిక ఫిర్యాదులకు 24 గంటల్లో స్పందన, 15 రోజుల్లో పరిష్కారం.',
        'Every story has a "Report" button. When we correct a story, a dated correction note appears on it. Formal complaints are acknowledged within 24 hours and resolved within 15 days.') ?></p>
      <a class="btn btn-ghost" href="/grievance"><?= icon('flag') . t('ఫిర్యాదు చేయండి', 'File a complaint') ?></a>
    </div>
  </div>
</section>

<section id="download" class="section download">
  <div class="wrap download-box">
    <img src="/assets/emblem-192.png" width="96" height="96" alt="">
    <div>
      <h2 class="h2"><?= t('చాటింపు యాప్ పొందండి', 'Get the Chatimpu app') ?></h2>
      <p class="lead"><?= t('Android ఫోన్లకు. ఉచితం. తక్కువ డేటా వాడుతుంది.', 'For Android phones. Free, and light on data.') ?></p>
      <?= store_buttons() ?>
    </div>
  </div>
</section>
<?php page_end();
