<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $isHome ? 'Nebelland – Ultima-Online-Rollenspielshard' : e($pageTitle) . ' · Nebelland'; ?></title>
<meta name="description" content="Nebelland – ein deutscher Ultima-Online-Freeshard mit Schwerpunkt auf Rollenspiel, eigener Welt, eigenen Völkern und einer lebendigen Gemeinschaft.">
<meta name="theme-color" content="#0d1117">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="icon" href="assets/img/favicon.svg" type="image/svg+xml">
<script src="assets/js/main.js" defer></script>
</head>
<body class="<?php echo $isHome ? 'is-home' : 'is-sub'; ?>">
<a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

<header class="site-header">
  <div class="site-header__inner">
    <a class="brand" href="<?php echo e(url('home')); ?>" aria-label="Nebelland – zur Startseite">
      <svg class="brand__mark" viewBox="0 0 32 32" aria-hidden="true"><use href="#sigil"/></svg>
      <span class="brand__name">Nebelland</span>
    </a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="hauptmenue">
      <span class="nav-toggle__bars" aria-hidden="true"></span>
      <span class="visually-hidden">Menü</span>
    </button>

    <nav class="main-nav" id="hauptmenue" aria-label="Hauptnavigation">
      <ul class="main-nav__list">
        <?php foreach ($NAV as $group => $entry): ?>
        <li class="main-nav__group<?php echo $pageSection === $group ? ' is-current' : ''; ?>">
          <button class="main-nav__trigger" type="button" aria-expanded="false"><?php echo e($entry[0]); ?>
            <svg viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <ul class="main-nav__menu">
            <?php foreach ($entry[1] as $navSlug => $label): ?>
            <li><a href="<?php echo e(url($navSlug)); ?>"<?php echo $navSlug === $slug ? ' aria-current="page"' : ''; ?>><?php echo e($label); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php endforeach; ?>
        <li class="main-nav__cta"><a class="btn btn--small" href="<?php echo e(url('anmeldung')); ?>">Jetzt anmelden</a></li>
      </ul>
    </nav>
  </div>
</header>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="sigil" viewBox="0 0 32 32">
    <circle cx="16" cy="16" r="14.5" fill="none" stroke="currentColor" stroke-width="1.2" opacity=".55"/>
    <path d="M16 4.5c-3.2 3.4-4.8 7.2-4.8 11.5S12.8 24.1 16 27.5c3.2-3.4 4.8-7.2 4.8-11.5S19.2 7.9 16 4.5Z" fill="none" stroke="currentColor" stroke-width="1.4"/>
    <path d="M6 13.5c3.4 1.3 6.8 1.3 10 0s6.6-1.3 10 0M6 18.5c3.4 1.3 6.8 1.3 10 0s6.6-1.3 10 0" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" opacity=".8"/>
  </symbol>
</svg>

<?php if ($isHome): ?>
<section class="hero">
  <div class="hero__fog" aria-hidden="true"><span></span><span></span></div>
  <div class="hero__content">
    <p class="eyebrow">Ultima Online · Rollenspiel-Freeshard</p>
    <h1 class="hero__title">Nebelland</h1>
    <p class="hero__lead">Vom Nebel verborgen und nur wenigen bekannt liegen die Inseln, die man Nebelland nennt. Trügerisch ist der Schein: Wo der Nebel scheinbar nichts verbirgt, wartet so viel Leben.</p>
    <div class="hero__actions">
      <a class="btn" href="<?php echo e(url('anmeldung')); ?>">Account anfragen</a>
      <a class="btn btn--ghost" href="<?php echo e(url('1schritte')); ?>">Erste Schritte</a>
    </div>
  </div>
</section>
<?php else: ?>
<section class="page-head">
  <div class="page-head__fog" aria-hidden="true"></div>
  <div class="page-head__inner">
    <?php if ($pageSection && isset($NAV[$pageSection])): ?>
    <p class="eyebrow"><?php echo e($NAV[$pageSection][0]); ?></p>
    <?php endif; ?>
    <h1><?php echo e($pageTitle); ?></h1>
  </div>
</section>
<?php endif; ?>

<main id="inhalt" class="site-main">
