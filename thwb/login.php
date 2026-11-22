<?php
/* $Id: login.php,v 1.2 2003/09/28 17:38:57 thed_o_n Exp $ */
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

define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
$Tloginform = new Template("templates/" . $style['styletemplate'] . "/login.html");

$navpath .= 'Einloggen';
$source = urlencode($source);

eval($Tloginform->GetTemplate("CONTENT"));
eval($Tframe->GetTemplate());
?>