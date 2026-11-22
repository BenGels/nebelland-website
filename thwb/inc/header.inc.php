<?php
/* $Id: header.inc.php,v 1.46 2004/01/09 20:21:05 thed_o_n Exp $ */
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

error_reporting(0);

/**
 * as of php5, $HTTP_*_VARS are disabled
 * so we have to recreate them here
 * 
 * this is actually pretty evil, but it does work.
 **/

if(substr(phpversion(), 0, 1) > 4)
{
    $a_globals = array(
        'HTTP_SERVER_VARS' => '_SERVER', 
        'HTTP_COOKIE_VARS' => '_COOKIE', 
        'HTTP_POST_VARS' => '_POST',
        'HTTP_GET_VARS' => '_GET',
        'HTTP_ENV_VARS' => '_ENV'
        );

    foreach($a_globals as $k => $v)
    {
        global $$k;

        $$k = $$v;
    }

    unset($a_globals);
}

if(!empty($HTTP_SERVER_VARS['REQUEST_URI']) && (strstr($HTTP_SERVER_VARS['REQUEST_URI'], "header.inc.php")))
{
  die();
}

if( !@include('./inc/config.inc.php') )
{
	print 'Das Forum ist noch nicht installiert! Klicken Sie <a href="./admin/install.php">hier</a>, um mit der Installation zu beginnen.';
	exit;
}

include('./inc/functions.inc.php');
include('./inc/thwbcode.inc.php');

if( !$pref )
	$pref = 'thwb_';

	//error_reporting(7); // E_ERROR | E_WARNING | E_PARSE
set_magic_quotes_runtime(0);

// php 4.1+
if( !empty($_REQUEST) )
	extract($_REQUEST, EXTR_SKIP);

if( get_magic_quotes_gpc() )
{
	$HTTP_GET_VARS = r_stripslashes($HTTP_GET_VARS);
	$HTTP_POST_VARS = r_stripslashes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = r_stripslashes($HTTP_COOKIE_VARS);
	$GLOBALS = r_stripslashes($GLOBALS);
}

// most apps (icq, outlook) treat urls containing [ or ] incorrectly. thus,
// we have to acceppt threadid, postid, boardid, categoryid if set.
if( isset($threadid) )
	$thread['threadid'] = $threadid;
if( isset($boardid) )
	$board['boardid'] = $boardid;
if( isset($postid) )
	$post['postid'] = $postid;
if( isset($categoryid) )
	$category['categoryid'] = $categoryid;
if( isset($userid) )
	$user['userid'] = $userid;

if( !($REMOTE_ADDR = @getenv('REMOTE_ADDR')) )
	$REMOTE_ADDR = @getenv('HTTP_X_FORWARDED_FOR');

$debug = '';
$navpath = '';
$navigation = array();
$config = array();
$option = array();
if( !isset( $time ) )
	$time = ''; // leerstring, weil eine 0 in der URL nicht so toll ist

/*
################################################################################
            code start
################################################################################
*/

$mysql = @mysql_connect($mysql_h, $mysql_u, $mysql_p);

$db = @mysql_select_db($mysql_db);

$mysql_h = ''; $mysql_u = ''; $mysql_p = ''; $mysql_db = '';

if(!$mysql || !$db )
{
	print '<b>Sorry</b><br><br>Es gibt momentan leider ein kleines Datenbank-Problem, bitte versuche es sp&#xE4;ter noch einmal.';
    ob_end_flush();
	exit;
}

define('P_VIEW', 0);
define('P_REPLY', 1);
define('P_POSTNEW', 2);
define('P_CLOSE', 3);
define('P_DELTHREAD', 4);
define('P_OMOVE', 5);
define('P_DELPOST', 6);
define('P_EDIT', 7);
define('P_OCLOSE', 8);
define('P_ODELTHREAD', 9);
define('P_ODELPOST', 10);
define('P_OEDIT', 11);
define('P_TOP', 12);
define('P_EDITCLOSED', 13);
define('P_IP', 14);
define('P_EDITTOPIC', 15);
define('P_NOFLOODPROT', 16);
define('P_NOEDITLIMIT', 17);
define('P_CANSEEINVIS', 18);
define('P_NOPMLIMIT', 19);
define('P_INTEAM', 20);
define('P_CEVENT', 21);

