<?php
/*
 * Nebelland – neue Homepage
 * Einfacher Front-Controller: ?s=<seite> lädt die passende Datei aus pages/.
 * Die Seitennamen entsprechen weitgehend denen der alten Seite, damit alte
 * Links weiterhin funktionieren.
 */

header('Content-Type: text/html; charset=utf-8');

require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/db.php';
require __DIR__ . '/inc/helpers.php';

// slug => array(Datei, Seitentitel, Navigationsgruppe)
$PAGES = array(
    'home'              => array('home.php',               'Willkommen',            'home'),
    'news'              => array('news.php',               'Neuigkeiten',           'nebelland'),
    'nebelland'         => array('nebelland.php',          'Über Nebelland',        'nebelland'),
    'anmeldung'         => array('anmeldung.php',          'Anmeldung',             'nebelland'),
    '1schritte'         => array('erste-schritte.php',     'Erste Schritte',        'nebelland'),
    'regeln'            => array('regeln.php',             'Regeln',                'nebelland'),
    'faq'               => array('faq.php',                'Häufige Fragen',        'nebelland'),
    'files'             => array('downloads.php',          'Downloads',             'nebelland'),
    'staff'             => array('staff.php',              'Das Team',              'nebelland'),
    'impressum'         => array('impressum.php',          'Rechtliches',           'nebelland'),

    'gesch'             => array('geschichte.php',         'Die Weltgeschichte',    'welt'),
    'rassen'            => array('rassen.php',             'Die Völker',            'welt'),
    'rasse_mensch'      => array('rassen/mensch.php',      'Menschen',              'welt'),
    'rasse_zwerg'       => array('rassen/zwerg.php',       'Zwerge',                'welt'),
    'rasse_lichtelb'    => array('rassen/lichtelb.php',    'Lichtelben',            'welt'),
    'rasse_nachtelb'    => array('rassen/nachtelb.php',    'Nachtelben',            'welt'),
    'klassen'           => array('klassen.php',            'Die Klassen',           'welt'),
    'klasse_krieger'    => array('klassen/krieger.php',    'Krieger',               'welt'),
    'klasse_handwerker' => array('klassen/handwerker.php', 'Handwerker',            'welt'),
    'klasse_magier'     => array('klassen/magier.php',     'Magier',                'welt'),
    'staedte'           => array('staedte.php',            'Die Städte',            'welt'),
    'stadt_3bruecken'   => array('staedte/dreibruecken.php', 'Dreibrücken',         'welt'),
    'stadt_gadarosch'   => array('staedte/gadarosch.php',  'Gadarosch',             'welt'),
    'stadt_morgenlicht' => array('staedte/morgenlicht.php', 'Morgenlicht',          'welt'),
    'stadt_halbmond'    => array('staedte/halbmond.php',   'Halbmond',              'welt'),
    'gilden'            => array('gilden.php',             'Gilden',                'welt'),
    'regionen'          => array('karte.php',              'Karte der Nebellande',  'welt'),
    'goetter'           => array('goetter.php',            'Die Nebelgeister',      'welt'),
    'geister_svenna'    => array('geister/svenna.php',     'Svenna',                'welt'),
    'geister_inares'    => array('geister/inares.php',     'Inares',                'welt'),
    'geister_barkus'    => array('geister/barkus.php',     'Barkus',                'welt'),
    'geister_torbos'    => array('geister/torbos.php',     'Torbos',                'welt'),
    'geister_simian'    => array('geister/simian.php',     'Simian',                'welt'),
    'geister_firson'    => array('geister/firson.php',     'Firson',                'welt'),
    'geister_magan'     => array('geister/magan.php',      'Magan',                 'welt'),
    'geister_quatan'    => array('geister/quatan.php',     'Quatan',                'welt'),
    'geister_lirsa'     => array('geister/lirsa.php',      'Lirsa',                 'welt'),

    'stats'             => array('stats.php',              'Attribute',             'wissen'),
    'skills'            => array('skills.php',             'Skillcaps',             'wissen'),
    'magie'             => array('magie.php',              'Magie',                 'wissen'),
    'zauber'            => array('zauber.php',             'Zauberbuch',            'wissen'),
    'Buch'              => array('bibliothek.php',         'Bibliothek',            'wissen'),

    'status'            => array('status.php',             'Serverstatus',          'server'),
    'ewiger'            => array('ewiger-status.php',      'Ewiger Status',         'server'),
    'haus'              => array('haeuser.php',            'Häuser & Mieten',       'server'),
    'final'             => array('final.php',              'Projekt „Final“',       'server'),
);

// Hauptnavigation: Gruppe => array(Bezeichnung, array(slug => Linktext))
$NAV = array(
    'nebelland' => array('Nebelland', array(
        'nebelland' => 'Über Nebelland',
        'anmeldung' => 'Anmeldung',
        '1schritte' => 'Erste Schritte',
        'regeln'    => 'Regeln',
        'faq'       => 'Häufige Fragen',
        'files'     => 'Downloads',
        'staff'     => 'Das Team',
        'forum/'    => 'Forum-Archiv',
    )),
    'welt' => array('Die Welt', array(
        'gesch'    => 'Weltgeschichte',
        'rassen'   => 'Völker',
        'klassen'  => 'Klassen',
        'staedte'  => 'Städte',
        'goetter'  => 'Nebelgeister',
        'gilden'   => 'Gilden',
        'regionen' => 'Karte',
    )),
    'wissen' => array('Wissen', array(
        'stats'  => 'Attribute',
        'skills' => 'Skillcaps',
        'magie'  => 'Magie',
        'zauber' => 'Zauberbuch',
        'Buch'   => 'Bibliothek',
    )),
    'server' => array('Server', array(
        'status' => 'Serverstatus',
        'ewiger' => 'Ewiger Status',
        'haus'   => 'Häuser & Mieten',
        'final'  => 'Projekt „Final“',
    )),
);

$slug = get_param('s', 'home');
if ($slug === '') {
    $slug = 'home';
}
if (!isset($PAGES[$slug])) {
    header('HTTP/1.0 404 Not Found');
    $page = array('404.php', 'Seite nicht gefunden', '');
} else {
    $page = $PAGES[$slug];
}

$pageTitle   = $page[1];
$pageSection = $page[2];
$isHome      = $slug === 'home';

// Seiteninhalt zuerst puffern, damit Seiten z. B. eigene Meta-Angaben setzen können
ob_start();
include __DIR__ . '/pages/' . $page[0];
$content = ob_get_clean();

include __DIR__ . '/inc/header.php';
echo $content;
include __DIR__ . '/inc/footer.php';
