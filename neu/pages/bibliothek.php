<?php
$db     = $NL_CONFIG['db_runuo'];
$bookId = get_param('ID');
$search = trim(get_param('suche'));

if ($bookId !== ''):
    $rows = nl_query($db, "SELECT * FROM `buecher` WHERE `BookSerial` = '" . nl_escape($bookId) . "' AND `oeffentlich` = 1 LIMIT 1");
?>
  <div class="container" style="max-width:40rem">
    <a class="back-link" href="<?php echo e(url('Buch')); ?>">← Zurück zur Bibliothek</a>
  </div>
  <?php if ($rows === false): ?>
    <div class="narrow"><?php echo db_unavailable_notice('Die Bibliothek'); ?></div>
  <?php elseif (!$rows): ?>
    <div class="narrow"><div class="notice">Unter dieser Kennung ist kein öffentliches Buch verzeichnet.</div></div>
  <?php else: $book = $rows[0]; ?>
    <article class="parchment">
      <h2><?php echo e($book['Titel']); ?></h2>
      <p class="parchment__author"><?php echo trim($book['Author']) !== '' ? 'von ' . e($book['Author']) : 'Verfasser unbekannt'; ?></p>
      <div class="parchment__text"><?php echo preg_replace('#^(<br>\s*)+#', '', safe_rich_text($book['Text'])); ?></div>
    </article>
  <?php endif; ?>

<?php else:
    $where = "`oeffentlich` = 1";
    if ($search !== '') {
        $s = nl_escape($search);
        $where .= " AND (`Titel` LIKE '%$s%' OR `Author` LIKE '%$s%' OR `BookSerial` = '$s')";
    }
    $books = nl_query($db, "SELECT `BookSerial`, `Titel`, `Author` FROM `buecher` WHERE $where ORDER BY `Titel` ASC");
?>
  <div class="ui-text center">
    <p>In den Bibliotheken der Nebellande ruhen die Schriften ihrer Bewohner: Abhandlungen über Magie und Kampfkunst, Bestiarien, Tagebücher und Gedichte. Alle öffentlichen Bücher aus dem Spiel kannst du hier nachlesen.</p>
  </div>

  <form class="search" method="get" action="index.php" role="search">
    <input type="hidden" name="s" value="Buch">
    <label class="visually-hidden" for="suche">Titel, Verfasser oder Buch-ID</label>
    <input class="input" id="suche" name="suche" type="search" placeholder="Titel, Verfasser oder Buch-ID" value="<?php echo e($search); ?>">
    <button class="btn btn--small" type="submit">Suchen</button>
  </form>

  <?php if ($books === false): ?>
    <div class="narrow"><?php echo db_unavailable_notice('Die Bibliothek'); ?></div>
  <?php elseif (!$books): ?>
    <p class="center muted">Keine Bücher gefunden<?php echo $search !== '' ? ' für „' . e($search) . '“' : ''; ?>.</p>
  <?php else: ?>
    <div class="shelf">
      <?php foreach ($books as $book): ?>
      <a class="book" href="<?php echo e(url('Buch', array('ID' => $book['BookSerial']))); ?>" style="--hue:<?php echo abs(crc32($book['BookSerial'])) % 360; ?>">
        <span class="book__title"><?php echo e($book['Titel']); ?></span>
        <span class="book__author"><?php echo trim($book['Author']) !== '' ? e($book['Author']) : 'Unbekannt'; ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>
