<?php
include __DIR__ . '/staedte/_nav.php';
$cities = array(
    'stadt_3bruecken'   => array('Dreibrücken', 'Stadt der Menschen', 'Die größte Stadt der Nebellande liegt im Zentrum der Insel und ist entsprechend dicht besiedelt – Handelsplatz aller Völker.'),
    'stadt_gadarosch'   => array('Gadarosch', 'Binge der Zwerge', 'Tief in den Berg gehauen, unter Tage, genau so, wie Zwerge es am liebsten haben. Hier dreht sich alles um Metall, Gold und Bier.'),
    'stadt_morgenlicht' => array('Morgenlicht', 'Stadt der Lichtelben', 'Aus unzähligen Marmorsteinen erbaut, erstrahlt Morgenlicht im hellen Stein – eine Perle für Gelehrte und Magier.'),
    'stadt_halbmond'    => array('Halbmond', 'Stadt der Nachtelben', 'Verborgen in einem dichten Wald, den keine Straße erreicht. Um einen uralten schwarzen Tempel reihen sich Häuser auf Holzstegen.'),
);
?>
<div class="ui-text center mb-2">
  <p>Vier Städte prägen das Leben auf Nebelland – jede mit eigenem Charakter, eigener Geschichte und eigenem Volk.</p>
</div>
<div class="container">
  <div class="grid">
    <?php foreach ($cities as $citySlug => $city): ?>
    <a class="card" href="<?php echo e(url($citySlug)); ?>">
      <div class="card__kicker"><?php echo e($city[1]); ?></div>
      <h3><?php echo e($city[0]); ?></h3>
      <p><?php echo e($city[2]); ?></p>
      <span class="card__more">Stadt erkunden →</span>
    </a>
    <?php endforeach; ?>
  </div>
  <div class="narrow mt-2 center">
    <p>Auf der Suche nach einem eigenen Zuhause? In der <a href="<?php echo e(url('haus')); ?>">Häuserliste</a> findest du alle Mietpreise und freien Häuser.</p>
  </div>
</div>
