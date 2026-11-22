<form action="add_patch.php" method="get">
Dateiname: <input type="Text" name="Dateiname" value="" size="40" maxlength=""><br>
Passwort: <input type="Text" name="Passwort" value="" size="40" maxlenght="">
<br>Server:
<select name="Server" size="0">
<option value="spiel">Spielserver</option>
<option value="bau">Bauserver</option>
</select>
<input type="Submit" name="abschicken" value="abschicken">
</form>


<?php
	$Dateiname = $_GET["Dateiname"];
	$Passwort = $_GET["Passwort"];
	$Server = $_GET["Server"];
	$pfad = "C:/Server/Apache/xampp/htdocs/nela/Patches/ ";
        
        echo $pfad = substr($pfad,0,-1).$Dateiname;
	if (is_file($pfad))
         {
          	 $md5 = md5_file($pfad);
	         $host = "localhost";
	         $user = "root";
                  $db = "dnwpatcher";
	         $connect = mysql_connect($host,$user,$Passwort);
	         mysql_select_db($db);

                  $sql = "SELECT Max(nummer) FROM patcher".$Server."server";
                  $result = mysql_query($sql);
                  $row = mysql_fetch_row($result);
                  $max = $row[0] + 1;
                  $next = $max+1;

	         $sql = "INSERT INTO patcher".$Server."server VALUES ($max, '$Dateiname', '$md5', $next )";
	         $result = mysql_query($sql);
                  echo "<br>Die Datei $Dateiname wurde für den $Server Server auf Patcherlevel $next eingetragen";


         }
         else echo $Dateiname." ist keine gültige Datei";
?>
