<?php
// Stammdaten der Nebelgeister: slug => array(Name, Beiname, Farbe (CSS), Farbbezeichnung, Seite vorhanden?)
$SPIRITS = array(
    'zinkhal' => array('Zinkhal', 'Verblichener Geist des Lebens', '#8a9a7b', '', false),
    'svenna'  => array('Svenna',  'Geist der Gutmütigkeit',        '#f0a24a', 'Warmes Gelb und Orange', true),
    'inares'  => array('Inares',  'Geist der Weisheit',            '#5b7fe0', 'Nachtblau', true),
    'barkus'  => array('Barkus',  'Geist des Handwerks',           '#9aa3ad', 'Sattes Grau', true),
    'torbos'  => array('Torbos',  'Schutzherr der Krieger',        '#e8edf2', 'Weiß', true),
    'simian'  => array('Simian',  'Geist des Wassers',             '#3fa3d6', 'Das Blau der Meere', true),
    'firson'  => array('Firson',  'Geist des Wetters und des Eises', '#a6e0f2', 'Eisblau', true),
    'magan'   => array('Magan',   'Geist des Feuers',              '#e05a46', 'Rot', true),
    'quatan'  => array('Quatan',  'Geist des Handels',             '#e6c84a', 'Gelb', true),
    'lirsa'   => array('Lirsa',   'Geist der Liebe',               '#b07ad6', 'Lila in allen Nuancen', true),
    'narinis' => array('Narinis', 'Geist der Erde',                '#7fae6a', '', false),
    'bragon'  => array('Bragon',  'Geist der Ernte',               '#c9a35a', '', false),
);

/**
 * Steckbrief-Kopf einer Geisterseite.
 * $facts: Bezeichnung => Wert
 */
function spirit_profile($key, $facts)
{
    global $SPIRITS, $slug;
    $spirit = $SPIRITS[$key];

    $nav = array('goetter' => 'Alle Geister');
    foreach ($SPIRITS as $k => $s) {
        if ($s[4]) {
            $nav['geister_' . $k] = $s[0];
        }
    }

    $html  = subnav($nav, $slug);
    $html .= '<div class="profile" style="--spirit:' . e($spirit[2]) . '">';
    $html .= '<div class="profile__emblem" aria-hidden="true">' . e(substr($spirit[0], 0, 1)) . '</div>';
    $html .= '<dl><dt>Name</dt><dd>' . e($spirit[0]) . ' – ' . e($spirit[1]) . '</dd>';
    foreach ($facts as $label => $value) {
        $html .= '<dt>' . e($label) . '</dt><dd>' . e($value) . '</dd>';
    }
    if ($spirit[3] !== '') {
        $html .= '<dt>Farbe</dt><dd>' . e($spirit[3]) . '</dd>';
    }
    return $html . '</dl></div>';
}
