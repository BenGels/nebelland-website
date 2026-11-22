<?php
// Name => array(Aufgaben, E-Mail, Porträt)
$staff = array(
    'Acarno'   => array(array('Quests', 'Support'), 'acarno@nebelland.com', ''),
    'Croaker'  => array(array('Grafik', 'Bau', 'Quests'), '', 'croaker.jpg'),
    'Dave'     => array(array('Support', 'Patcher', 'Grafik'), 'dave@nebelland.com', 'dave.gif'),
    'Draven'   => array(array('Skripte', 'Gameserver'), 'draven@nebelland.com', ''),
    'Larina'   => array(array('Bau', 'Quests', 'Support', 'Magie'), 'larina@nebelland.com', ''),
    'Mehlwurm' => array(array('Skripte', 'Webserver'), 'mehlwurm@nebelland.com', ''),
    'Raziel'   => array(array('Quests', 'Support'), 'raziel@nebelland.com', 'raziel.jpg'),
);
$raceGms = array(
    'Menschen'   => 'Acarno',
    'Zwerge'     => 'Larina',
    'Lichtelben' => 'Larina',
    'Nachtelben' => 'Croaker',
);
?>
<div class="ui-text center mb-2">
  <p>Hinter Nebelland steht ein kleines Team aus Freiwilligen, das in seiner Freizeit baut, skriptet, Quests leitet und euch bei Fragen zur Seite steht.</p>
</div>

<div class="container">
  <div class="grid">
    <?php foreach ($staff as $name => $info): ?>
    <article class="card staff-card">
      <?php if ($info[2]): ?>
        <img class="staff-card__portrait" src="assets/img/staff/<?php echo e($info[2]); ?>" alt="Porträt von <?php echo e($name); ?>" loading="lazy">
      <?php else: ?>
        <div class="staff-card__initial" aria-hidden="true"><?php echo e(substr($name, 0, 1)); ?></div>
      <?php endif; ?>
      <h3><?php echo e($name); ?></h3>
      <ul class="tags">
        <?php foreach ($info[0] as $task): ?><li><?php echo e($task); ?></li><?php endforeach; ?>
      </ul>
      <?php if ($info[1]): ?>
        <span class="card__more"><a href="mailto:<?php echo e($info[1]); ?>"><?php echo e($info[1]); ?></a></span>
      <?php endif; ?>
    </article>
    <?php endforeach; ?>
  </div>

  <div class="narrow mt-2">
    <h2>Ansprechpartner der Völker</h2>
    <p class="muted">Für alle Fragen rund um das Rollenspiel eines Volkes ist jeweils ein eigener GM zuständig.</p>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th scope="col">Volk</th><th scope="col">Zuständig</th></tr></thead>
        <tbody>
          <?php foreach ($raceGms as $race => $gm): ?>
          <tr><th scope="row"><?php echo e($race); ?></th><td><?php echo e($gm); ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
