<?php
require_once dirname(__FILE__) . '/secrets.php'; // Zugangsdaten (nicht im Git)



         $host = "localhost";
         $user = "root";
         $passwort = NL_DB_PASS;
         $db = "runuo";
	$connect = mysql_connect($host,$user,$passwort);
         mysql_select_db($db);

          $sql = "SELECT * FROM `status`";
          $result = mysql_query($sql);

         //$image = imagecreate(400, 400);  //Erschafft das Bild
         $image = imagecreatefromjpeg("karte.jpg");

	//$bg = imagecolorallocate($image, 255, 255, 255); //Hintergrund:weiß
         $col_schrift = imagecolorallocate($image, 0,0, 0); //schwarz
	$SäuleFarbe = imagecolorallocate($image, 255,0, 0);  #0080FF

	while( $row = mysql_fetch_row($result) )
         {
         	if ( ($row[1]/3.16) < 510 && $row[4] == 0 )
	         imagefilledrectangle ( $image, (($row[1]/3.16)-1) , (($row[2]/3.16)-1) , (($row[1]/3.16)+1) , (($row[2]/3.16)+1) , $SäuleFarbe);   //Balken werden erzeugt


         }
         $Zeit = date("d M Y - H:i:s");
         imagestring ( $image, 2, 540, 250, $Zeit, $col_schrift );

         $sql = "SELECT COUNT(*) FROM `status`";
         $result = mysql_query($sql);
         $row = mysql_fetch_row($result);
         imagestring ( $image, 3, 550, 280, $row[0]." Spieler online", $col_schrift );

	header("Content-type: image/png");
	imagepng($image);



?>