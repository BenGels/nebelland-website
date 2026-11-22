<?php
$progress = array(
    'Planung'     => 95,
    'Homepage'    => 90,
    'Bau & Karte' => 31,
    'Skripte'     => 15,
);
?>
<div class="ui-text">
  <p>„Nebelland Final“ ist die nächste große Ausbaustufe unseres Shards. Hier seht ihr, wie weit die einzelnen Bereiche gediehen sind.</p>

  <div class="form-card" style="max-width:none">
    <?php foreach ($progress as $label => $value): ?>
    <div class="progress">
      <div class="progress__label"><span><?php echo e($label); ?></span><span><?php echo $value; ?> %</span></div>
      <div class="progress__bar" role="progressbar" aria-label="<?php echo e($label); ?>" aria-valuenow="<?php echo $value; ?>" aria-valuemin="0" aria-valuemax="100"><i style="width:<?php echo $value; ?>%"></i></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
