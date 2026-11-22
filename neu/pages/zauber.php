<?php
// Zirkel => array(array(Name, Repräsentationen, Beschreibung, Befehl))
$spells = array(
    1 => array(
        array('Heilende Hände', array('Hermetiker', 'Kleriker', 'Hexe', 'Druide', 'Geode'), 'Der Magier heilt körperlichen Schaden bei sich oder anderen; kleine Wunden schließen sich sofort. Wie viel geheilt wird, hängt von seinem Magieskill ab.', '[heilen'),
        array('Segen', array('Hermetiker', 'Kleriker'), 'Erhöht für kurze Zeit Stärke, Geschicklichkeit oder Intelligenz des Ziels. Welche Eigenschaft profitiert, entscheidet der Zufall; die Höhe des Bonus richtet sich nach dem Können des Magiers.', '[segen'),
        array('Giftbolzen', array('Kleriker', 'Hexe'), 'Ein Geschoss aus reinem Gift fügt dem Opfer körperlichen Schaden zu. Giftresistenzen der Rüstung können ihn mindern.', '[giftbolzen'),
        array('Feuerball', array('Hermetiker', 'Kleriker', 'Hexe', 'Druide', 'Geode'), 'Der Magier schleudert dem Opfer eine Kugel aus gebündelter Feuerkraft entgegen. Feuerresistenzen schwächen den Schaden ab.', '[feuerball'),
        array('Entgiften', array('Kleriker', 'Hexe', 'Druide'), 'Befreit ein vergiftetes Wesen vom Einfluss des Giftes.', '[entgiften'),
        array('Eisblitz', array('Hermetiker', 'Kleriker', 'Druide', 'Geode'), 'Der Magier formt einen Blitz aus eisiger Kälte und schleudert ihn seinem Gegenüber entgegen. Kälteresistenzen schwächen ihn ab.', '[eisblitz'),
    ),
    2 => array(
        array('Verwurzeln', array('Hexe', 'Druide', 'Geode'), 'Wurzeln brechen aus dem Boden und halten das Ziel fest, bis eine gewisse Zeit verstrichen ist oder es Schaden erleidet. Allzu starke Ungetüme können die Ranken allerdings nicht halten.', '[verwurzeln'),
        array('Magische Rüstung', array('Hermetiker', 'Kleriker', 'Geode'), 'Die Haut des Ziels verhärtet sich – je nach Magier auf unterschiedliche Weise, stets aber zum besseren Schutz vor Angriffen.', '[ruestung'),
        array('Lichteffekte', array('Hermetiker', 'Kleriker', 'Hexe', 'Druide', 'Geode'), 'Beschwört ein gleißendes Licht, das an der gewünschten Stelle verharrt und nach einiger Zeit wieder im Nichts verschwindet, aus dem es kam.', '[effekte'),
        array('Unsichtbarkeit', array('Hermetiker'), 'Der Magier wird auf der Stelle unsichtbar. Jeder Schritt kostet ihn in diesem Zustand jedoch deutlich Kraft – ist sie erschöpft, wird er wieder sichtbar.', '[unsichtbarkeit'),
    ),
    3 => array(
        array('Vergiften', array('Kleriker', 'Hexe'), 'Lässt ein gefährliches Gift direkt in den Adern des Opfers entstehen, das es eine ganze Weile plagt, bis sein Körper sich dagegen wehren kann.', '[vergiften'),
        array('Blitz', array('Hermetiker', 'Kleriker', 'Druide'), 'Ein Blitz aus reiner Energie fährt an der gewünschten Stelle vom Himmel herab. Wer getroffen wird, kann nur auf den Energiewiderstand seiner Rüstung hoffen.', '[blitz'),
        array('Kleine Beschwörung', array('Hermetiker', 'Kleriker', 'Hexe', 'Druide', 'Geode'), 'Beschwört je nach Repräsentation ein kleines Wesen, das dem Magier eine Zeit lang zur Seite steht und danach in seine Sphäre zurückkehrt.', '[kleineBeschw'),
    ),
    4 => array(
        array('Feuersturm', array('Hermetiker', 'Kleriker', 'Hexe', 'Druide', 'Geode'), 'Die Luft füllt sich mit unzähligen Flammen, die das Opfer umzingeln und versengen. Nur die Feuerresistenz seiner Rüstung kann es schützen.', '[feuersturm'),
        array('Giftlanze', array('Kleriker', 'Hexe'), 'Eine Lanze aus reinem Gift fügt dem Opfer schweren körperlichen Schaden zu. Giftresistenzen der Rüstung können ihn mindern.', '[giftlanze'),
        array('Eisschlag', array('Hexe', 'Geode'), 'Winzige Eispartikel und Kristalle prasseln auf das Opfer nieder. Die Kälteresistenz seiner Rüstung schützt es ein wenig.', '[eisschlag'),
    ),
    5 => array(
        array('Wirbelwind', array('Hexe', 'Druide', 'Geode'), 'Ein atemberaubender Sturm zieht auf und gipfelt in einer Windhose, deren umherfliegende Trümmer alles verletzen, was ihr zu nahe kommt.', '[wirbelwind'),
        array('Massenheilung', array('Hermetiker', 'Kleriker'), 'Der Magier setzt seine Kraft ein, um alle Gefährten zugleich zu heilen und ihre Wunden zu schließen.', '[massenheilung'),
        array('Gewitter', array('Hermetiker', 'Kleriker'), 'Ein furchterregendes Gewitter zieht auf und lässt in kürzesten Abständen gefährliche Blitze auf die Gegner niederfahren.', '[gewitter'),
    ),
);
$schools = array('Hermetiker', 'Kleriker', 'Hexe', 'Druide', 'Geode');
?>
<div class="ui-text center">
  <p>Alle Zauber der Nebellande, geordnet nach Zirkeln. Jeder Zauber lässt sich über das Magierbuch oder direkt per Befehl wirken. Wähle eine Repräsentation, um nur ihre Zauber zu sehen.</p>
</div>

<div class="filter" data-spell-filter role="group" aria-label="Nach Repräsentation filtern">
  <button type="button" data-school="alle" aria-pressed="true">Alle</button>
  <?php foreach ($schools as $school): ?>
  <button type="button" data-school="<?php echo e(strtolower($school)); ?>" aria-pressed="false"><?php echo e($school); ?></button>
  <?php endforeach; ?>
</div>

<?php foreach ($spells as $circle => $list): ?>
<section class="circle" aria-labelledby="zirkel-<?php echo $circle; ?>">
  <div class="circle-title"><h2 id="zirkel-<?php echo $circle; ?>"><?php echo $circle; ?>. Zirkel</h2></div>
  <div class="container">
    <div class="grid">
      <?php foreach ($list as $spell): ?>
      <article class="spell" data-schools="<?php echo e(strtolower(implode(' ', $spell[1]))); ?>">
        <h3><?php echo e($spell[0]); ?></h3>
        <ul class="spell__schools" aria-label="Repräsentationen">
          <?php foreach ($spell[1] as $school): ?><li class="school-<?php echo e(strtolower($school)); ?>"><?php echo e($school); ?></li><?php endforeach; ?>
        </ul>
        <p><?php echo e($spell[2]); ?></p>
        <div class="spell__cmd">Befehl: <span class="cmd"><?php echo e($spell[3]); ?></span></div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endforeach; ?>
