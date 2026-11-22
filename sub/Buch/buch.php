
<div align="center">
<?php
require_once dirname(__FILE__) . '/../../secrets.php'; // Zugangsdaten (nicht im Git)
            if ( $_GET["ID"] != "" )
	            $BuchID = $_GET["ID"];

            //echo $BuchID;

         $host = "localhost";
         $user = "root";
         $passwort = NL_DB_PASS;
         $db = "runuo";
	 $connect = mysql_connect($host,$user,$passwort);
         mysql_select_db($db);

	if ( !isset($Suchen) && !isset($BuchID) )
	{
				echo "<form method=\"post\" action=\"index.php?s=Buch\">";
				echo "<input type=\"text\" name=\"BuchID\" >";
				echo "<input type=\"Submit\" name=\"Suchen\" value=\"Suchen\">";
				echo "</form>";

//          $sql = "SELECT * FROM `buecher` WHERE `oeffentlich` = 1 ORDER BY `Author` ASC";
//          $result = mysql_query($sql);
//          if (mysql_num_rows($result) != 0)
//          	echo "<font color=#C0C0C0><h3>öffentliche Bücher:</h3></font>";
//          while( $row = mysql_fetch_row($result) )
//          {
//          	echo "<p><a href=\"buch.php?ID=".$row[4]."\">".$row[5]." - ".$row[6]."</a></p>";
//          }
		include("test.php");
	}
	else
	{


          $sql = "SELECT * FROM `buecher` WHERE `BookSerial` = '$BuchID'";
          $result = mysql_query($sql);

	while( $row = mysql_fetch_row($result) )
          {
                  //echo "<font color=#FFFFFF>";
		  echo "<table width=345 cellspacing=0><tr height=37><td colspan=3 width=345 background=http://62.75.218.177/nela/sub/Buch/oben.bmp><font color=#000000>.</font></td></tr>";
                  echo "<tr><td width=21><font color=#000000>.</font></td><td background=http://62.75.218.177/nela/sub/Buch/mitte2.bmp width=315>";
                  echo "<div align=center><font color=#000000>".$row[6]." von ".$row[5]."<br>".$row[2]."</div>";
                  echo "</font></td><td width=22><font color=#000000>.</font></td></tr>";
                  echo "<tr height=34><td width=16></td><td width=314 background=http://62.75.218.177/nela/sub/Buch/unten.bmp><font color=#000000>.</font></td><td width=15></td></tr></table>";
          	  //echo "</font>";
	  }
          echo "<a href=index.php?s=Buch>zurück</a>";
          if (mysql_num_rows($result) == 0)
          {
				echo "<form method=\"post\" action=\"index.php?s=Buch\">";
				echo "<input type=\"text\" name=\"BuchID\" >";
				echo "<input type=\"Submit\" name=\"Suchen\" value=\"Suchen\">";
				echo "</form>";
						echo "<br><br><p>Es ist kein Buch mit dieser ID vorhanden.</p>";
          }
				}



?>
</div>