/* note: flags 0 - 35 are reserved for thwb use */
// POLLHACK S
define('P_NEWPOLL',36);
define('P_PARTICIPATEPOLL',37);
define('P_EDITOWNPOLL',38);
define('P_EDITOTHERSPOLL',39);
define('P_CLOSEOWNPOLL',40);
define('P_CLOSEOTHERSPOLL',41);
// POLLHACK E
define("BWORD_NONE", 0);
define("BWORD_TOPIC", 1);
define("BWORD_POST", 2);
define("BWORD_ALL", 3);

/*
################################################################################
            Creating config array
################################################################################
*/

$r_registry = thwb_query("SELECT keyname, keyvalue, keytype FROM " . $pref . "registry");
while ( $registry = mysql_fetch_array($r_registry) )
{
	switch( $registry['keytype'] )
	{
		case 'integer':
		case 'boolean':
			$config[$registry['keyname']] = intval($registry['keyvalue']);
			break;
			
		case 'array':
			if( $registry['keyvalue'] )
			{
				$array = explode("\n", $registry['keyvalue']);
				while( list($k, $v) = @each($array) )
					$array[$k] = '"'.addslashes(trim($v)).'"';
				eval("\$config[\$registry['keyname']] = array(".implode(',', $array).");");
			}
			break;
				
		default:
			$config[$registry['keyname']] = $registry['keyvalue'];
	}
}

/*
################################################################################
            verify cookie / session data and create g_user[]
################################################################################
*/

$have_session = false;
$thwb_cookie = '';

$uri = $HTTP_SERVER_VARS['REQUEST_URI'];
if( !stristr( $uri, 'login.php' ) && !stristr( $uri, 'do_register.php' ) && !stristr( $uri, 'misc.php') )
{
  if( substr($uri, -1, 1) != '/' )
    $path = urlencode(preg_replace("/(&|&amp;|\?)s=([a-zA-Z0-9]+)/", '', basename($HTTP_SERVER_VARS['REQUEST_URI'])));
  else
    $path = '';
}
else
{
  $path = '';
}

if(isset($HTTP_COOKIE_VARS['thwb_sid']))
{
  $s = $HTTP_COOKIE_VARS['thwb_sid'];
  $have_session_cookie = true;
}
else
{
  $have_session_cookie = false;
}

$is_guest = false;

if(isset($s) && $s)
{
  $r_session = thwb_query("SELECT o.userid, o.onlineip, o.onlinetime, u.userpassword FROM ".$pref."online AS o LEFT OUTER JOIN ".$pref."user AS u ON o.userid=u.userid WHERE o.sessionid='".addslashes($s)."' ORDER BY o.onlinetime DESC LIMIT 1");

  if(!mysql_num_rows($r_session))
    {
      // invalid session id
      if( defined('THWB_NOSESSION_PAGE'))
	{
	if(isset( $HTTP_COOKIE_VARS['thwb_cookie'] ) )
	  {
	    $thwb_cookie = $HTTP_COOKIE_VARS['thwb_cookie'];
	  }
	else
	  {
	    $is_guest = true;
	  }
	}
      else
	{
	  message("Fehler", "Diese Session-ID ist ungültig.");
	}
    }
  else
    {
      $a_session = mysql_fetch_array($r_session);

      /**
       * use just the first 24 bytes of the ip for comparing.
       * this _should_ avoid problems with aol and other proxies.
       **/

      if(substr(dechex(ip2long($a_session['onlineip'])), 0, 6) != substr(dechex(ip2long($HTTP_SERVER_VARS['REMOTE_ADDR'])), 0, 6))
	{
	  // ip mismatch
	  if(!isset($HTTP_COOKIE_VARS['thwb_cookie']))
	    {
	      $is_guest = true;
	    }
	  elseif(!defined('THWB_NOSESSION_PAGE'))
	    {
	      message("IP Mismatch", "Diese Session-ID ist an eine andere IP gebunden.<br>Klicken Sie <a href=\"".build_link("login.php?source=".$path)."\">hier</a> um sich einzuloggen.");      
	    }
	  else
	    {
	      $thwb_cookie = $HTTP_COOKIE_VARS['thwb_cookie'];
	    }
	}
      else
	{
	  if(!$is_guest && $a_session['onlinetime'] >= (time() - $config['session_timeout']))
	    {
	      $thwb_cookie = $a_session['userpassword'] . $a_session['userid'];
	      $have_session = true;
	    }
	  else
	    {
	      // timed out
	      thwb_query("DELETE FROM ".$pref."online WHERE sessionid='".addslashes($s)."'");
	  
	      message("Timeout", "Sie wurden automatisch ausgeloggt, weil Ihre Session-ID abgelaufen ist. <br>Bitte <a href=\"".build_link("login.php?source=".$path)."\">loggen</a> Sie sich neu ein.");
	    }
	}
    }
}
elseif(defined('THWB_NOSESSION_PAGE') && !$is_guest)
{
  if( isset( $HTTP_COOKIE_VARS['thwb_cookie'] ) )
    {
      $thwb_cookie = $HTTP_COOKIE_VARS['thwb_cookie'];
    }
  else
    {
      $is_guest = true;
    }
}

