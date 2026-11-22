<?php
$latestNews = nl_query($NL_CONFIG['db_homepage'], 'SELECT * FROM `news` ORDER BY `NID` DESC LIMIT 4');
$online = players_online();
?>
<div class="home-intro">
  <div class="prose">
    <p class="eyebrow">Willkommen</p>
    <h2 class="mt-0">Eine Welt, geschaffen für Rollenspiel</h2>
    <p class="lead">Nebelland ist ein deutscher Ultima-Online-Shard, der seit 2003 ganz auf seine Spielerinnen und Spieler ausgerichtet ist.</p>
    <p>Karte, Skripte, Gegenstände und Texte stammen aus unserer eigenen Feder, und die gesamte Welt spricht Deutsch. Vier Völker mit eigener Geschichte, drei Klassen mit zahlreichen Ausprägungen und vier Städte voller Leben warten darauf, von euch erkundet zu werden.</p>
    <p>Was uns am wichtigsten ist? Der Spaß am Spiel, ein freundliches Miteinander und Rollenspiel, das diesen Namen verdient.</p>
    <p><a href="<?php echo e(url('nebelland')); ?>">Mehr über Nebelland erfahren →</a></p>
  </div>

  <aside class="status-widget" aria-labelledby="status-titel">
    <h2 id="status-titel">Serverstatus</h2>
    <div class="status-widget__count">
      <?php if ($online === null): ?>
        <span class="pulse pulse--off" aria-hidden="true"></span><span class="muted">Status derzeit unbekannt</span>
      <?php else: ?>
        <span class="pulse<?php echo $online ? '' : ' pulse--off'; ?>" aria-hidden="true"></span><strong><?php echo (int) $online; ?></strong><span>Abenteurer online</span>
      <?php endif; ?>
    </div>
    <ul>
      <li><span>Spielwelt</span><span>Nebelland</span></li>
      <li><span>Sprache</span><span>Deutsch</span></li>
      <li><span>Schwerpunkt</span><span>Rollenspiel</span></li>
      <li><span>IRC</span><span><a href="<?php echo e($NL_CONFIG['irc_url']); ?>"><?php echo e($NL_CONFIG['irc_channel']); ?></a></span></li>
    </ul>
    <a class="btn btn--ghost btn--small" href="<?php echo e(url('status')); ?>">Wer ist gerade unterwegs?</a>
  </aside>
</div>

<section class="section" aria-labelledby="einstieg">
  <div class="section-title"><h2 id="einstieg">Dein Weg in die Nebellande</h2></div>
  <div class="grid">
    <a class="card" href="<?php echo e(url('anmeldung')); ?>">
      <div class="card__kicker">Schritt 1</div>
      <h3>Anmelden</h3>
      <p>Bei uns gibt es eine persönliche Anmeldung: Ein kurzes Gespräch mit dem Team, dann erhältst du deine Zugangsdaten.</p>
      <span class="card__more">Zur Anmeldung →</span>
    </a>
    <a class="card" href="<?php echo e(url('1schritte')); ?>">
      <div class="card__kicker">Schritt 2</div>
      <h3>Erste Schritte</h3>
      <p>Client einrichten, Charakter erschaffen, Volk und Klasse wählen – unser Leitfaden begleitet dich durch die ersten Stunden.</p>
      <span class="card__more">Zum Leitfaden →</span>
    </a>
    <a class="card" href="<?php echo e(url('rassen')); ?>">
      <div class="card__kicker">Schritt 3</div>
      <h3>Dein Volk finden</h3>
      <p>Menschen, Zwerge, Lichtelben oder Nachtelben? Lerne die Völker kennen, bevor du dich entscheidest.</p>
      <span class="card__more">Zu den Völkern →</span>
    </a>
    <a class="card" href="<?php echo e(url('gesch')); ?>">
      <div class="card__kicker">Lore</div>
      <h3>Die Weltgeschichte</h3>
      <p>Von den Lingarden über den Blutregen bis zur Schlacht am Pan’Torra: die Bücher des Katarian.</p>
      <span class="card__more">Weiterlesen →</span>
    </a>
  </div>
</section>

<section class="section" aria-labelledby="neuigkeiten">
  <div class="section-title">
    <h2 id="neuigkeiten">Neuigkeiten</h2>
    <a href="<?php echo e(url('news')); ?>">Alle Neuigkeiten →</a>
  </div>
  <?php if ($latestNews === false): ?>
    <?php echo db_unavailable_notice('Der Newsbereich'); ?>
  <?php elseif (!$latestNews): ?>
    <p class="muted">Noch keine Neuigkeiten vorhanden.</p>
  <?php else: ?>
    <div class="grid grid--2">
      <?php foreach ($latestNews as $row) { echo render_news_item($row); } ?>
    </div>
  <?php endif; ?>
</section>
