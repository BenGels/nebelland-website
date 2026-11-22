<?php
/*
 * Schlanke Datenbank-Schicht auf Basis der mysql_*-Funktionen (PHP 5.6),
 * wie sie auch die alte Seite verwendet. Fehler führen nicht zum Abbruch
 * der Seite, sondern werden als freundlicher Hinweis angezeigt.
 */

function nl_db()
{
    static $link = null;
    static $tried = false;
    global $NL_CONFIG;

    if (!$tried) {
        $tried = true;
        // Zuerst wie die alte Seite über "localhost"; findet PHP den Socket dort
        // nicht (z. B. Debian/MariaDB), werden die üblichen Socket-Pfade probiert.
        $hosts = array($NL_CONFIG['db_host']);
        if ($NL_CONFIG['db_host'] === 'localhost') {
            foreach ($NL_CONFIG['db_sockets'] as $socket) {
                if (@file_exists($socket)) {
                    $hosts[] = 'localhost:' . $socket;
                }
            }
        }
        foreach ($hosts as $host) {
            $link = @mysql_connect($host, $NL_CONFIG['db_user'], $NL_CONFIG['db_pass']);
            if ($link) {
                break;
            }
        }
        if ($link) {
            // Tabellen sind latin1 – MySQL wandelt beim Auslesen nach UTF-8 um.
            mysql_set_charset('utf8', $link);
        }
    }
    return $link ? $link : false;
}

/**
 * Führt eine Abfrage in der angegebenen Datenbank aus und liefert alle
 * Zeilen als assoziative Arrays – oder false, wenn etwas schiefgeht.
 */
function nl_query($database, $sql)
{
    $link = nl_db();
    if (!$link || !@mysql_select_db($database, $link)) {
        return false;
    }
    $result = @mysql_query($sql, $link);
    if ($result === false) {
        return false;
    }
    if ($result === true) {
        return true;
    }
    $rows = array();
    while ($row = mysql_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysql_free_result($result);
    return $rows;
}

function nl_escape($value)
{
    $link = nl_db();
    return $link ? mysql_real_escape_string($value, $link) : addslashes($value);
}
