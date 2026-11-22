<?
$datum = date("d M Y");
$Start = "1.8.2004";

$Differenz = ($datum - $Start)*2;

$Tag = bcmod($Differenz,4);
$Woche = bcmod(floor($Differenz/4),4);
$Monat = bcmod(floor($Differenz/16),11);

$Kalender[0][0] = "Taron"; //Menschen
$Kalender[0][1] = "Argolox"; //Zwerge
$Kalender[0][2] = "Salandrion"; //Lichtelben
$Kalender[0][3] = "Tarsinion"; //Nachtelben
$Kalender[1][0] = "Neumond";
$Kalender[1][1] = "zunehmenden";
$Kalender[1][2] = "Vollmond";
$Kalender[1][3] = "abnehmenden";
$Kalender[2][0] = "Zinkhal";
$Kalender[2][1] = "Svenna";
$Kalender[2][2] = "Inares";
$Kalender[2][3] = "Barkus";
$Kalender[2][4] = "Torbos";
$Kalender[2][5] = "Simian" ;
$Kalender[2][6] = "Firson";
$Kalender[2][7] = "Magan";
$Kalender[2][8] = "Quantan";
$Kalender[2][9] = "Lirsa";
$Kalender[2][10] = "Bragon";

$Ausgabe = Kalender[0][$Tag]." ".$Kalender[1][$Woche]." ".$Kalender[2][$Monat];

echo($Ausgabe);
?>