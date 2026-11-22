<?php include __DIR__ . '/klassen/_nav.php'; ?>
<div class="ui-text center mb-2">
  <p>Im Kern gibt es auf Nebelland drei Sparten: <strong>Krieger</strong>, <strong>Handwerker</strong> und <strong>Magier</strong>.</p>
  <p>Niemand ist dabei in ein starres Korsett gezwängt. Ein Schmied darf durchaus mit der eigenen Waffe auf die Jagd gehen, ein Magier einen Trank mischen und ein Schneider seltene Leder selbst erbeuten – verwandte Tätigkeiten gelingen auch außerhalb der eigenen Klasse, ohne dass man gleich scheitert.</p>
  <p>Alleskönner gibt es dagegen nicht: Ein Krieger wird für eine gute Rüstung immer einen Schmied brauchen, ein Magier für einen starken Trank einen Alchemisten. Gerade das macht das Miteinander aus.</p>
</div>

<div class="container">
  <div class="grid">
    <a class="card" href="<?php echo e(url('klasse_krieger')); ?>">
      <div class="card__kicker">Nahkampf &amp; Fernkampf</div>
      <h3>Krieger</h3>
      <p>Söldner, Ehrenkrieger, Waldläufer, Nebeljäger und Meuchelmörder – Kämpfer mit Klinge, Axt, Keule, Bogen oder Armbrust.</p>
      <span class="card__more">Zu den Kriegern →</span>
    </a>
    <a class="card" href="<?php echo e(url('klasse_handwerker')); ?>">
      <div class="card__kicker">Schmiede, Schneider &amp; Co.</div>
      <h3>Handwerker</h3>
      <p>Ohne sie keine Rüstung, kein Möbelstück und kein Trank. Handwerker sind das Rückgrat der Wirtschaft Nebellands.</p>
      <span class="card__more">Zu den Handwerkern →</span>
    </a>
    <a class="card" href="<?php echo e(url('klasse_magier')); ?>">
      <div class="card__kicker">Die Gabe der Geister</div>
      <h3>Magier</h3>
      <p>Kleriker, Hermetiker, Druiden, Hexen und Geoden – jede Repräsentation versteht die Magie auf ihre eigene Weise.</p>
      <span class="card__more">Zu den Magiern →</span>
    </a>
  </div>
</div>