$thwb_cookie_userid = substr($thwb_cookie, 32);
$thwb_cookie_userpassword = substr($thwb_cookie, 0, 32);

$r_user = thwb_query("SELECT username, useremail, userid, userpassword, userhidesig,
	userbanned, userisadmin, userlastpost, usernoding, styleid, groupids FROM ".$pref."user
	WHERE userid='".intval($thwb_cookie_userid)."'");
$g_user = mysql_fetch_array($r_user);

if(!isset($g_user['userpassword']))
{
  $g_user['userpassword'] = '';
}

if($g_user['userisadmin'])
{
  error_reporting(E_ALL);
}

if( !$have_session && $thwb_cookie_userid && isset($g_user['userpassword']) && $thwb_cookie_userpassword == $g_user['userpassword'] )
{
  // we don't have a session, but we do have a cookie
  $s = md5(microtime().uniqid(microtime()));

  thwb_query("INSERT INTO ".$pref."online (onlineip, onlinetime, userid, sessionid)
	VALUES ('" . $HTTP_SERVER_VARS['REMOTE_ADDR'] . "', '" . time() . "', '" . $g_user['userid'] . "', '" . addslashes($s) ."')");
}

unset($have_session);

if( $thwb_cookie_userid && $thwb_cookie_userpassword != $g_user['userpassword'] || mysql_num_rows($r_user) < 1 || $is_guest)
{
        unset($is_guest);
	unset($g_user);

	$g_user['userid'] = 0;
	$g_user['groupids'] = $config['guest_groupid'];
	$g_user['username'] = "Gast";
	$g_user['userisadmin'] = 0;
	$g_user['userbanned'] = 0;
	$g_user['issession'] = false;
	$g_user['userhidesig'] = false;

	$option[] = '<a href="register.php">Registrieren</a>';
	$option[] = '<a href="login.php?source='.$path.'">Einloggen</a>';
}
else
{
	$option[] = '<a href="'.build_link("pm.php").'">Private Messages</a>';
	$option[] = '<a href="'.build_link("editprofile.php").'">Profil</a>';
	$g_user['groupids'] = substr($g_user['groupids'], 1, strlen($g_user['groupids']) - 2);

	if( !$g_user['userbanned'] )
	{
		$option[] = '<a href="'.build_link("logout.php").'">Logout</a>';
	}
}
$g_user['userhtmlname'] = parse_code($g_user['username']);

// online

if(($g_user['userid'] != 0) && (!empty($s)))
{
  thwb_query("INSERT INTO ".$pref."online (onlineip, onlinetime, userid, sessionid)
	VALUES ('" . $HTTP_SERVER_VARS['REMOTE_ADDR'] . "', '" . time() . "', '" . $g_user['userid'] . "', '" . addslashes($s) ."');");
}
else
{
  thwb_query("INSERT INTO ".$pref."online (onlineip, onlinetime, userid)
	VALUES ('" . $HTTP_SERVER_VARS['REMOTE_ADDR'] . "', '" . time() . "', '" . $g_user['userid'] . "');");
}

if( time() % 60 > 55 )
{
	// delete some old records
	thwb_query("DELETE FROM ".$pref."online WHERE onlinetime<" . (time() - 300));
}

if( $config['forumclosed'] && !$g_user['userisadmin'] )
{
	$navpath = 'Forum geschlossen!';
	//ttt: keine smilies weil das style noch nicht geladen ist
	message('Forum geschlossen', parse_code(stripslashes(implode("\n", $config['closedmsg'])), 1, 1, 1, 0 ));
}

// has to be moved here in order to work with session --theDon

if( isset($board['boardid']) && $board['boardid'] < 0 )
{
	header('Location: '.build_link('index.php?categoryid='.(intval($board['boardid']) * -1), true));
	exit;
}


/*
################################################################################
            create head options [ register ] [ ..
################################################################################
*/
 $option[] = '<a href="'.build_link("help.php").'">Hilfe/FAQ</a>';
 $option[] = '<a href="'.build_link("search.php").'">Suche</a>';
 $option[] = '<a href="'.build_link("memberlist.php").'">Memberlist</a>';
 $option[] = '<a href="'.build_link("index.php").'">Home</a>';
 $option[] = '<a href="'.build_link("stats.php").'">Statistik</a>';
 $option[] = '<a href="'.build_link("calendar.php").'">Kalender</a>';
 $option[] = '<a href="'.build_link("team.php").'">Staff</a>';
 $option[] = '<a href="'.build_link("qdb.php").'">Quote DB</a>';
if( $g_user['userisadmin'] )
{
	$option[] = '<a href="./admin/" target="_blank">Admincenter</a>';
}

$options = implode(' || ', $option);

if( $g_user['userbanned'] == 1 )
{
	$r_ban = thwb_query("SELECT banpubreason, banexpire FROM ".$pref."ban WHERE userid=$g_user[userid]");
	$ban = mysql_fetch_array($r_ban);
	
	if( $ban['banexpire'] > time() || $ban['banexpire'] == 0 )
	{
		$message = "Sie sind gebannt<br><br>Grund: <i>" 
			. ($ban['banpubreason'] ? $ban['banpubreason'] : "(Keine Angabe)") . "</i><br><br>Dieser Ban"
			. ($ban['banexpire'] ? (" ist gültig bis: " . form_date($ban['banexpire']) . ".") : " ist permanent." )
			. "<br><br><br>Hinweis: Die Forumadministration behält sich das Recht vor,
Benutzer gegebenenfalls ohne Angabe eines Grundes
permanent vom Posten abzuhalten.$style[smallfontend]";
		
		message("Fehler", $message);
	}
	else
	{
		thwb_query("UPDATE ".$pref."user SET userbanned=0 WHERE userid=$g_user[userid]");
		thwb_query("DELETE FROM ".$pref."ban WHERE userid=$g_user[userid]");
	}
}




/*
################################################################################
            add debug messages to $DEBUG string
################################################################################
*/
if ( $g_user['userisadmin'] && $config['debugmode'] )
{
	$DEBUG = '<b>Debug Messages:</b><br>'.$DEBUG;
}

/*
################################################################################
            lastvisited stuff
################################################################################
*/

$lastvisited = '';

if( ereg("board.php", $HTTP_SERVER_VARS['PHP_SELF']) && isset( $boardid ) )
{
	if( $g_user['userid'] != 0 )
	{
		$r_lastvisited = thwb_query("SELECT lastvisitedtime FROM ".$pref."lastvisited WHERE userid=$g_user[userid] AND boardid='".intval($board['boardid'])."'");
			$lastvisited = mysql_fetch_array($r_lastvisited);
			
		if( !$lastvisited['lastvisitedtime'] )
		{
			// new user
			thwb_query("INSERT INTO ".$pref."lastvisited (userid, boardid, lastvisitedtime) VALUES($g_user[userid], '".intval($board['boardid'])."', " . time() . ")");
			$lastvisited = 0;
		}
		else
		{
		  if(mysql_num_rows(thwb_query("SELECT boardid FROM ".$pref."board WHERE boardid = '".intval($board['boardid'])."'")))
		    {
			$lastvisited = $lastvisited['lastvisitedtime'];
			thwb_query("UPDATE ".$pref."lastvisited SET lastvisitedtime=" . time() . " WHERE userid=$g_user[userid] AND boardid='".intval($board['boardid'])."'");
		    }
		}
	}
}


/*
################################################################################
            get parents ( threadid by postid  ..)
################################################################################
*/

if( isset($post['postid']) )
{
	$post['postid'] = intval($post['postid']);
	$r_post = thwb_query("SELECT postid, threadid FROM ".$pref."post WHERE postid='$post[postid]'");
	if( mysql_num_rows($r_post) < 1 )
	{
		message("Fehler", "Post existiert nicht");
	}
	$post = mysql_fetch_array($r_post);

	$thread['threadid'] = $post['threadid'];
}

if( isset($thread['threadid']) )
{
	$thread['threadid'] = intval($thread['threadid']);
	$r_thread = thwb_query("SELECT threadid, boardid, threadtopic, threadpollid, threadauthor, threadcreationtime, threadreplies, threadclosed
	FROM ".$pref."thread WHERE threadid='$thread[threadid]'");
	if( mysql_num_rows($r_thread) < 1 )
	{
	message("Fehler", "Thread existiert nicht");
	}
	$thread = mysql_fetch_array($r_thread);
	// POLLHACK
	if ( $thread['threadpollid'] > 0 )
	{
	$r_poll = thwb_query("SELECT * FROM ".$pref."poll WHERE pollid = $thread[threadpollid]");
	$poll = mysql_fetch_array($r_poll);
	}
	// POLLHACK
	
	$navigation[] = "<a class=\"bglink\" href=\"".build_link("showtopic.php?threadid=$thread[threadid]".((!empty($time)) ? "&time=$time" : ""))."\">" . parse_code($thread['threadtopic']) . "</a>";
	$board['boardid'] = $thread['boardid'];
}

if( isset($board['boardid']) )
{
	$board['boardid'] = intval($board['boardid']);
	$r_board = thwb_query("SELECT boardid, boardname, styleid, boardthreads, boarddisabled FROM ".$pref."board WHERE boardid=$board[boardid]");
	if( mysql_num_rows($r_board) < 1 )
	{
		message("Fehler", "Board existiert nicht");
	}
	$board = mysql_fetch_array($r_board);
	
	$navigation[] = "<a class=\"bglink\" href=\"".build_link("board.php?boardid=$board[boardid]".((!empty($time)) ? "&time=$time" : ""))."\">$board[boardname]</a>";
}

/*
################################################################################
            get style
################################################################################
*/
define( 'STYLE_DEFAULT', 0 );

if(empty($board['styleid']))
{
  if(isset($g_user['styleid']))
    {
      $board['styleid'] = $g_user['styleid'];
    }
  else 
    {
      $board['styleid'] = STYLE_DEFAULT;
    }
}

if( $board['styleid'] == STYLE_DEFAULT )
{
	$r_style = thwb_query("SELECT styleid, styletemplate, colorbg, color1, CellA, CellB, color4, colorbgfont, col_he_fo_font, color_err,
		col_link, col_link_v, col_link_hover, stdfont,
		boardimage, newtopicimage, border_col FROM
		".$pref."style WHERE styleisdefault=1");
}
else
{
	$r_style = thwb_query("SELECT styleid, styletemplate, colorbg, color1, CellA, CellB, color4, colorbgfont, col_he_fo_font, color_err,
		col_link, col_link_v, col_link_hover, stdfont,
		boardimage, newtopicimage, border_col FROM
		".$pref."style WHERE styleid=$board[styleid]");
}
$style = mysql_fetch_array($r_style);

$style['smallfont'] = '<span class="smallfont">';
$style['smallfontend'] = '</span>';
$style['font'] = $style['stdfont'];
$style['stdfont'] = '<span class="stdfont">';
$style['stdfontend'] = '</span>';


/*
################################################################################
Quicklinks[hack] By Morpheus
################################################################################
*/
$quicklinks = '';
$t_quicklinks = '';
if( $config['enable_quicklinks'] )
{
	$TQuicklinks = new Template('./templates/' . $style['styletemplate'] . '/quicklinks.html');

	$r_qlink = thwb_query("SELECT linkid, linkalt, linkcaption FROM ".$pref."qlink");
	while( $qlink = mysql_fetch_array($r_qlink) )
	{
		$quicklinks .= "<A HREF=\"qlinks.php?id=$qlink[linkid]\" title=\"$qlink[linkalt]\" target=_blank>[ $qlink[linkcaption] ]</a> ";
	}
	
	eval($TQuicklinks->GetTemplate("t_quicklinks"));
}



/*
################################################################################
            permissions
################################################################################
*/

global $P;

if( isset($board['boardid']) )
{
	$P = new Permission($g_user['groupids'], $board['boardid']);
	requires_permission( P_VIEW );
	
}
else
{
  $P = new Permission($g_user['groupids']);
}

/*
################################################################################
            create navigation path ( forum / board / thread .. )
################################################################################
*/
$navigation[] = "<a class=\"bglink\" href=\"".build_link("index.php")."\">$config[board_name]</a>";
$navigation = thwb_array_reverse($navigation);
while( list($key, $val) = each($navigation) )
{
	$navpath .= "$val &raquo; ";
}

/*
################################################################################
            static stuff
################################################################################
*/
$topicicon = array(
	'fullalpha',
	'smile',
	'wink',
	'angry',
	'frown',
	'biggrin',
	'gumble',
	'question',
	'strange',
	'rolleyes',
	'oah',
	'prefect'
);

/*
#################################################################################
            additional template stuff
#################################################################################
*/
if( file_exists('./templates/' . $style['styletemplate'] . '/dynamic.inc.php') )
{
	@include('./templates/' . $style['styletemplate'] . '/dynamic.inc.php');
}

$CONTENT = '';
$titleprepend = '';


?>
