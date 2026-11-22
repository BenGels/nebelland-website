<?php
// Statcaps je Volk und Klasse: array(STR, DEX, INT)
$classes = array('Krieger', 'Schütze', 'Handwerker', 'Magier');
$caps = array(
    'Menschen'   => array(array(130, 100, 70), array(110, 120, 70), array(115, 105, 80), array(90, 85, 125)),
    'Zwerge'     => array(array(140, 90, 70),  array(120, 110, 70), array(125, 95, 80),  array(100, 85, 115)),
    'Lichtelben' => array(array(120, 110, 70), array(100, 130, 70), array(105, 115, 80), array(80, 80, 140)),
    'Nachtelben' => array(array(115, 115, 70), array(95, 135, 70),  array(100, 120, 80), array(75, 90, 135)),
);
?>
<div class="ui-text center">
  <p>Stärke (STR), Geschicklichkeit (DEX) und Intelligenz (INT) stellst du bei der Charaktererstellung frei ein. Wie weit du sie im Spiel steigern kannst, hängt von Volk und Klasse ab.</p>
  <div class="stat-legend">
    <span class="statcap"><b class="s">STR</b></span> Stärke
    <span class="statcap"><b class="d">DEX</b></span> Geschicklichkeit
    <span class="statcap"><b class="i">INT</b></span> Intelligenz
  </div>
</div>

<div class="container" style="max-width:58rem">
  <div class="table-wrap">
    <table class="table">
      <caption class="visually-hidden">Höchstwerte der Attribute nach Volk und Klasse</caption>
      <thead>
        <tr>
          <th scope="col">Volk</th>
          <?php foreach ($classes as $class): ?><th scope="col" class="center"><?php echo e($class); ?></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($caps as $race => $values): ?>
        <tr>
          <th scope="row"><?php echo e($race); ?></th>
          <?php foreach ($values as $v): ?>
          <td class="center"><span class="statcap" aria-label="Stärke <?php echo $v[0]; ?>, Geschicklichkeit <?php echo $v[1]; ?>, Intelligenz <?php echo $v[2]; ?>"><b class="s"><?php echo $v[0]; ?></b><b class="d"><?php echo $v[1]; ?></b><b class="i"><?php echo $v[2]; ?></b></span></td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="muted center">Lichtelben sind die geborenen Magier, Zwerge die stärksten Krieger und Nachtelben die gewandtesten Schützen.</p>
</div>
