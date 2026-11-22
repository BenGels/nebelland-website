<html>
<head>
<title>Nebelland - UO Freeshard</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="layout.css">
<style type="text/css">
</style>
</head>
<body style="background-image:url(http://www.uonl.de/bg2.jpg); background-repeat:no-repeat; background-attachment:fixed;">
<h1 align="center">Anmeldung</h1>

	<?php

$ICQ = $_POST["ICQ"];    
$Rasse = $_POST["Rasse"];    
$Absenden = $_POST["Absenden"];          
$Datum = date("d.m.Y H:i");

           include("connect.php");

           if(isset($Absenden) && is_numeric($ICQ))
           {
            $sql = "INSERT INTO `anmeldungen` (`ICQ`,`Rasse`,`Datum`) VALUES ('$ICQ','$Rasse','$Datum')";
            $result = mysql_query($sql,$connect) or die(mysql_error());

            echo "Deine Anmeldung wurde gesichert und wird so schnell wie möglich bearbeitet.<br>";

           }
           else
           {

         ?>
<form method="post" action="index.php?s=anmeldung">
Fülle bitte das folgende kleine Formular aus.
Wir werden dann so schnell wie möglich Kontakt über ICQ zwecks eines kleinen Gesprächs aufnehmen. Sollte dies zu beiderseitiger Zufriedenheit verlaufen, wirst du dort deine Account-Daten erhalten. <br>
<br>
Euer Staff
<br>
<br>
<br>
<table border=0>
<tr>
 <td width=200>Meine ICQ-Nummer*</td>
 <td width=80><input type="Text" name="ICQ" value="" size="20" maxlength="10"></td>
</tr>
<tr>
 <td width=200>Rasseninteresse</td>
 <td width=80>
 <select name="Rasse" size="" width="80">
 <option value="Mensch">Mensch</option>
 <option value="Zwerg">Zwerg</option>
 <option value="Nachtelb">Nachtelb</option>
 <option value="Lichtelb">Lichtelb</option>
 </select>


 </td>
</tr>
<tr><td colspan="3">.</td></tr>
<tr>
 <td width=200>
 <input type="Submit" name="Absenden" value="Absenden" style="background-color:#7B79BD;"><input type="reset" style="background-color:#7B79BD; width:100px"></td>
 <td width=80><input type="hidden" name="Datum" size="10"  value="<?php echo $Datum; ?>"></td>
</tr>
</table>
</form>

	<?php
             }
         ?>


</body>
</html>