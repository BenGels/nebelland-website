 <IMG SRC="sub/Buch/regal.php"
 ALT="Bücherregal Nebellands" USEMAP="#map1" border=0>

<MAP NAME="map1">

<?php
require_once dirname(__FILE__) . '/../../secrets.php'; // Zugangsdaten (nicht im Git)

      $coords[0] = "0,10,30,180";
      $coords[1] = "31,10,55,180" ;
      $coords[2] = "60,10,80,180" ;
      $coords[3] = "80,10,100,180" ;
      $coords[4] =  "100,10,130,180";
      $coords[5] = "130,10,160,180" ;
      $coords[6] = "160,10,190,180" ;
      $coords[7] = "190,10,205,180" ;
      $coords[8] = "205,10,220,180" ;
      $coords[9] = "220,10,250,180" ;
      $coords[10] = "0,150,20,350" ;
      $coords[11] = "21,150,40,350" ;
      $coords[12] = "40,150,60,350" ;
      $coords[13] = "60,150,80,350" ;
      $coords[14] = "80,150,100,350" ;
      $coords[15] = "100,150,120,350" ;
      $coords[16] = "120,150,150,350" ;
      $coords[17] = "150,150,170,350" ;
      $coords[18] = "170,150,200,350" ;
      $coords[19] = "200,150,210,350" ;
      $coords[20] = "210,150,230,350" ;
      $coords[21] = "230,150,250,350" ;

            $db_server = "localhost";
	   $db_name = "runuo";
	   $db_user = "root";
	   $db_passwort = NL_DB_PASS;


          $db = @MYSQL_CONNECT($db_server,$db_user,$db_passwort) or die ("Konnte keine Verbindung zur Datenbank herstellen");
   	 $db_check = @MYSQL_SELECT_DB($db_name);

          $sql = "SELECT * FROM `buecher` WHERE `oeffentlich` = 1 ORDER BY `Author` ASC";
          $result = mysql_query($sql);
          $i=0;
          while( $row = mysql_fetch_row($result) )
          {
          	echo "<AREA SHAPE=rect COORDS=\"".$coords[$i]."\" HREF=\"http://62.75.218.177/nela/index.php?s=Buch&ID=".$row[4]."\">";

                 $i++;
          }

?>

</MAP>
