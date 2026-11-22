<?php
require_once dirname(__FILE__) . '/../secrets.php'; // Zugangsdaten (nicht im Git)

	$Level = $_GET["patchlevel"];
        $Server = $_GET["server"];

         $host = "localhost";
         $user = "root";
         $passwort = NL_DB_PASS_ALT;
         $db = "dnwpatcher";
	$connect = mysql_connect($host,$user,$passwort);
 
         mysql_select_db($db);

         $sql = "SELECT  MAX(nummer) FROM `patcher".$Server."server`";
         $result = mysql_query($sql);
 	$row = mysql_fetch_row($result);


         if ($row[0] < $Level)
         	echo "aktuell";
          else
         {
	        $sql = "SELECT * FROM `patcher".$Server."server` WHERE `nummer` = $Level ";
	         $result = mysql_query($sql);
	         $row = mysql_fetch_row($result);
	         echo "http://nebelland.3ngels.de/Patches/".$row[1]." ".$row[2]." ".$row[3];
         }



?>