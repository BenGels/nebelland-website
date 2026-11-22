<?php
$rows = nl_query($NL_CONFIG['db_runuo'], 'SELECT `Charname`, `IGTime` FROM `ewigerstatus` ORDER BY `IGTime` DESC LIMIT 200');

function format_playtime($minutes)
{
    $hours = $minutes / 60;
    if ($hours >= 24) {
        return number_format($hours / 24, 1, ',', '.') . ' Tage';
    }
    return number_format($hours, 1, ',', '.') . ' Std.';
}
?>
<div class="ui-text center">
  <p>Die Ruhmeshalle der Nebellande: Hier stehen die Charaktere, die die meiste Zeit in unserer Welt verbracht haben.</p>
</div>

<?php if ($rows === false): ?>
  <div class="narrow"><?php echo db_unavailable_notice('Der Ewige Status'); ?></div>
<?php elseif (!$rows): ?>
  <p class="center muted">Noch keine Einträge vorhanden.</p>
<?php else: ?>
  <?php $top = array_slice($rows, 0, 3); ?>
  <div class="stat-tiles" style="max-width:52rem">
    <?php foreach ($top as $i => $row): ?>
    <div class="stat-tile" style="text-align:center">
      <span class="rank rank--<?php echo $i + 1; ?>" style="margin:0 auto .6rem"><?php echo $i + 1; ?></span>
      <strong style="font-size:1.3rem"><?php echo e($row['Charname']); ?></strong>
      <span style="margin-top:.4rem;text-transform:none;letter-spacing:0"><?php echo e(format_playtime($row['IGTime'])); ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="narrow">
    <div class="table-wrap">
      <table class="table">
        <thead>
          <tr><th scope="col">Rang</th><th scope="col">Charakter</th><th scope="col" class="num">Spielzeit</th><th scope="col" class="num">Stunden</th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $i => $row): ?>
          <tr>
            <td><span class="rank<?php echo $i < 3 ? ' rank--' . ($i + 1) : ''; ?>"><?php echo $i + 1; ?></span></td>
            <th scope="row"><?php echo e($row['Charname']); ?></th>
            <td class="num"><?php echo e(format_playtime($row['IGTime'])); ?></td>
            <td class="num"><?php echo number_format($row['IGTime'] / 60, 1, ',', '.'); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>
