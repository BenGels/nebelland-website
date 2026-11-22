<html>
<head>
<title>Nebelland - Der UltimaOnline-FreeShard</title>
<link rel=stylesheet type="text/css" href="style.css">
<?php
$s = $_GET["s"];
if($s == "status"){echo "<meta http-equiv=\"refresh\" content=\"300\">"; }
?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-2379081-1";
urchinTracker();
</script>

<table border=0 width="100%" height="100%" cellpadding="0" cellspacing="0">
<tr>
<td colspan=3 style="background-image: url(header.jpg); background-position: 0cm 0cm; background-repeat: no-repeat" height="120px" valign="top" align="right">
<p align="right">
<b><a href="http://www.nebelland.com/?s=status">Status</a> - <a href="http://forum.nebelland.com/">forum.nebelland.com</a></b></p>
<TABLE>
<tr><td><p>Spieler auf dem Server:</p></td><td>

</td></tr>
</TABLE>
</td>
</tr>
<tr>
<td valign="top" width="130">
<div style="border: 1px solid gray; padding: 5px; margin-top: 20px; background-image: url(01.gif); background-position: bottom center" valign="top" width="100%">
<center><b>Home</b></center>
<li><a href="?s=">News</a></li>
<li><a href="?s=nebelland">Nebelland?</a></li>
<li><a href="?s=anmeldung">Anmeldung</a></li>
<center>-</center>
<li><a href="?s=regeln">Regeln</a></li>
<li><a href="?s=faq">FAQ</a></li>
<li><a href="http://forum.nebelland.com" target="_blank">Forum</a></li>
<li><a href="?s=files">Files</a></li>
<center>-</center>
<li><a href="?s=staff">Staff</a></li>
<li><a href="./staff/" target="_blank">Staffintern</a></li>
<center>-</center>
<li><a href="?s=shop">Shop</a></li>
<li><a href="?s=impressum">Impressum</a></li>
<li>RL-Galerie</li>
<li><a href="?s=Buch">Bibliothek</a></li>

<a><br><b>IRC</b><br>Chan: </a><a href="irc://irc.gamesurge.net/nebelland">#Nebelland</a><a><br>auf </a></a><a href="http://www.gamesurge.net/servers/" target="_blank">Gamesurge</a>
</div>

