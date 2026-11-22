<?php
/* $Id: newpoll.php,v 1.1 2004/06/13 16:10:10 narodnaja Exp $ */
define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

// http://www.securiteam.com/securitynews/5FP0C204KE.html
$poll['posttext'] = ((isset($HTTP_POST_VARS['poll'])) ? $HTTP_POST_VARS['poll']['posttext'] : '');

if(!isset($poll['postcode']))
{
  $poll['postcode'] = 0;
}

if(!isset($poll['postsmilies']))
{
  $poll['postsmilies'] = 0;
}

if(!isset($poll['postemailnotify']))
{
  $poll['postemailnotify'] = 0;
}

if( $g_user['userid'] == 0 && !$P->has_permission( P_NEWPOLL ) )
{
	message("Hinweis", "Sie m&uuml;ssen registriert sein, um Umfragen erstellen zu k&ouml;nnen");
}

$bgcolor1 = '';
$bgcolor2 = '';
$checknoicon = '';

requires_permission( P_NEWPOLL, 1);

if( !isset($step) )
{
	$newtopicicons = '';

	$j = 1;
	while( list($iconid, $img) = each($topicicon) )
	{
		$newtopicicons .= "<INPUT type=\"radio\" name=\"poll[polliconid]\" value=\"$iconid\" >
			<img src=\"templates/".$style['styletemplate']."/images/icon/".$img."_new.png\">&nbsp;&nbsp;&nbsp;";
		if( $j % 6 == 0 )
		{
			$newtopicicons .= "<br>";
		}

		$j++;
	}

	if( $config['smilies'] )
	{
		$smilies_on_off = "AN";
	}
	else
	{
		$smilies_on_off = "AUS";
	}

	if( $config['use_email'] )
	{
		$notifyavailable = '';
	}
	else
	{
		$notifyavailable = ' (Derzeit nicht verfügbar)';
	}
//               <input class="tbinput" type="text" name="thread[postguestname]">
	if (($config['pollmultiple'] == "1" AND $g_user['userisadmin'] == "1") OR $config['pollmultiple'] == "3")
	{
		$multiplepollfield = '<tr bgcolor="'.$style['CellB'].'"><td>'.$style['smallfont'].'Art der Umfrage: '.$style['smallfontend'].'</td>
						 <td>'.$style['smallfont'].'<input type="radio" name="pollmultiple" value="0" checked>Einfachauswahl (Benutzer k&ouml;nnen nur eine Antwort ausw&auml;hlen)<br>
						<input type="radio" name="pollmultiple" value="1">Mehrfachauswahl (Benutzer k&ouml;nnen mehrere Antworten ausw&auml;hlen)'.$style['smallfontend'].'</td></tr>';
		$bgcolor1 = $style['CellA'];
		$bgcolor2 = $style['CellB'];
	}
	else if ($config['pollmultiple'] == "2")
	{
		$r_boardmultiple = thwb_query("SELECT boardmultiplepoll FROM ".$pref."board WHERE boardid = $boardid");
		$a_boardmultiple = mysql_fetch_array($r_boardmultiple);
		$boardmultiple = $a_boardmultiple['boardmultiplepoll'];
		if ($boardmultiple == "1")
		{
			$multiplepollfield = '<tr bgcolor="'.$style['CellB'].'"><td>'.$style['smallfont'].'Art der Umfrage: '.$style['smallfontend'].'</td>
						 <td>'.$style['smallfont'].'<input type="radio" name="pollmultiple" value="0" checked>Einfachauswahl (Benutzer k&ouml;nnen nur eine Antwort ausw&auml;hlen)<br>
						<input type="radio" name="pollmultiple" value="1">Mehrfachauswahl (Benutzer k&ouml;nnen mehrere Antworten ausw&auml;hlen)'.$style['smallfontend'].'</td></tr>';
			$bgcolor1 = $style['CellA'];
			$bgcolor2 = $style['CellB'];
		}
	}
	else
	{
		$multiplepollfield = "";
		$pollmultiple = '';
		$bgcolor1 = $style['CellB'];
		$bgcolor2 = $style['CellA'];
	}


	if( $g_user['userid'] )
	{
		$replyusername = "$style[stdfont]$g_user[userhtmlname]$style[stdfontend]$style[smallfont] [ <a href=\"logout.php\">Logout</a> ]$style[smallfontend]";
	}
	else
	{
		if( $g_user['userid'] == 0 && $P->has_permission( P_NEWPOLL ) )
		{
			$replyusername = '<input class="tbinput" name="post[postguestname]" type="text">' . $style['smallfont'] .
			' (Minimal ' . $config['min_usernamelength'] . ', maximal ' . $config['max_usernamelength'] .
			' Zeichen, keine Sonderzeichen)' . $style['smallfontend'];
		}
		else
		{
			$replyusername = '';
		}
	}

	$Tframe= new Template("templates/" . $style['styletemplate'] . "/frame.html");
	$Tform= new Template("templates/" . $style['styletemplate'] . "/newpoll1.html");

	$navpath .= 'Neue Umfrage erstellen';


	if ($config['pollmaxoptions'] > 10) $pollmaxoptions = 10;
	else if ($config['pollmaxoptions'] < 2) $pollmaxoptions = 2;
	else $pollmaxoptions = $config['pollmaxoptions'];

	if ($config['pollminoptions'] > 10) $pollminoptions = 10;
	else if ($config['pollminoptions'] < 2) $pollminoptions = 2;
	else $pollminoptions = $config['pollminoptions'];

	$polloptselect = '<select name="poll[polloptcount]" class="tbselect">';
	for ($i=$pollminoptions; $i<=$pollmaxoptions;$i++)
	{
		if ($i == $pollminoptions) $suffix = " selected"; else $suffix = "";
		$polloptselect .= '<option value="'.$i.'"'.$suffix.'>'.$i.'</option>';
	}
	$polloptselect .= '</select>';

	eval($Tform->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());
}
else if ($step == "1")
{
	$msg = '';
	if (!isset($pollmultiple))
	{
		$pollmultiple = '';
	}
	if( $config['smilies'] )
	{
		$smilies_on_off = "AN";
	}
	else
	{
		$smilies_on_off = "AUS";
	}

	if( $config['use_email'] )
	{
		$notifyavailable = '';
	}
	else
	{
		$notifyavailable = ' (Derzeit nicht verfügbar)';
	}

	if( $g_user['userid'] )
	{
		$replyusername = "$style[stdfont]$g_user[userhtmlname]$style[stdfontend]$style[smallfont] [ <a href=\"logout.php\">Logout</a> ]$style[smallfontend]";
	}
	else
	{
		if( $g_user['userid'] == 0 && $P->has_permission( P_NEWPOLL ) )
		{
			$replyusername = '<input class="tbinput" name="post[postguestname]" type="text" value="'.$post[postguestname].'">' . $style['smallfont'] .
			' (Minimal ' . $config['min_usernamelength'] . ', maximal ' . $config['max_usernamelength'] .
			' Zeichen, keine Sonderzeichen)' . $style['smallfontend'];
		}
		else
		{
			$replyusername = '';
		}
	}

	if( strlen($poll['polltopic']) > $config['subject_maxlength'] )
	{
		$msg .= "Das Subject ist zu lang!<br>";
	}
	if( strlen(preparse_code($poll['polltopic'])) < $config['subject_minlength'] )
	{
		$msg .= "Das Subject ist zu kurz!<br>";
	}
	$teststring = parse_code(preparse_code($poll['posttext']), 1, ($config['imageslevel'] ? 0 : 1), $post['postcode'], $post['postsmilies']);
	if( strlen(strip_tags($teststring)) < $config['message_minlength'] )
	{
		$msg .= "Der Text ist zu kurz<br>";
	}
	if( strlen(strip_tags($teststring)) > $config['message_maxlength'] )
	{
		$msg .= "Der Text ist zu lang<br>";
	}

	if( strlen($msg) > 0 )
	{
		$navpath .= 'Neue Umfrage';
		message("Fehler","Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$msg</font>");
	}

	$time = time();

	$Tframe= new Template("templates/" . $style['styletemplate'] . "/frame.html");
	$Tform= new Template("templates/" . $style['styletemplate'] . "/newpoll2.html");

	// pollicon weitergeben

	$newtopicicons = '';
	$j = 1;
	while( list($iconid, $img) = each($topicicon) )
	{
		if (intval($poll['polliconid']) == "0")
		{
			$checknoicon = "checked";
			$newtopicicons .= "<INPUT type=\"radio\" name=\"poll[polliconid]\" value=\"$iconid\" >
				<img src=\"templates/".$style['styletemplate']."/images/icon/".$img."_new.png\">&nbsp;&nbsp;&nbsp;";
		}
		else 
		{
			if ($j-1 == intval($poll['polliconid']))
			{
				$newtopicicons .= "<INPUT type=\"radio\" name=\"poll[polliconid]\" value=\"$iconid\" checked>
				<img src=\"templates/".$style['styletemplate']."/images/icon/".$img."_new.png\">&nbsp;&nbsp;&nbsp;";
			}
			else
			{
				$newtopicicons .= "<INPUT type=\"radio\" name=\"poll[polliconid]\" value=\"$iconid\" >
				<img src=\"templates/".$style['styletemplate']."/images/icon/".$img."_new.png\">&nbsp;&nbsp;&nbsp;";
			}
			$checknoicon = "";
		}
		if( $j % 6 == 0 )
		{
			$newtopicicons .= "<br>";
		}

		$j++;
	}
	// $poll[polloptcount] von 2 - 10
	$polltable = '';
	for ($i=0; $i<$poll['polloptcount']; $i++)
	{
		if ($i % 2 == 0)
		{
			$bg = "$style[CellA]";
		}
		else
		{
			$bg = "$style[CellB]";
		}
		$polltable .= '<tr bgcolor="'.$bg.'">
            <td valign="top" align="right"><img src="templates/' . $style['styletemplate'] . '/images/poll/poll_'.($i+1).'.gif" width="40" height="8">&nbsp;'.$style['stdfont'].'Antwort Nr. '.($i+1).' - '.$style['stdfontend'].'
            </td>
            <td valign="top"><input name="poll[pollopt]['.$i.']" type="text" class="tbinput" size="75" maxlength="75"></td>
          </tr>';
	}
	if ($poll['polllimittime'] == "0")
	{
		$pollendtime = "unbeschr&auml;nkt";
	}
	else
	{
		$polllimitsec = $poll['polllimittime']*24*60*60;
		$polllimittime = time()+$polllimitsec;
		$pollendtime = form_date($polllimittime).'<input type="hidden" name="poll[polllimittime]" value="'.$polllimittime.'">';
	}

	$navpath .= 'Neue Umfrage erstellen';

	if (isset($post['postsmilies'])) { $smileycheck = " checked"; } else { $smileycheck = "";}
	if (isset($post['postemailnotify'])) { $emailcheck = " checked"; } else { $emailcheck = "";}
	if (isset($post['postcode'])) { $codecheck = " checked"; } else { $codecheck = "";}

	eval($Tform->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());
}

