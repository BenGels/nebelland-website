<?php
require_once dirname(__FILE__) . '/../../secrets.php'; // Zugangsdaten (nicht im Git)
/*
 * Zentrale Konfiguration der neuen Nebelland-Seite.
 * Zugangsdaten entsprechen der alten Seite (sub/connect.php).
 */

$NL_CONFIG = array(
    'db_host'     => 'nela-mysql-restore',
    'db_user'     => 'root',
    'db_pass'     => NL_DB_PASS_NEU,
    'db_homepage' => 'hp',     // News, Anmeldungen, Häuser
    'db_runuo'    => 'runuo',  // Spielerstatus, Ewiger Status, Bibliothek
    // Ausweich-Sockets, falls PHP den MySQL-Socket unter "localhost" nicht findet
    'db_sockets'  => array('/var/run/mysqld/mysqld.sock', '/run/mysqld/mysqld.sock', '/tmp/mysql.sock'),

    'forum_url'   => 'forum/',  // statisches, schreibgeschütztes Forum-Archiv (2004–2007)
    'irc_channel' => '#Nebelland',
    'irc_url'     => 'irc://irc.gamesurge.net/nebelland',
    'irc_network' => 'GameSurge',

    // Spiel-Client Ultima Online: Mondain's Legacy (externer Download)
    'uo_client_url' => 'https://download.cnet.com/ultima-online-mondain-s-legacy-client/3000-7540_4-10432237.html',

    // Patcher-Installer liegt unter /Patches/ (einziger per Cloudflare freigegebener Pfad
    // ausserhalb von /neu/, siehe Access-Policy "^/(neu|Patches)(/.*)?$")
    'patcher_url' => '../Patches/NeLa-Patcher-Installation.exe',
);
