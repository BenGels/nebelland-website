<?php
/* $Id: editpoll.php,v 1.1 2004/06/13 16:14:28 narodnaja Exp $ */
define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

// http://www.securiteam.com/securitynews/5FP0C204KE.html
for ($i=0; $i<10; $i++)
{
	$newpollanswer[$i] = ((isset($HTTP_POST_VARS['newpollanswer'][$i])) ? $HTTP_POST_VARS['newpollanswer'][$i] : '');
}

if (!isset($pollid))
{
	message ("Fehler","Ung&uuml;ltiger Aufruf");
}
else
{
	if (!isset($Submit))
	{

		$r_poll = thwb_query("SELECT p.*, t.threadauthor,t.threadid, t.threadpollid, t.threadtopic
		FROM ".$pref."poll AS p
		LEFT JOIN ".$pref."thread AS t
		ON t.threadpollid=p.pollid
		WHERE pollid=$pollid");

		$q_poll = mysql_fetch_array($r_poll);

		// verify edit time settings, for shorter line in edit permission creation

		$edittimecheck = verifyedittime();

		// verify edit permission

		if( ($g_user['username'] == $q_poll['threadauthor'] && $P->has_permission( P_EDITOWNPOLL ) && $edittimecheck == "1" ) || $P->has_permission( P_EDITOTHERSPOLL ) )
		{
			if( $P->has_permission( P_EDITTOPIC) )
			{
				$poll['printtopic'] = '<input class="tbinput" type="text" name="newpoll[polltopic]" size="50" value="'.$q_poll['threadtopic'].'" maxlength="75">';
			}
			else
			{
				$poll['printtopic'] = $q_poll['threadtopic'];
			}

			if ($q_poll['pollmultiple'] == "1")
			{
				$poll['polltype'] = "Mehrfachauswahl";
			}
			else
			{
				$poll['polltype'] = "Einfachauswahl";
			}

			if( $config['smilies'] )
			{
				$smilies_on_off = "AN";
			}
			else
			{
				$smilies_on_off = "AUS";
			}

			$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
            $Tform = new Template("templates/" . $style['styletemplate'] . "/editpoll.html");
			$POLLROWS = '';
           	for ($i=1; $i<=$q_poll['pollsize']; $i++)
			{
				$Tpollrow = new Template("templates/" . $style['styletemplate'] . "/editpollrow.html");
				if ($q_poll['pollvotes'] == "0")
				{
					$pollvotecount = "100";
				}
				else
				{
					$pollvotecount = $poll['pollvotes'];
				}
				$pro = round(($q_poll['polla'.$i]/$pollvotecount)*100);
				$con = 100 - $pro;
				$pollbar = '<img src="templates/' . $style['styletemplate'] . '/images/poll/poll_'.$i.'.gif" width="'.$pro.'" height="5"><img src="templates/' . $style['styletemplate'] . '/images/poll/poll_0.gif" width="'.$con.'" height="5">'.$style['smallfont'].' ('.$q_poll['polla'.$i].')'.$style['smallfontend'];

				$newpoll['pollquestion'] = '<input class="tbinput" type="text" name="newpollanswer['.$i.']" size="50" value="'.$q_poll['pollq'.$i].'" maxlength="255">';
				eval($Tpollrow->GetTemplate("POLLROWS"));
			}

			$navpath .= "Umfrage bearbeiten";

			eval($Tform->GetTemplate("CONTENT"));
			eval($Tframe->GetTemplate());
		}
		else
		{
			message("Fehler","Sie haben leider keine Berechtigung, diese Umfrage zu editieren");
		}
	}
	else
	{
		$msg='';

		// strip session id & time
	  	$board_url = str_replace('/', '\/', (($config['board_baseurl'][(strlen($config['board_baseurl']) - 1)] == '/') ? $config['board_baseurl'] : $config['board_baseurl'].'/'));
	  
		for ($i=1;  $i<=$pollsize; $i++)
		{
			if( strlen($newpollanswer[$i]) < $config['pollminlength'] )
			{
				$msg .= "Die Antwort # ".$i." ist zu kurz! (Mind. ".$config['pollminlength']." Zeichen)<br>";
			}
			if( strlen($newpollanswer[$i]) > $config['pollmaxlength'] )
			{
				$msg .= "Die Antwort # ".$i." ist zu lang! (Max. ".$config['pollmaxlength']." Zeichen)<br>";
			}
			
			$newpollanswer[$i] = preg_replace("/(".$board_url."[^ ]+\.php[^ ]*)([?&]s=[^?&]+)/", '$1', $newpollanswer[$i]);
	  		$newpollanswer[$i] = preg_replace("/(".$board_url."[^ ]+\.php[^ ]*)([?&]time=[^?&]+)/", '$1', $newpollanswer[$i]);
			// badwords
			if( $config["usebwordprot"] >= BWORD_POST )
			{
				$newpollanswer[$i] = check_banned($newpollanswer[$i]);
			}
		}

		if( isset ($msg) AND strlen($msg) > 0 )
		{
		message("Fehler", "Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$msg</font>");
		}

		$c_time = time();
		for ($i=1; $i < 11; $i++)
		{
			if (!isset($newpollanswer[$i]))
			{
				$newpollanswer[$i] = '';
			}
		}
		thwb_query("UPDATE ".$pref."poll SET
				pollq1 = '".$newpollanswer[1]."',
				pollq2 = '".$newpollanswer[2]."',
				pollq3 = '".$newpollanswer[3]."',
				pollq4 = '".$newpollanswer[4]."',
				pollq5 = '".$newpollanswer[5]."',
				pollq6 = '".$newpollanswer[6]."',
				pollq7 = '".$newpollanswer[7]."',
				pollq8 = '".$newpollanswer[8]."',
				pollq9 = '".$newpollanswer[9]."',
				pollq10 = '".$newpollanswer[10]."'
				WHERE pollid = '$pollid'");

		// topic updaten, (auch board), nicht als normaluser
		if( $P->has_permission( P_EDITTOPIC ) )
		{
			// badwords
			if( $config['usebwordprot'] == BWORD_TOPIC || $config['usebwordprot'] == BWORD_ALL )
			{
				$newpoll['polltopic'] = check_banned($newpoll['polltopic']);
			}
			
			$newpoll['polltopic'] = addslashes(preparse_code($newpoll['polltopic']));
			
			// topic setten
			thwb_query("UPDATE ".$pref."thread SET threadtopic = '". $newpoll['polltopic'] ."' WHERE threadid = '$threadid'");
			thwb_query("UPDATE ".$pref."poll SET polltitle = '".$newpoll['polltopic']."'");

			// board updaten
			updateboard($thread['boardid']);
		}

		header("Location: showtopic.php?threadid=$threadid&pagenum=lastpage");
	}
}
?>