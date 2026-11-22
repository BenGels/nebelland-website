<?php require_once __DIR__ . '/geister/_data.php'; ?>
<div class="prose">
  <p class="lead">In den Weiten des Nebels, der unsere Lande umgibt, leben Wesen vielerlei Art. Eine von ihnen sind die Nebelgeister.</p>
  <p>Seit langer Zeit geleiten sie die Bewohner Nebellands durch die Gezeiten der Welt, und ihre Zahl ist unüberschaubar. Viele haben sich noch nie offenbart; schweigend blicken sie auf die Welt hinab, um vielleicht eines Tages in ihre Geschicke einzugreifen.</p>
  <p>Die bekanntesten Nebelgeister sind die drei Schöpfer der Völker: <strong>Zinkhal der Verblichene</strong>, <strong>Svenna die Gutmütige</strong> und <strong>Inares der Weise</strong>. Alle kulturschaffenden Völker verehren sie als Sinnbilder für Leben und Gedeihen.</p>
  <p>Anders als niedere Wesen wie Orks oder Trolle widmet sich ein Bewohner Nebellands jedoch nicht nur einem einzigen Gott, sondern schließt viele Geister in seine Gebete ein. Welchen er sich besonders verbunden fühlt, ergibt sich oft aus Alltag und Stand. So verehren viele Handwerker <strong>Barkus</strong>, der überall als Meister des Handwerks bekannt ist. Denn obwohl allen Geistern sämtliche rechtschaffenen Wesen am Herzen liegen, haben sie unterschiedliche Neigungen und Gaben: <strong>Torbos</strong> gilt als Schutzherr der Krieger, <strong>Simian</strong> verkörpert das Wasser und wird von Fischern und Seefahrern angebetet, <strong>Firson</strong> der Eisige herrscht über Kälte und Wetter, und <strong>Magan</strong> ist das ewige Feuer.</p>
  <p>Diese Aufzählung ließe sich endlos fortsetzen. Eines aber haben alle Nebelgeister gemeinsam: Die Welt, wie wir sie kennen, liegt ihnen am Herzen und steht unter ihrem Schutz. Mit aller Kraft verteidigen sie sie gegen die <strong>Dämonen</strong>, die danach trachten, die Nebellande zu beherrschen oder zu vernichten.</p>
  <p>Die Dämonen fürchten die Macht der Geister und wagen keinen offenen Angriff. List, Tücke und Verrat sind ihre Waffen; sie hausen in den Tiefen des Nebels oder unter der Erde begraben. So bleiben ihre Taten den Nebelgeistern mitunter verborgen. Und selbst wenn eine List entlarvt wird, müssen sich erst ehrenhafte Gläubige finden, die das Übel bekämpfen – denn unmittelbar in die Welt eingreifen können die Nebelgeister nicht.</p>
  <p class="byline">Verfasst von Rolan</p>
</div>

<div class="container mt-2">
  <div class="section-title"><h2 class="mt-0">Die bekanntesten Nebelgeister</h2></div>
  <div class="grid">
    <?php foreach ($SPIRITS as $key => $spirit): ?>
      <?php if ($spirit[4]): ?>
      <a class="card spirit-card" href="<?php echo e(url('geister_' . $key)); ?>" style="--spirit:<?php echo e($spirit[2]); ?>">
        <span class="spirit-dot" aria-hidden="true"><?php echo e(substr($spirit[0], 0, 1)); ?></span>
        <h3><?php echo e($spirit[0]); ?></h3>
        <p><?php echo e($spirit[1]); ?></p>
        <span class="card__more">Glaube &amp; Riten →</span>
      </a>
      <?php else: ?>
      <div class="card spirit-card is-muted" style="--spirit:<?php echo e($spirit[2]); ?>">
        <span class="spirit-dot" aria-hidden="true"><?php echo e(substr($spirit[0], 0, 1)); ?></span>
        <h3><?php echo e($spirit[0]); ?></h3>
        <p><?php echo e($spirit[1]); ?></p>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
