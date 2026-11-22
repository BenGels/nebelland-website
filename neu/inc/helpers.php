<?php

/** HTML-sichere Ausgabe */
function e($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/** Link auf eine Unterseite */
function url($slug, $params = array())
{
    // Direkte Pfade (z. B. "forum/" für das statische Archiv) unverändert lassen
    if (strpos($slug, '/') !== false) {
        return $slug;
    }
    $query = $slug === 'home' ? array() : array('s' => $slug);
    $query = array_merge($query, $params);
    return 'index.php' . ($query ? '?' . http_build_query($query) : '');
}

function get_param($key, $default = '')
{
    return isset($_GET[$key]) ? (string) $_GET[$key] : $default;
}

function post_param($key, $default = '')
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

/**
 * Erlaubt in Texten aus der Datenbank nur Zeilenumbrüche und einfache
 * Hervorhebungen – alles andere wird als Text ausgegeben.
 */
function safe_rich_text($text)
{
    // Alte Links zeigen auf längst abgeschaltete Server – Linktext bleibt erhalten.
    $text = strip_tags($text, '<br><b><i><u><strong><em>');
    $text = e($text);
    $text = preg_replace('#&lt;br\s*/?&gt;#i', '<br>', $text);
    $text = preg_replace('#&lt;(/?)(b|i|u|strong|em)&gt;#i', '<$1$2>', $text);
    return $text;
}

/** Hinweisbox, wenn die Datenbank nicht erreichbar ist */
function db_unavailable_notice($what)
{
    return '<div class="notice notice--warn"><strong>' . e($what) . ' ist gerade nicht verfügbar.</strong> '
        . 'Die Datenbank antwortet nicht – bitte versuche es später noch einmal.</div>';
}

/**
 * Unternavigation für Seitengruppen (Völker, Städte, Klassen …).
 * $items: slug => Bezeichnung
 */
function subnav($items, $current)
{
    $html = '<nav class="subnav" aria-label="Unterseiten"><ul>';
    foreach ($items as $slug => $label) {
        $active = $slug === $current ? ' aria-current="page"' : '';
        $html .= '<li><a href="' . e(url($slug)) . '"' . $active . '>' . e($label) . '</a></li>';
    }
    return $html . '</ul></nav>';
}

/** "08.07.2004 14:19" → "8. Juli 2004" */
function format_date_de($raw)
{
    $months = array(1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli',
        'August', 'September', 'Oktober', 'November', 'Dezember');
    if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})/', $raw, $m) && isset($months[(int) $m[2]])) {
        return (int) $m[1] . '. ' . $months[(int) $m[2]] . ' ' . $m[3];
    }
    return $raw;
}

/** "08.07.2004 14:19" → "2004-07-08" für <time datetime> */
function iso_date($raw)
{
    if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})/', $raw, $m)) {
        return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
    }
    return '';
}

/** Gibt einen News-Eintrag aus der Tabelle hp.news aus */
function render_news_item($row)
{
    $html  = '<article class="news-item">';
    $html .= '<div class="news-item__meta"><time datetime="' . e(iso_date($row['Datum'])) . '">' . e(format_date_de($row['Datum'])) . '</time>';
    if (trim($row['Von']) !== '') {
        $html .= '<span>von ' . e(ucfirst($row['Von'])) . '</span>';
    }
    $html .= '</div>';
    $title = trim($row['Betreff']) !== '' && $row['Betreff'] !== 'Betreff' ? $row['Betreff'] : 'Neuigkeiten';
    $html .= '<h3>' . e($title) . '</h3>';
    $html .= '<div class="news-item__body"><p>' . safe_rich_text($row['Text']) . '</p></div>';
    return $html . '</article>';
}

/** Online-Spieler laut RunUO-Statustabelle (null, wenn unbekannt) */
function players_online()
{
    global $NL_CONFIG;
    static $count = false;
    if ($count === false) {
        $rows = nl_query($NL_CONFIG['db_runuo'], 'SELECT COUNT(*) AS n FROM `status`');
        $count = $rows ? (int) $rows[0]['n'] : null;
    }
    return $count;
}
