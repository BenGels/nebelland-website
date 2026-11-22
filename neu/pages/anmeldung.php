<?php
$races  = array('Mensch' => 'Mensch', 'Zwerg' => 'Zwerg', 'Lichtelb' => 'Lichtelb', 'Nachtelb' => 'Nachtelb');
$errors = array();
$icq    = '';
$race   = 'Mensch';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $icq  = preg_replace('/\D+/', '', post_param('ICQ'));
    $race = post_param('Rasse');

    // Einfacher Spamschutz: Dieses Feld ist für Menschen unsichtbar
    if (post_param('website') !== '') {
        $errors[] = 'Die Anmeldung konnte nicht gespeichert werden.';
    }
    if (strlen($icq) < 5 || strlen($icq) > 10) {
        $errors[] = 'Bitte gib eine gültige ICQ-Nummer an (5 bis 10 Ziffern).';
    }
    if (!isset($races[$race])) {
        $errors[] = 'Bitte wähle ein Volk aus.';
    }

    if (!$errors) {
        $sql = sprintf(
            "INSERT INTO `anmeldungen` (`ICQ`, `Rasse`, `Datum`) VALUES ('%s', '%s', '%s')",
            nl_escape($icq),
            nl_escape($race),
            nl_escape(date('d.m.Y H:i'))
        );
        if (nl_query($NL_CONFIG['db_homepage'], $sql) === true) {
            header('Location: ' . url('anmeldung', array('gesendet' => 1)));
            exit;
        }
        $errors[] = 'Deine Anmeldung konnte gerade nicht gespeichert werden. Bitte versuche es später noch einmal.';
    }
}
?>
<div class="ui-text">
  <p class="lead" style="font-size:1.15rem">Auf Nebelland gibt es keine anonyme Account-Vergabe. Wer mitspielen möchte, meldet sich bei uns – danach melden wir uns bei dir.</p>

  <ol class="steps">
    <li><strong>Formular ausfüllen.</strong> Trag deine ICQ-Nummer ein und verrate uns, welches Volk dich interessiert.</li>
    <li><strong>Kurzes Gespräch.</strong> In der Regel innerhalb eines Tages meldet sich jemand aus dem Team für ein lockeres Kennenlerngespräch. Keine Sorge: Wir fragen keine Regeln ab.</li>
    <li><strong>Zugangsdaten erhalten.</strong> Passt es für beide Seiten, bekommst du direkt im Gespräch deine Account-Daten.</li>
  </ol>
</div>

<?php if (get_param('gesendet') === '1'): ?>
  <div class="form-card">
    <div class="notice notice--ok" role="status"><strong>Vielen Dank!</strong> Deine Anmeldung ist bei uns eingegangen. Wir melden uns so schnell wie möglich bei dir.</div>
    <p class="mt-0">Bis dahin kannst du schon einmal einen Blick in die <a href="<?php echo e(url('1schritte')); ?>">Ersten Schritte</a> werfen und den <a href="<?php echo e(url('files')); ?>">Patcher</a> installieren.</p>
  </div>
<?php else: ?>
  <form class="form-card" method="post" action="<?php echo e(url('anmeldung')); ?>" novalidate>
    <?php if ($errors): ?>
      <div class="notice notice--warn" role="alert">
        <?php foreach ($errors as $error): ?><div><?php echo e($error); ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="field">
      <label for="icq">Deine ICQ-Nummer</label>
      <input class="input" id="icq" name="ICQ" type="text" inputmode="numeric" autocomplete="off" maxlength="12" required value="<?php echo e($icq); ?>">
      <small>Darüber nehmen wir Kontakt mit dir auf.</small>
    </div>

    <fieldset class="field" style="border:0;padding:0;margin:0 0 1.5rem">
      <legend style="font-weight:600;font-size:.92rem;color:var(--heading);margin-bottom:.4rem">Welches Volk interessiert dich?</legend>
      <div class="choice-grid">
        <?php foreach ($races as $value => $label): ?>
        <label class="choice">
          <input type="radio" name="Rasse" value="<?php echo e($value); ?>"<?php echo $race === $value ? ' checked' : ''; ?>>
          <span><?php echo e($label); ?></span>
        </label>
        <?php endforeach; ?>
      </div>
      <small><a href="<?php echo e(url('rassen')); ?>">Die Völker im Überblick</a> – die endgültige Wahl triffst du erst im Spiel.</small>
    </fieldset>

    <div class="hp-field" aria-hidden="true">
      <label for="website">Bitte leer lassen</label>
      <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
    </div>

    <button class="btn" type="submit">Anmeldung absenden</button>
  </form>
<?php endif; ?>
