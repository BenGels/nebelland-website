<?php
require_once dirname(__FILE__) . '/../secrets.php'; // Zugangsdaten (nicht im Git)



		
         $host = "localhost";
         $user = "root";
         $passwort = NL_DB_PASS;
         $db = "runuo";
	$connect = mysql_connect($host,$user,$passwort);
         mysql_select_db($db);

          $sql = "SELECT * FROM `ewigerstatus` ORDER BY IGTime desc limit 0,200";
          $result = mysql_query($sql);

	echo "<div align=center><p><h3>Ewiger Status<h3></p>";

         echo "<p><table cellspacing=0 cellpadding=0><tr><td><table><tr bgcolor=#273A4D> ";
                 echo "<td width=40><p><b>Rang</b></p></td>";
                 echo "<td width=200><p><b>Name</b></p></td>";
                 echo "<td width=100><p><b>IG-Zeit in Min.</b></p></td>";
		 echo "<td width=100><p><b>IG-Zeit in Std.</b></p></td>";
                 echo "</tr></table></tr></td>";
         $i = 1;
	while( $row = mysql_fetch_row($result) )
         {

                 echo "<tr><td width=500><table><tr> ";
                 echo "<td width=40><p>".$i."</p></td>";
                 echo "<td width=200><p>".$row[1]."</p></td>";
                 echo "<td width=100><p>".$row[2]." Minuten</p></td>";
		 echo "<td width=100><p>".round(($row[2]/60),1)." Stunden</p></td>";
                 echo "</tr></table></td></tr> ";
                 $i++;
         }
         echo "</table></div> "



?>


