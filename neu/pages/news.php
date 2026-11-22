<?php
$perPage = 8;
$total   = nl_query($NL_CONFIG['db_homepage'], 'SELECT COUNT(*) AS n FROM `news`');
$total   = $total ? (int) $total[0]['n'] : 0;
$pages   = max(1, (int) ceil($total / $perPage));
$current = min($pages, max(1, (int) get_param('seite', 1)));
$offset  = ($current - 1) * $perPage;
$rows    = nl_query($NL_CONFIG['db_homepage'], 'SELECT * FROM `news` ORDER BY `NID` DESC LIMIT ' . (int) $offset . ', ' . (int) $perPage);
?>
<div class="news">
  <?php if ($rows === false): ?>
    <?php echo db_unavailable_notice('Der Newsbereich'); ?>
  <?php elseif (!$rows): ?>
    <p class="muted center">Noch keine Neuigkeiten vorhanden.</p>
  <?php else: ?>
    <?php foreach ($rows as $row) { echo render_news_item($row); } ?>
  <?php endif; ?>
</div>

<?php if ($pages > 1): ?>
<nav class="pagination" aria-label="Seiten">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <?php if ($i === $current): ?>
      <span aria-current="page"><?php echo $i; ?></span>
    <?php else: ?>
      <a href="<?php echo e(url('news', array('seite' => $i))); ?>"><?php echo $i; ?></a>
    <?php endif; ?>
  <?php endfor; ?>
</nav>
<?php endif; ?>