<td valign="top" align="left" style="padding: 10px" height="100%">
<?php
switch($s){
	case "staffintern":
		echo "<iframe src=\"/nela/staff/index.html\" id=\"frame\" width=\"100%\" height=\"100%\" frameborder=\"0\">";
		echo "<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen.<br></p>";
		echo "</iframe>";
		break;
	case "board":
		echo "<iframe src=\"/thwb/index.php\" id=\"frame\" width=\"100%\" height=\"100%\" frameborder=\"0\">";
		echo "<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen.<br></p>";
		echo "</iframe>";
		break;
	case "statusstats":
		include("./sub/statusstats.html");
		break;
	case "zauber":
		include("./sub/zauber.html");
		break;
	case "skills":
		include("./sub/skills.html");
		break;
	case "status":
		include("C:/server/apache/xampp/htdocs/status.html");
		echo "<div align=center><br><br><img src=\"bild.php\"></div>";
		break;
	case "anmeldung":
		include("./sub/anmeldung.html");
		break;
	case "nebelland":
		include("./sub/nebelland.html");
		break;
	case "links":
		include("./sub/links.html");
		break;
	case "impressum":
		include("./sub/impressum.html");
		break;
	case "faq":
		include("./sub/faq.html");
		break;
	case "regeln":
		include("./sub/regeln.html");
		break;
	case "files":
		include("./sub/files.html");
		break;
	case "staff":
		include("./sub/staffi.html");
		break;
	case "1schritte":
		include("./sub/1schritte.html");
		break;
	case "gesch":
		include("./sub/gesch.html");
		break;
	case "rassen":
		include("./sub/rassen.html");
		break;
	case "regionen":
		include("./sub/regionen.html");
		break;
	case "rasse_mensch":
		include("./sub/rassen/mensch.html");
		break;
	case "rasse_zwerg":
		include("./sub/rassen/zwerg.html");
		break;
	case "rasse_lichtelb":
		include("./sub/rassen/lichtelb.html");
		break;
	case "rasse_nachtelb":
		include("./sub/rassen/nachtelb.html");
		break;
	case "rasse_vamp":
		include("./sub/rassen/vampire.html");
		break;
	case "klassen":
		include("./sub/klassen.html");
		break;
	case "klasse_krieger":
		include("./sub/klassen/krieger.html");
		break;
	case "klasse_handwerker":
		include("./sub/klassen/handwerker.html");
		break;
	case "klasse_magier":
		include("./sub/klassen/magier.html");
		break;
	case "staedte":
		include("./sub/staedte.html");
		break;
	case "stadt_3bruecken":
		include("./sub/staedte/3bruecken.html");
		break;
	case "gesetz_3bruecken":
		include("./sub/staedte/gesetze/3bruecken.html");
		break;
	case "stadt_gadarosch":
		include("./sub/staedte/gadarosch.html");
		break;
	case "stadt_morgenlicht":
		include("./sub/staedte/morgenlicht.html");
		break;
	case "stadt_halbmond":
		include("./sub/staedte/halbmond.html");
		break;
	case "goetter":
		include("./sub/goetter.html");
		break;
	case "stats":
		include("./sub/stats.html");
		break;
	case "magie":
		include("./sub/magie.html");
		break;
	case "quests":
		include("./sub/quests.html");
		break;
	case "gilden":
		include("./sub/gilden.html");
		break;
	case "todo":
		include("./sub/todo.html");
		break;
//	case "skills":
//		include("http://127.0.0.1/cgi-bin/skills.pl");
//		break;
	case "haus":
		include("./sub/haus.html");
		break;
	case "final":
		include("./sub/final.html");
		break;
	case "shop":
		echo "<iframe height=\"500\" width=\"700\" src=\"http://www.epresso.com/shops/frame.php?shopId=9228\" name=\"epressoShop\" id=\"epressoShop\" frameborder=\"0\"></iframe>";
		break;
	case "ewiger":
		include("./sub/ewigerstatus.php");
		break;
	case "Buch":
		include("./sub/Buch/buch.php");
		break;
         case "geister_svenna":
                 include("./sub/geister/svenna.html");
		break;
         case "geister_inares":
                 include("./sub/geister/inares.html");
		break;
         case "geister_barkus":
                 include("./sub/geister/barkus.html");
		break;
         case "geister_torbos":
                 include("./sub/geister/torbos.html");
		break;
         case "geister_firson":
                 include("./sub/geister/firson.html");
		break;
         case "geister_simian":
                 include("./sub/geister/simian.html");
		break;
         case "geister_magan":
                 include("./sub/geister/magan.html");
		break;
         case "geister_quatan":
                 include("./sub/geister/quatan.html");
		break;
         case "geister_lirsa":
                 include("./sub/geister/lirsa.html");
		break;
	default:
		include("./sub/news.html");
		break;
}
?>
</td>
<td valign="top" width="130">
<div style="border: 1px solid gray; padding: 5px; margin-top: 20px; background-image: url(02.gif); background-position: top center" valign="top">
<center><b>Guides</b></center>
<li><a href="?s=1schritte">Erste Schritte...</a></li>
<center>-</center>
<li><a href="?s=gesch">Geschichte</a></li>
<center>-</center>
<li><a href="?s=rassen">Rassen</a></li>
<li><a href="?s=klassen">Klassen</a></li>
<li><a href="?s=staedte">Städte</a></li>
<li><a href="?s=gilden">Gilden</a></li>
<li><a href="?s=regionen">Regionen</a></li>
<li><a href="?s=goetter">Götter</a></li>
<center>-</center>
<li><a href="?s=stats">Stats</a></li>
<li><a href="?s=magie">Magie</a></li>
</div>
<br>
<div style="border: 1px solid gray; padding: 5px; background-image: url(02.gif); background-position: bottom center" valign="top">
<center><b>Status</b></center>
<li><a href="?s=status">Serverstatus</a></li>
<li><a href="?s=ewiger">Ewiger Status</a></li>
<li><a href="?s=final">Final</a></li>
</div>
</td>
</tr>
</table>

</body>
</html>
