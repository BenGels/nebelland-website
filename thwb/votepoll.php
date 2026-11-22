<?php
/* $Id: pollvote.php,v 0.1 2003/01/08 23:35:01 Narodnaja Exp $ */
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000, 2001 by
               Paul Baecher         <paul@thewall.de>
               Felix Gonschorek   <funner@thewall.de>

          download the latest version:
            http://www.thwboard.de

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================

*/

include "./inc/header.inc.php";

if (!isset($pollsubmit) OR !isset($pollid) OR $pollopt != $_POST['pollopt'])
{
	message ("Fehler","Ung&uuml;ltiger Aufruf");
}
else
{
	requires_permission( P_PARTICIPATEPOLL );

	$participants = getvoteid($pollid);
	if ($g_user['userid'] != "0" AND (in_array($g_user['userid'],$participants))) // NEUNEUNEU
	{
		message("Fehler","Sie haben an dieser Umfrage schon teilgenommen.");
	}

	if ($g_user['userid'])
    {
    	$poll['pollusers'] .= $g_user['userid']."@".$g_user['username']."@@";
    }

	if ($type == "single")
	{
		$msg = "Single Poll";
		if ($pollopt[0] == "")
		{
			message("Fehler","Du hast keine Auswahl getroffen.");
		}
		$pollwhich = "polla".$pollopt[0];

		$vote = thwb_query ("UPDATE ".$pref."poll SET ".$pollwhich." = ".$pollwhich." + 1, pollusers = '".$poll['pollusers']."', pollvotes = pollvotes + 1
					WHERE pollid = '".$pollid."'");

	}
	else if ($type == "multi")
	{
		$msg = "Multiple Poll";
		$anzahl = sizeof($pollopt);

		if ($anzahl == "0")
		{
			message("Fehler","Sie haben keine Auswahl getroffen.");
		}

		for ($i=1; $i<=10; $i++)
		{
			if (!isset($pollopt[$i]))
			{
				$plus[$i] = "0";
			}
			else
			{
				$plus[$i] = "1";
			}
		}

		

		$vote = thwb_query("UPDATE ".$pref."poll SET
		polla1 = polla1 + ".$plus[1].",
		polla2 = polla2 + ".$plus[2].",
		polla3 = polla3 + ".$plus[3].",
		polla4 = polla4 + ".$plus[4].",
		polla5 = polla5 + ".$plus[5].",
		polla6 = polla6 + ".$plus[6].",
		polla7 = polla7 + ".$plus[7].",
		polla8 = polla8 + ".$plus[8].",
		polla9 = polla9 + ".$plus[9].",
		polla10 = polla10 + ".$plus[10].",
		pollusers = '".$poll['pollusers']."',
		pollvotes = pollvotes + 1
		WHERE pollid = '".$pollid."'");
	}

	if ($vote)
	{
		message_redirect("Sie haben an der Abstimmung erfolgreich teilgenommen", "showtopic.php?threadid=".$threadid);
	}
	else
	{
		message_redirect("Leider kam es w&auml;hrend der Abstimmung zu einem Fehler.<br><br>Bitte versuchen Sie es nocheinmal oder nehmen Sie Kontakt mit dem Admininstrator auf.", "showtopic.php?threadid=".$threadid);
	}
}

?>