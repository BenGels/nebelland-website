<?php
// Positionen der Spieler aus der RunUO-Statustabelle. Umrechnung wie im alten
// bild.php: Spielkoordinate / 3.16 = Pixel auf der 510 × 326 px großen Inselkarte.
$players = nl_query($NL_CONFIG['db_runuo'], 'SELECT `X`, `Y`, `Hidden` FROM `status`');
$visible = array();
if ($players) {
    foreach ($players as $p) {
        $x = $p['X'] / 3.16;
        $y = $p['Y'] / 3.16;
        if ((int) $p['Hidden'] === 0 && $x < 510 && $y < 326) {
            $visible[] = array(round($x / 510 * 100, 2), round($y / 326 * 100, 2));
        }
    }
}
$total = $players === false ? null : count($players);
?>
<div class="stat-tiles">
  <div class="stat-tile">
    <span>Spieler online</span>
    <strong><?php echo $total === null ? '–' : $total; ?></strong>
  </div>
  <div class="stat-tile">
    <span>Auf der Karte sichtbar</span>
    <strong><?php echo $total === null ? '–' : count($visible); ?></strong>
  </div>
  <div class="stat-tile">
    <span>Stand</span>
    <strong style="font-size:1.4rem"><?php echo date('d.m.Y, H:i'); ?> Uhr</strong>
  </div>
</div>

<?php if ($total === null): ?>
  <div class="narrow"><?php echo db_unavailable_notice('Der Serverstatus'); ?></div>
<?php endif; ?>

<figure class="live-map">
  <img src="assets/img/karte-insel.jpg" alt="Karte der Nebellande mit den aktuellen Aufenthaltsorten der Spieler" width="510" height="326">
  <?php foreach ($visible as $pos): ?>
  <span class="live-map__dot" style="left:<?php echo $pos[0]; ?>%;top:<?php echo $pos[1]; ?>%"></span>
  <?php endforeach; ?>
</figure>
<p class="center muted">Jeder rote Punkt ist ein Abenteurer, der gerade unterwegs ist. Versteckte Charaktere werden nicht angezeigt.</p>

<div class="narrow mt-2 center">
  <p>Wer hat die meiste Zeit in den Nebellanden verbracht? Das verrät der <a href="<?php echo e(url('ewiger')); ?>">Ewige Status</a>.</p>
</div>