else if ($step == "2")

{
	// strip session id & time
  	$board_url = str_replace('/', '\/', (($config['board_baseurl'][(strlen($config['board_baseurl']) - 1)] == '/') ? $config['board_baseurl'] : $config['board_baseurl'].'/'));
  	$poll['posttext'] = preg_replace("/(".$board_url."[^ ]+\.php[^ ]*)([?&]s=[^?&]+)/", '$1', $poll['posttext']);
  	$poll['posttext'] = preg_replace("/(".$board_url."[^ ]+\.php[^ ]*)([?&]time=[^?&]+)/", '$1', $poll['posttext']);

	$msg = '';
	if( strlen($poll['polltopic']) > $config['subject_maxlength'] )
	{
		$msg .= "Das Subject ist zu lang!<br>";
	}
	if( strlen(preparse_code($poll['polltopic'])) < $config['subject_minlength'] )
	{
		$msg .= "Das Subject ist zu kurz!<br>";
	}
  
  	$testlen = strlen(preg_replace("/(\s+|(&#032;)+)/", '', strip_tags(parse_code(preparse_code($poll['posttext']), 1, ($config['imageslevel'] ? 0 : 1), $post['postcode'], $post['postsmilies']))));
  	
	if( $testlen < $config['message_minlength'] )
    {
      $msg .= "Der Text ist zu kurz<br>";
    }
  	
	if( $testlen > $config['message_maxlength'] )
    {
      $msg .= "Der Text ist zu lang<br>";
    }

	for ($i=0;  $i<$poll['polloptcount']; $i++)
	{
		$testlen_poll[$i] = strlen(preg_replace("/(\s+|(&#032;)+)/", '', strip_tags(parse_code(preparse_code($poll['pollopt'][$i]), 1, ($config['imageslevel'] ? 0 : 1), $post['postcode'], $post['postsmilies']))));
		if( $testlen_poll[$i] < $config['pollminlength'] )
		{
			$msg .= "Die Antwort # ".($i+1)." ist zu kurz! (Mind. ".$config['pollminlength']." Zeichen)<br>";
		}
		if( $testlen_poll[$i] > $config['pollmaxlength'] )
		{
			$msg .= "Die Antwort # ".($i+1)." ist zu lang! (Max. ".$config['pollmaxlength']." Zeichen)<br>";
		}
	}

	if( strlen($msg) > 0 )
	{
		$navpath .= 'Neue Umfrage';
		message("Fehler","Es sind leider folgende Fehler aufgetreten:<br><br><font color='$style[color_err]'>$msg</font>");
	}

	if( !$P->has_permission( P_NOFLOODPROT ) && time() - $config['postdelay'] < $g_user['userlastpost'] )
	{
		$navpath .= 'Neue Umfrage';
		message("Fehler", "Sie können nur alle $config[postdelay] Sekunden eine neue Umfrage erstellen.");
	}

	$time = time();

	(isset($post['postemailnotify']) && $post['postemailnotify']) ? $post['postemailnotify'] = 1 : $post['postemailnotify'] = 0;

 	if( !isset($poll['polliconid']) || !$topicicon[($poll['polliconid'])] )
    {
      $poll['polliconid'] = 0;
    }

	if( $P->has_permission( P_NEWPOLL ) && ($g_user['userid'] == 0) )
	{
		// check username
		check_username($post['postguestname']);

		// override notify
		$post['postemailnotify'] = 0;

		$g_user['username'] = $config['guestprefix'] . $post['postguestname'];
		$post['postguestname'] = $config['guestprefix'] . addslashes($post['postguestname']);
	}
	else
	{
		$post['postguestname'] = '';
	}

	if( $config['uppercase_prot'] )
	{
		$words = explode(' ', $poll['polltopic']);
		$newwords = array();

		while( list(, $word) = @each($words) )
		{
			if( strlen($word) > 1 )
			{
				$newwords[] = substr($word, 0, 1) . strtolower(substr($word, 1));
			}
			else
			{
				$newwords[] = $word;
			}
		}

		$poll['polltopic'] = implode(' ', $newwords);
	}
	// Bannedwords-Protection
	if( $config["usebwordprot"] == BWORD_TOPIC || $config["usebwordprot"] == BWORD_ALL )
	{
		$poll["polltopic"] = check_banned($poll["polltopic"]);
	}
	if( $config["usebwordprot"] >= BWORD_POST )
	{
		$poll["posttext"] = check_banned($poll["posttext"]);
	}

	for ($i=0;  $i<$poll['polloptcount']; $i++)
	{
		if( $config["usebwordprot"] >= BWORD_POST )
		{
			$poll['pollopt'][$i] = check_banned($poll['pollopt'][$i]);
		}
	}

	 // Autoclose & delete
  	if ( isset($config['auto_close']) && $config['auto_close'] > 0 )
    {
      thwb_query("UPDATE  " . $pref . "thread SET threadclosed = '1' WHERE threadtime < '" . ( time() - ( ( $config['auto_close'] + 1 ) * 86400 ) ) . "'");
    }
 	 if ( isset($config['auto_delete']) && $config['auto_delete'] > 0 )
    {
      thwb_query("DELETE FROM " . $pref . "thread WHERE threadtime < " . ( time() - ( $config['auto_delete'] * 86400 ) ) . "");
    }
 
	// als erstes Poll in poll table speichern
	
	// vars setzen, falls nicht gesetzt
	if (!isset($poll['polllimittime']))
	{
		$poll['polllimittime'] = '0';
	}
	for ($i=0; $i < 10; $i++)
	{
		if (!isset($poll['pollopt'][$i]))
		{
			$poll['pollopt'][$i] = '';
		}
	}
	
	thwb_query ("INSERT INTO ".$pref."poll (polltitle, pollend, pollsize, pollmultiple, pollq1, pollq2, pollq3, pollq4, pollq5, pollq6, pollq7, pollq8, pollq9, pollq10)
				VALUES ('".addslashes(preparse_code($poll['polltopic']))."',
				'".$poll['polllimittime']."',
				'".$poll['polloptcount']."',
				'".$pollmultiple."',
				'".addslashes(preparse_code($poll['pollopt'][0]))."',
				'".addslashes(preparse_code($poll['pollopt'][1]))."',
				'".addslashes(preparse_code($poll['pollopt'][2]))."',
				'".addslashes(preparse_code($poll['pollopt'][3]))."',
				'".addslashes(preparse_code($poll['pollopt'][4]))."',
				'".addslashes(preparse_code($poll['pollopt'][5]))."',
				'".addslashes(preparse_code($poll['pollopt'][6]))."',
				'".addslashes(preparse_code($poll['pollopt'][7]))."',
				'".addslashes(preparse_code($poll['pollopt'][8]))."',
				'".addslashes(preparse_code($poll['pollopt'][9]))."')");

	$pollid = mysql_insert_id();

	// die neue nachricht abspeichern in dem topics table
	thwb_query("INSERT INTO ".$pref."thread (threadtime, threadtopic, threadpollid, threadauthor, boardid,
		threadlastreplyby, threadiconid, threadcreationtime)
		VALUES('$time',
		'".addslashes(preparse_code($poll['polltopic']))."',
		'$pollid',
		'".addslashes($g_user['username'])."',
		'$board[boardid]',
		'".addslashes($g_user['username'])."',
		'".intval($poll['polliconid'])."',
		'$time')");

	$thread['threadid'] = mysql_insert_id();

	// die neue nachricht abspeichern in dem messages table
	thwb_query("INSERT INTO ".$pref."post (posttime, posttext, userid, threadid, postemailnotify, postip, postsmilies,
		postcode, postguestname)
		VALUES('$time',
		'" . addslashes(preparse_code($poll['posttext'])) . "',
		'$g_user[userid]',
		'$thread[threadid]',
		'$post[postemailnotify]',
		'".addslashes($REMOTE_ADDR)."',
		'" . ($post['postsmilies'] ? 1 : 0) . "',
		'" . ($post['postcode'] ? 1 : 0) . "',
		'" . addslashes($post['postguestname']) . "')");

	// Den topics-count-wert des board erh&ouml;hen
	thwb_query("UPDATE ".$pref."board SET
	boardthreads=boardthreads+1,
	boardlastpost='$time',
	boardposts=boardposts+1,
	boardlastpostby='" . addslashes($g_user['username']) . "',
	boardthreadtopic='" . addslashes($poll['polltopic']) . "',
	boardthreadid=$thread[threadid]
		WHERE boardid='$board[boardid]'");

	if( $g_user['userid'] )
	{
		// Den postings wert des postenden users erh&ouml;hen
		thwb_query("UPDATE ".$pref."user SET userlastpost=$time, userposts=userposts+1 WHERE userid='$g_user[userid]'");
	}

	header("Location: showtopic.php?threadid=$thread[threadid]");
}

?>