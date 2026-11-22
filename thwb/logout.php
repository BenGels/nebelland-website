<?php
/* $Id: logout.php,v 1.5 2003/11/09 20:10:49 thed_o_n Exp $ */
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000, 2001 by
               Paul Baecher         <paul@thewall.de>
               Felix Gonschorek   <funner@thewall.de>

          download the latest version:
            http://www.thwboard.de

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================

*/
require('./inc/header.inc.php');

if(!$g_user['userid'])
	message('Gast kann nicht ausgeloggt werden',
			'Der Gast-Benutzer kann nicht ausgeloggt werden.<br><br>
			Dieser Fehler tritt auch auf, wenn Sie diese Seite ohne gültige Session-ID besuchen.');

setcookie("thwb_cookie");

unset($s);

$g_user['issession'] = false;

thwb_query("DELETE FROM $pref"."online WHERE userid='$g_user[userid]'");

header("Location: index.php");
//message_redirect('Sie wurden erfolgreich ausgeloggt, bitte warten ...', 'index.php');
?>