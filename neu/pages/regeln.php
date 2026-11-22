<?php
// Regelwerk: Abschnitt => array(Titel, array(array(Nummer, Überschrift, Text, Unterpunkt?)))
$rules = array(
    1 => array('Account', array(
        array('1.1', 'Verantwortung', 'Jeder Spieler ist für seinen Account voll verantwortlich. Zugangsdaten dürfen nicht an Dritte weitergegeben werden.'),
        array('1.2', 'Löschung', 'Das Team behält sich vor, Accounts jederzeit zu löschen.'),
        array('1.3', 'Ein Account pro Person', 'Jeder Spieler darf nur einen Account besitzen. Darauf haben bis zu drei Charaktere Platz, die unterschiedliche Namen tragen müssen.'),
    )),
    2 => array('Charaktere', array(
        array('2.1', 'Namen', 'Charakternamen dürfen keinen historischen, biblischen oder literarischen Hintergrund haben und sollen grundsätzlich zur deutschen Sprache passen. Rassistische, sexistische oder anderweitig anstößige Namen werden geändert – in schweren Fällen droht ein Bann. Ranganzeigende Titel im Namen sind nur mit Erlaubnis eines GMs gestattet. Hinweise zu Namen von Zwergen und Elben findest du in den Beschreibungen der Völker.'),
        array('2.2', 'Profil', 'Das Charakterprofil muss ausgefüllt werden. Es beschreibt ausschließlich das äußere Erscheinungsbild – Hintergrundgeschichten und sonstige Informationen gehören nicht hinein.'),
        array('2.3', 'Volk und Klasse', 'Ein einmal gewähltes Volk und eine gewählte Klasse werden nicht mehr geändert. Das Rollenspiel des Charakters sollte zu beidem passen.'),
    )),
    3 => array('Verhalten', array(
        array('3.1', 'Crossing', 'Gegenstände oder Gold zwischen Charakteren desselben Accounts zu verschieben ist verboten – es sei denn, es ist im Rollenspiel logisch und nachvollziehbar. Dem Account-Inhaber darf daraus kein Vorteil entstehen.'),
        array('3.2', 'Kein AFK-Skillen', 'Wer Skills ausübt, ob aktiv oder passiv, muss am Rechner anwesend sein und dies jederzeit durch eine Reaktion belegen können.'),
        array('3.3', 'Rollenspielpflicht', 'Es gilt jederzeit Rollenspielpflicht. Grobe Verstöße und extreme Brüche im Charakterspiel werden geahndet.'),
        array('3.3.1', 'Power- und Gedankenemotes', 'Sie zählen als Non-RP und werden entsprechend behandelt.', true),
        array('3.4', 'OOC', 'Sprache und Wissen von außerhalb des Spiels (OOC) haben im Spiel nichts zu suchen – ausgenommen im Gespräch mit einem GM. Das gilt auch für die Verbreitung von Spielwissen im Forum oder im IRC.'),
        array('3.5', 'Griefplay', 'Handlungen, die einzig darauf abzielen, anderen Spielern zu schaden, sind strengstens verboten.'),
        array('3.6', 'NPCs', 'Händler, Gardisten, Questcharaktere und andere friedliche NPCs dürfen weder angegriffen noch bestohlen oder gelootet werden.'),
    )),
    4 => array('Bugs', array(
        array('4.1', 'Meldepflicht', 'Bugs sind schon bei Verdacht zu melden und gemeinsam mit einem GM zu testen.'),
        array('4.2', 'Verluste', 'Gegenstände oder Skills, die durch Bugs verloren gehen, werden nicht ersetzt.'),
        array('4.3', 'Bugusing', 'Das wissentliche Ausnutzen von Bugs ist verboten.'),
        array('4.3.1', 'Umgehung des Wirtschaftssystems', 'Wer Gold zwischen Charakteren verschiebt oder auf den Boden wirft, um die Verkaufsgrenze bei Händlern zu umgehen, begeht Bugusing und wird entsprechend bestraft.', true),
    )),
    5 => array('Team', array(
        array('5.1', 'Beschwerden', 'Beschwerden über das Vorgehen eines Teammitglieds reicht ihr schriftlich beim Koordinator ein – am besten mit Logs und Screenshots als Beleg.'),
        array('5.2', 'Letztes Wort', 'Das Team hat immer das letzte Wort.'),
        array('5.3', 'Keine Ausnahmen', 'Kein Teammitglied macht Ausnahmen von bestehenden Regeln – ganz gleich, um welche Situation es geht.'),
    )),
    6 => array('Allgemeines', array(
        array('6.1', 'Angriffe auf den Server', 'Angriffe auf den Server führen zum sofortigen Accountentzug. Wir behalten uns vor, Strafanzeige zu erstatten.'),
        array('6.2', 'Third-Party-Tools', 'Programme, die in den Datenstrom des Clients eingreifen, sind strengstens untersagt. Bei allen anderen Hilfsprogrammen gilt strikte Anwesenheitspflicht (siehe § 3.2).'),
        array('6.3', 'Inhalte', 'Menschenverachtende, rassistische, sexuelle oder diskriminierende Inhalte sind verboten – zum einen, weil auch Jugendliche und Kinder bei uns spielen, zum anderen, weil sie dem Miteinander schaden.'),
    )),
    7 => array('Geistiges Eigentum', array(
        array('7', 'Nutzungsrechte', 'Wer einen Text, eine Grafik oder einen anderen Inhalt veröffentlicht, erlaubt uns, diesen zu nutzen und zu vervielfältigen. Das Urheberrecht bleibt beim Autor; wird Nebelland aufgelöst, fallen alle Rechte an ihn zurück. Stammt ein Inhalt nicht vom Veröffentlichenden selbst, muss er die Erlaubnis des Urhebers einholen und uns darüber informieren. Von rassistischen, sexistischen oder sonstigen rechtswidrigen Inhalten distanzieren wir uns ausdrücklich und entfernen sie umgehend, sobald wir davon erfahren.'),
    )),
);
?>
<div class="rules">
  <div class="notice notice--gold">Mit dem Betreten der Nebellande erkennst du diese Regeln an. Sie sollen niemanden einschränken, sondern ein faires und stimmungsvolles Miteinander ermöglichen.</div>

  <?php foreach ($rules as $no => $section): ?>
  <section class="rule-block" id="r<?php echo $no; ?>">
    <h2><span>§ <?php echo $no; ?></span><?php echo e($section[0]); ?></h2>
    <?php foreach ($section[1] as $rule): ?>
    <div class="rule<?php echo !empty($rule[3]) ? ' rule--sub' : ''; ?>">
      <div class="rule__no">§ <?php echo e($rule[0]); ?></div>
      <div>
        <h3><?php echo e($rule[1]); ?></h3>
        <p><?php echo e($rule[2]); ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </section>
  <?php endforeach; ?>
</div>
