<?php
// Region => Präfix der Hausbezeichnung in hp.hauser
$regions = array(
    'Umland'      => 'Umland',
    'Dreibrücken' => 'Dreibrücken',
    'Gadarosch'   => 'Gadarosch',
    'Morgenlicht' => 'Morgenlicht',
    'Halbmond'    => 'Halbmond',
);
$region = get_param('Region', 'Umland');
if ($region === 'Morgenlich') {
    $region = 'Morgenlicht'; // alter Link der früheren Seite
}
if (!isset($regions[$region])) {
    $region = 'Umland';
}

$rows = nl_query($NL_CONFIG['db_homepage'],
    "SELECT `Haus`, `Preis`, `Vermietet` FROM `hauser` WHERE `Haus` LIKE '" . nl_escape($regions[$region]) . "%' ORDER BY `HID`");
$free = 0;
if ($rows) {
    foreach ($rows as $row) {
        if (!(int) $row['Vermietet']) {
            $free++;
        }
    }
}
?>
<div class="ui-text center">
  <p>Alle Häuser der Nebellande mit ihrer Tagesmiete. Hast du dein Traumhaus gefunden, ruf per Page einen GM, der es dir übergibt. Als Kaution fallen zwei Wochenmieten an. Mehr dazu in den <a href="<?php echo e(url('faq')); ?>">häufigen Fragen</a>.</p>
</div>

<nav class="subnav" aria-label="Regionen">
  <ul>
    <?php foreach ($regions as $key => $prefix): ?>
    <li><a href="<?php echo e(url('haus', array('Region' => $key))); ?>"<?php echo $key === $region ? ' aria-current="page"' : ''; ?>><?php echo e($key); ?></a></li>
    <?php endforeach; ?>
  </ul>
</nav>

<div class="narrow">
  <?php if ($rows === false): ?>
    <?php echo db_unavailable_notice('Die Häuserliste'); ?>
  <?php elseif (!$rows): ?>
    <p class="center muted">In dieser Region sind keine Häuser verzeichnet.</p>
  <?php else: ?>
    <p class="muted"><?php echo count($rows); ?> Häuser in <?php echo e($region); ?>, davon <strong style="color:var(--success)"><?php echo $free; ?> frei</strong>.</p>
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr><th scope="col">Haus</th><th scope="col" class="num">Miete pro Tag</th><th scope="col" class="num">Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row):
              $number = trim(substr($row['Haus'], strlen($regions[$region])));
          ?>
          <tr>
            <th scope="row"><?php echo e($region); ?> · Nr. <?php echo e($number); ?></th>
            <td class="num"><?php echo (int) $row['Preis']; ?> Dukaten</td>
            <td class="num"><?php echo (int) $row['Vermietet'] ? '<span class="badge badge--taken">vermietet</span>' : '<span class="badge badge--free">frei</span>'; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
