<?php
require_once dirname(__FILE__) . '/../../secrets.php'; // Zugangsdaten (nicht im Git)


         $host = "localhost";
         $user = "root";
         $passwort = NL_DB_PASS;
         $db = "runuo";
	$connect = mysql_connect($host,$user,$passwort);
         mysql_select_db($db);

      $coords[0] = "10 ,175";
      $coords[1] = "38 ,175" ;
      $coords[2] = "62 ,175" ;
      $coords[3] = "84 ,175" ;
      $coords[4] = "110,175";
      $coords[5] = "137,175" ;
      $coords[6] = "164,175" ;
      $coords[7] = "187,175" ;
      $coords[8] = "207,175" ;
      $coords[9] = "230,175" ;
      $coords[10] = "4  ,350" ;
      $coords[11] = "25 ,350" ;
      $coords[12] = "45 ,350" ;
      $coords[13] = "65 ,350" ;
      $coords[14] = "86 ,350" ;
      $coords[15] = "108,350" ;
      $coords[16] = "130,350" ;
      $coords[17] = "150,350" ;
      $coords[18] = "172,350" ;
      $coords[19] = "197,350" ;
      $coords[20] = "212,350" ;
      $coords[21] = "240,350" ;


          $sql = "SELECT * FROM `buecher` WHERE `oeffentlich` = 1 ORDER BY `Author` ASC LIMIT 0,21";
          $result = mysql_query($sql);

          $image = imagecreatefromjpeg("http://62.75.218.177/nela/sub/Buch/regal.jpg");

         $col_schrift = imagecolorallocate($image, 0,0, 255); //schwarz
	$SäuleFarbe = imagecolorallocate($image, 255,0, 0);
         $i=0;
	while( $row = mysql_fetch_row($result) )
         {
                 $str = $row[6];
                 imagestringup($image, 2,substr($coords[$i],0,3),substr($coords[$i],4,3), $str ,$col_schrift);
             	$i++;
         }


	header("Content-type: image/png");
	imagepng($image);




?>