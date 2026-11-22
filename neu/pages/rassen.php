<?php
$races = array(
    'rasse_mensch'   => array('Menschen', 'Svennas Kinder', 'Geformt aus der Glut des Pan’Torra und dem Wasser des Nebelgebirges: vielseitig, wandelbar und so verschieden wie ihre Kleidung.'),
    'rasse_zwerg'    => array('Zwerge', 'Zinkhals Erben', 'Stur, ungestüm und unerschütterlich treu. Wer das Vertrauen eines Zwerges gewinnt, hat einen Freund fürs Leben.'),
    'rasse_lichtelb' => array('Lichtelben', 'Inares’ Auserwählte', 'Grazil, langlebig und der Gemeinschaft verpflichtet. Hinter ihrer Gelassenheit verbergen sie tiefe Gefühle.'),
    'rasse_nachtelb' => array('Nachtelben', 'Die Verstoßenen', 'Blauhäutig, stolz und geheimnisvoll. Einst verbannt, wurden sie in der Schlacht am Pan’Torra zu Rettern der Völker.'),
);
?>
<div class="ui-text center mb-2">
  <p>Auf Nebelland gibt es vier spielbare Völker. Keines ist an eine feste Gesinnung gebunden, doch jedes zeigt typische Eigenheiten und Verhaltensweisen. Ausnahmen sind natürlich möglich – sie sollten aber Einzelfälle bleiben.</p>
  <div class="notice notice--gold" style="text-align:left"><strong>Wichtig:</strong> Nachtelben sind keine Drow! Bitte lies dir ihre Beschreibung aufmerksam durch.</div>
</div>

<div class="container">
  <div class="grid">
    <?php foreach ($races as $slug => $race): ?>
    <a class="card" href="<?php echo e(url($slug)); ?>">
      <div class="card__kicker"><?php echo e($race[1]); ?></div>
      <h3><?php echo e($race[0]); ?></h3>
      <p><?php echo e($race[2]); ?></p>
      <span class="card__more">Mehr erfahren →</span>
    </a>
    <?php endforeach; ?>
  </div>

  <div class="narrow mt-2">
    <h2>Welche Werte erreicht mein Volk?</h2>
    <p>Jedes Volk hat je nach Klasse eigene Höchstwerte für Stärke, Geschicklichkeit und Intelligenz. Eine Übersicht findest du unter <a href="<?php echo e(url('stats')); ?>">Attribute</a>.</p>
  </div>
</div>
