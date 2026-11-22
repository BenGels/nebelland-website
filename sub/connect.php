<?php
require_once dirname(__FILE__) . '/../secrets.php'; // Zugangsdaten (nicht im Git)

         $host = "localhost";
         $user = "root";
         $passwort = NL_DB_PASS;
         $db = "hp";
	$connect = mysql_connect($host,$user,$passwort);
         mysql_select_db($db);
?>