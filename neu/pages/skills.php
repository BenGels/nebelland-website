<?php
$levels = array(110, 100, 75, 50, 25);

// Skillgruppe => Liste der Skills
$groups = array(
    'Kampf'        => array('Verteidigung', 'Taktik', 'Bogenschießen', 'Armbrustkampf', 'Klingenkampf', 'Keulenkampf', 'Axtkampf', 'Faustkampf'),
    'Körperliches' => array('Verstecken', 'Schlossknacken', 'Magieresistenz', 'Spionieren', 'Vergiften', 'Musizieren', 'Stehlen', 'Schleichen', 'Fallen entschärfen'),
    'Handwerk'     => array('Alchemie', 'Steinmetz', 'Feinschmied', 'Rüstschmied', 'Waffenschmied', 'Schnitzerei', 'Schreinerei', 'Schneidern', 'Gärtnern', 'Kartografie', 'Kochen', 'Künstler', 'Architekt'),
    'Erträge'      => array('Prospektor', 'Holzfällen', 'Bergbau', 'Fischen'),
    'Wissen'       => array('Anatomie', 'Heilen', 'Pfadfinden', 'Leichenkunde', 'Intelligenz beurteilen', 'Inschriften', 'Geisterkunde', 'Magie', 'Meditation', 'Pflanzenkunde', 'Tierwissen', 'Tierzähmung', 'Tierheilkunde', 'Hirten', 'Beruhigen', 'Enttarnen'),
    'Konzentration' => array('Konzentration'),
);

// Klasse => Gruppe => Anzahl Skills je Cap (110, 100, 75, 50, 25)
$caps = array(
    'Krieger' => array(
        'Kampf' => array(1, 1, 1, 2, 3), 'Körperliches' => array(0, 2, 1, 2, 2), 'Handwerk' => array(0, 0, 0, 2, 5),
        'Erträge' => array(0, 1, 1, 2, 0), 'Wissen' => array(0, 1, 3, 5, 3), 'Konzentration' => array(0, 1, 0, 0, 0),
    ),
    'Handwerker' => array(
        'Kampf' => array(0, 0, 0, 3, 2), 'Körperliches' => array(0, 1, 4, 2, 0), 'Handwerk' => array(1, 1, 1, 1, 4),
        'Erträge' => array(0, 2, 1, 1, 0), 'Wissen' => array(0, 2, 0, 7, 3), 'Konzentration' => array(0, 1, 0, 0, 0),
    ),
    'Magier' => array(
        'Kampf' => array(0, 0, 0, 3, 1), 'Körperliches' => array(0, 2, 1, 4, 2), 'Handwerk' => array(0, 0, 0, 2, 5),
        'Erträge' => array(0, 0, 0, 1, 3), 'Wissen' => array(1, 4, 5, 2, 2), 'Konzentration' => array(0, 1, 0, 0, 0),
    ),
);
?>
<div class="ui-text center">
  <p>Auf Nebelland kann niemand alles meistern. Je nach Klasse darfst du aus jeder Skillgruppe nur eine bestimmte Anzahl an Fähigkeiten bis zu einer bestimmten Obergrenze (Cap) steigern.</p>
  <p class="muted">Lesebeispiel: Ein Krieger kann <strong>einen</strong> Kampfskill auf 110 bringen, einen weiteren auf 100, einen auf 75, zwei auf 50 und drei auf 25.</p>
</div>

<div class="container">
  <div class="grid grid--2">
    <?php foreach ($caps as $class => $rows): ?>
    <section>
      <h2 class="mt-0" style="font-size:1.3rem"><?php echo e($class); ?></h2>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th scope="col">Gruppe</th><?php foreach ($levels as $level): ?><th scope="col" class="num"><?php echo $level; ?></th><?php endforeach; ?></tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $group => $counts): ?>
            <tr>
              <th scope="row"><?php echo e($group); ?></th>
              <?php foreach ($counts as $count): ?>
              <td class="num"><?php echo $count ? $count : '<span class="muted">–</span>'; ?></td>
              <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
    <?php endforeach; ?>
  </div>

  <div class="narrow mt-2">
    <h2>Welche Skills gehören wozu?</h2>
    <dl class="key-facts">
      <?php foreach ($groups as $group => $skills): ?>
      <div><dt><?php echo e($group); ?></dt><dd><?php echo e(implode(', ', $skills)); ?></dd></div>
      <?php endforeach; ?>
    </dl>
  </div>
</div>
