</main>

<footer class="site-footer">
  <div class="site-footer__inner">
    <div class="site-footer__brand">
      <a class="brand" href="<?php echo e(url('home')); ?>">
        <svg class="brand__mark" viewBox="0 0 32 32" aria-hidden="true"><use href="#sigil"/></svg>
        <span class="brand__name">Nebelland</span>
      </a>
      <p>Ein deutscher Ultima-Online-Shard mit eigener Welt, eigenen Völkern und viel Liebe zum Rollenspiel – seit 2003.</p>
    </div>

    <div class="site-footer__col">
      <h2>Gemeinschaft</h2>
      <ul>
        <li><a href="<?php echo e($NL_CONFIG['forum_url']); ?>">Forum-Archiv</a></li>
        <li><a href="<?php echo e($NL_CONFIG['irc_url']); ?>">IRC: <?php echo e($NL_CONFIG['irc_channel']); ?></a> <span class="muted">auf <?php echo e($NL_CONFIG['irc_network']); ?></span></li>
        <li><a href="<?php echo e(url('staff')); ?>">Das Team</a></li>
      </ul>
    </div>

    <div class="site-footer__col">
      <h2>Einstieg</h2>
      <ul>
        <li><a href="<?php echo e(url('anmeldung')); ?>">Anmeldung</a></li>
        <li><a href="<?php echo e(url('1schritte')); ?>">Erste Schritte</a></li>
        <li><a href="<?php echo e(url('files')); ?>">Downloads</a></li>
      </ul>
    </div>

    <div class="site-footer__col">
      <h2>Rechtliches</h2>
      <ul>
        <li><a href="<?php echo e(url('regeln')); ?>">Regeln</a></li>
        <li><a href="<?php echo e(url('impressum')); ?>">Impressum &amp; Hinweise</a></li>
      </ul>
    </div>
  </div>
  <p class="site-footer__legal">Ultima Online ist eine eingetragene Marke von Electronic Arts / Origin. Nebelland ist ein nicht-kommerzielles Fanprojekt und steht in keiner Verbindung zu den Rechteinhabern.</p>
</footer>
</body>
</html>
