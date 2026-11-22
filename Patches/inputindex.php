<?php
require_once dirname(__FILE__) . '/../secrets.php'; // Zugangsdaten (nicht im Git)

	$Level = $_GET["patchlevel"];
        $Server = $_GET["server"];

         $host = "localhost";
         $user = "root";
         $passwort = NL_DB_PASS;
         $db = "dnwpatcher";
	$connect = mysql_connect($host,$user,$passwort);
         mysql_select_db($db);






         $sql = "SELECT * FROM `patcherspielserver`";
         $result = mysql_query($sql);
	while($row = mysql_fetch_row($result))
	echo $row[0]." --- ".$row[1]." --- ".$row[2]." --- ".$row[3]." --- <br>";



     



?>