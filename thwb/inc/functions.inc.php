<?php
/* $Id: functions.inc.php,v 1.40 2004/01/06 08:46:09 thed_o_n Exp $ */
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

function flag_make_array($str)
{
  $a = array();

  for($i = 0; $i < strlen($str); $i++)
    {
      $a[] = $str[$i];
    }

  return $a;
}

function flag_or($str1, $str2)
{
  $str = "";

  for($i = 0; $i < max(strlen($str1), strlen($str2)); $i++)
    {
      if(($i < strlen($str1) && $str1[$i]) || ($i < strlen($str2) && $str2[$i]))
	{
	  $str .= "1";
	}
      else
	{
	  $str .= "0";
	}
    }

  return $str;
}

function build_link($link, $noamp = false)
{
  global $g_user, $s;

  $add = '';

  if($g_user['userid'] != 0)
    {
      if((strpos($link, "?") || (strpos($link, "&"))))
	{
	  $add .= (($noamp) ? "&" : "&amp;")."s=".$s;
	}
      else
	{
	  $add .= "?s=".$s;
	}

      if($x = strpos($link, "#"))
	{
	  $link = substr($link, 0, $x) . $add . substr($link, $x);
	}
      else
	{
	  $link .= $add;
	}
    }
	
  return $link;
}

class Template
{
	function Template($szTemplateName)
	{
		if( !file_exists($szTemplateName) )
		{
			$this->Halt("unable to load template file: '" . $szTemplateName . "' does not exist.");
		}
		$this->szTemplateData = @implode('', (@file($szTemplateName)));
		$this->szTemplateData = str_replace('"', '\"', $this->szTemplateData);
		$this->szTemplateData = preg_replace("/_\('([^']+)'\)/", '".build_link("$1")."', $this->szTemplateData);
	}

	function GetTemplate($szVarname = "")
	{
	  if( $szVarname )
		{
			return ('$' . $szVarname . ' .= "' . $this->szTemplateData . '";');
		}
		else
		{
			return ('compress_page("' . $this->szTemplateData . '");');
		}
	}

	function Halt($szErrorMsg)
	{
		echo "<pre>Template error:\n " . $szErrorMsg . "</pre>";
		exit;
	}
}

/*
permission class
*/
class Permission
{
	var		$a_group;
	var		$a_groupboard;
	var		$boardid;

	function Permission($groupids, $boardid = -1)
	{
		global		$pref;

		if( $groupids === '' )
			$groupids = '-1';
	
		$this->a_group = array();
		$r_group = thwb_query("SELECT groupid, accessmask FROM $pref"."group WHERE groupid IN(".$groupids.")");
		while( $group = mysql_fetch_array($r_group) )
		{
			$this->a_group[$group['groupid']] = $group['accessmask'];
		}
		mysql_free_result($r_group);

		$this->a_groupboard = array();
		if( $boardid == -1 )
			$r_groupboard = thwb_query("SELECT groupid, boardid, accessmask FROM $pref"."groupboard WHERE groupid IN (".$groupids.")");
		else
			$r_groupboard = thwb_query("SELECT groupid, boardid, accessmask FROM $pref"."groupboard WHERE boardid='$boardid' AND groupid IN (".$groupids.")");
		
		while( $groupboard = mysql_fetch_array($r_groupboard) )
		{
			$this->a_groupboard[ $groupboard['boardid'] ][ $groupboard['groupid'] ] = $groupboard['accessmask'];
		}
		mysql_free_result($r_groupboard);
		
		$this->set_boardid($boardid);
	}

	function set_boardid($boardid)
	{
		$this->boardid = $boardid;
	}
	
	function has_permission($perm)
	{
		$mask = "";
		
		reset($this->a_group);
		reset($this->a_groupboard);

 		while( list($groupid, $accessmask) = each($this->a_group) )
 		{
 			if( isset($this->a_groupboard[$this->boardid][$groupid]) )
			  {
			    $mask = flag_or($mask, $this->a_groupboard[$this->boardid][$groupid]);
			  }
 			else
			  {
			    $mask = flag_or($mask, $accessmask);
			  }
 		}

		if(strlen($mask) < $perm)
		  {
		    return false;
		  }

		$access = flag_make_array($mask);

		return (bool) ($access[$perm]);
	}
}


function requires_permission($perm, $poll = 0) // POLLHACK
{
	global $g_user, $pref, $style, $config, $options, $P, $HTTP_SERVER_VARS, $t_quicklinks, $DEBUG, $debug, $titleprepend, $CONTENT;

	if( $P->has_permission($perm) )
		return;

	global $board;

	if( !empty($g_user['styleid']) )
	{
		$board['styleid'] = $g_user['styleid'];
	}

	if( $board['styleid'] == 0 )
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
			".$pref."style WHERE styleid='".intval($board['styleid'])."'");
	}
	$style = mysql_fetch_array($r_style);

	$style['smallfont'] = '<font face="' . $style['stdfont'] . '" size="1">';
	$style['smallfontend'] = '</font>';
	$style['stdfont'] = '<font face="' . $style['stdfont'] . '" size="2">';
	$style['stdfontend'] = '</font>';

	if( !isset($navpath) || !$navpath )
	{
		$navpath = '<a class="bglink" href="'.build_link("index.php").'">'.$config['board_name'].'</a> &raquo; Zugriff verweigert';
	}
	elseif(substr($navpath, strlen($navpath) - 8) != "&raquo; ")
	  {
	    $navpath .= " &raquo; Zugriff verweigert";
	  }

	$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
// POLLHACK
if ($poll)
{
$Tnopermission = new Template("templates/" . $style['styletemplate'] . "/nopollpermission.html");
}
else
{
$Tnopermission = new Template("templates/" . $style['styletemplate'] . "/nopermission.html");
}
// POLLHACK
	$t_loginform = '';
	
	if( !$g_user['userid'] )
	{
		$Tnoperm_login = new Template("templates/" . $style['styletemplate'] . "/noperm_login.html");
		
		$source = urlencode(basename($HTTP_SERVER_VARS['REQUEST_URI']));
		eval($Tnoperm_login->GetTemplate('t_loginform'));
	}

	eval($Tnopermission->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());

	exit;
}

// fix by tendor --theDon
function highlight_words($text, $a_word)
{
	$a_color = array('#ffff66', '#A0FFFF', '#99ff99', '#ff9999', '#ff66ff');
	$i = 0;

	while( list(, $word) = each($a_word) )
	{
		if( strlen($word) >= 3 )
		{
		  $text = preg_replace('/('.preg_quote($word, '/').')(?=([^>]*\<)|$)/i', '<strong style="color:black; background-color:'.$a_color[$i % count($a_color)].'">$1</strong>', $text);
		  $i++;
        }
    }

    return $text;
}

define('INVALID_CHAR', 1);
define('INVALID_LENGTH', 2);
define('INVALID_TAG', 3);
define('NAME_TAKEN', 4);
define('NAME_BANNED', 5);

function verify_username($username)
{
	global $config, $pref;
	$legalchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 [|](){}.-_äöüÄÖÜß";

	for( $i = 0; $i < strlen($username); $i++ )
	{
		if( !strstr($legalchars, $username[$i]) )
		{
			return INVALID_CHAR;
		}
	}

	$a_tags = array("php", "code", "mail", "url", "noparse", "color", "b", "i", "u", "-", "quote");
	$a_illegal = array();

	preg_match_all("/\[.*\]/U", "[".join($a_tags, "][")."][/".join($a_tags, "][/")."]", $a_illegal);

	foreach($a_illegal[0] as $k => $v)
	  {
	    if(strstr($v, $username))
	      {
		return INVALID_TAG;
	      }
	  }

	while( list(, $bannedname) = @each($config['bannednames']) )
	{
		if( $bannedname && stristr($username, $bannedname) )
		{
			return NAME_BANNED;
		}
	}
	
	if( strlen($username) > $config['max_usernamelength'] ||
		strlen($username) < $config['min_usernamelength'] )
	{
		return INVALID_LENGTH;
	}
	
	$r_user = thwb_query("SELECT userid FROM $pref"."user WHERE username='".addslashes($username)."'");
	if( mysql_num_rows($r_user) )
	{
		return NAME_TAKEN;
	}
	
	return 0;
}

//called by register, reply, newtopic
function check_username($username)
{
	switch( verify_username($username) )
	{
		case NAME_TAKEN:
			message('Fehler', 'Der Benutzername existiert leider schon!');
			break;
		case INVALID_CHAR:
			message('Fehler', 'Ihr gew&#xFC;nschter Benutzername enth&#xE4;lt ung&#xFC;ltige Zeichen!');
			break;
		case NAME_BANNED:
			message('Fehler', 'Der ausgew&#xE4;hlte Benutzername kann leider nicht verwendet werden.');
			break;
		case INVALID_LENGTH:
			message('Fehler', 'Die L&#xE4;nge des Benutzernamens ist ung&#xFC;ltig');
			break;
	        case INVALID_TAG:
		        message('Fehler', 'Ihr gew&uuml;nschter Benutzername enth&auml;lt ThWB-Code-Tags und kann daher nicht verwendet werden.');
		default:
	}
	
	return;
}

//called by register, editprofile
function check_email($email)
{
	return eregi("^[\_a-z0-9-]+(\.[\_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$", $email);
}

//ttt: check whether user is allowed to get a member's email address
function get_email( $user, $short = false )
{
	global $g_user, $style;
	
	if( $g_user['userid'] == 0 )
		return '- (Versteckt)';
		
	$retstring = '<a href="mailto:'. $user['useremail'] .'">'
		.($short ? chopstring($user['useremail'],20) : $user['useremail'] ) .'</a>';
	
	if( !$user['userhideemail'] || ($user['userid'] == $g_user['userid']) )
	{
		return $retstring;
	} 
	else	
	{
		if( !$g_user['userisadmin'] )
		{
			return '- (Versteckt)';
		}
		else
		{
			if( $short )
				return $retstring;
			else
				return '- (Versteckt) '.$style['smallfont'].' [Admin: '. $retstring .' ]'.$style['smallfontend'];
		}
	}
}

function message($title, $msg, $opt_back = 1, $opt_index = 1)
{
	global $style, $config, $g_user, $options, $JUMP_MENU, $navpath, $pref, $debug, $DEBUG;

	$CONTENT = '';
	$titleprepend = '';
	$t_quicklinks = '';

	if( !$style )
	{
		global $board;

		if( !empty($g_user['styleid']) )
		{
			$board['styleid'] = $g_user['styleid'];
		}

		if( empty($board['styleid']) || $board['styleid'] == 0 )
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
				".$pref."style WHERE styleid='".intval($board['styleid'])."'");
		}
		$style = mysql_fetch_array($r_style);

		$style['smallfont'] = '<font face="' . $style['stdfont'] . '" size="1">';
		$style['smallfontend'] = '</font>';
		$style['stdfont'] = '<font face="' . $style['stdfont'] . '" size="2">';
		$style['stdfontend'] = '</font>';
	}

	if( !isset($navpath) || !$navpath )
	{
		$navpath = "<a class=\"bglink\" href=\"".build_link("index.php")."\">$config[board_name]</a> &raquo; ";
	}
	elseif(substr($navpath, strlen($navpath) - 8) != "&raquo; ")
	  {
	    $navpath .= " &raquo; ";
	  }

	$navpath .= $title;

	$messageoptions = "&nbsp;";
	
	$opt_back ? $messageoptions .= "[ <a class=\"hefo\" href=\"javascript:history.back()\">Zur&uuml;ck</a> ] " : "";
	$opt_index ? $messageoptions .= "[ <a class=\"hefo\" href=\"".build_link("index.php")."\">Index</a> ]" : "";
	
	$Tframe = new Template("templates/" . $style['styletemplate'] . "/frame.html");
	$Tmessage = new Template("templates/" . $style['styletemplate'] . "/message.html");

	eval($Tmessage->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());
	exit;
}




function message_redirect($msg, $url)
{
	global $style;

	$TRedirect = new Template('templates/' . $style['styletemplate'] . '/redirect.html');
	eval($TRedirect->GetTemplate());
	
	exit;
}

function form_date($time, $verbose = 1)
{
	global $config;
	
	if( $time < (60 * 60 * 24) )
	{
		return "N/A";
	}

	$time += $config['timeoffset'] * 3600;

	if( date("d.m.Y", (time() + $config['timeoffset'] * 3600) ) == date('d.m.Y', $time) && $verbose )
	{
		return "<b>Heute</b>, " . date("H:i", $time) . " Uhr";
	}
	else
	{
		return date("d.m.Y, H:i", $time) . " Uhr";
	}
}

function r_stripslashes(&$array)
{
	while( list($k, $v) = each($array) )
	{
		if( $k != 'argc' && $k != 'argv' && (strtoupper($k) != $k || ''.intval($k) == "$k") )
		{
			if( is_string($v) )
			{
				$array[$k] = stripslashes($v);
			}
			if( is_array($v) )
			{
				$array[$k] = r_stripslashes($v);
			}
		}
	}
	return $array;
}

function jumpmenu($currentboard = 1)
{
	global $pref, $g_user;

	$P = new Permission($g_user['groupids']);

	// precache boards
	$a_board = array();
	$r_board = thwb_query("SELECT boardid, boardname, categoryid
		FROM ".$pref."board
		WHERE boarddisabled = 0
		ORDER BY boardorder ASC");
	while( $board = mysql_fetch_array($r_board) )
	{
		$P->set_boardid($board['boardid']);
		if( $P->has_permission( P_VIEW ) )
			$a_board[$board['categoryid']][] = $board;
	}
	
	// category
	$r_category = thwb_query("SELECT categoryid, categoryname FROM
	".$pref."category ORDER BY categoryorder ASC");
	
	$JUMP_MENU = '<select class="tbselect" name="board[boardid]" onChange="Submit.click()">';
	while( $category = mysql_fetch_array($r_category) )
	{
		if( !empty($a_board[$category['categoryid']]) && $a_board[$category['categoryid']] )
		{
			$JUMP_MENU .= '<option value="-'.$category['categoryid'].'">'.$category['categoryname'].'</option>';
			while( list(, $board) = each($a_board[$category['categoryid']]) )
			{
				$JUMP_MENU .= '<option value="'.$board['boardid'].'"'.($board['boardid'] == $currentboard ? ' selected' : '') . '>- '.$board['boardname'].'</option>';
			}
		}
	}

	$JUMP_MENU .= '</select> <input class="tbbutton" type="submit" name="Submit" value="Jump">';

	return $JUMP_MENU;
}


function thwb_array_reverse($array)
{
	if( function_exists('array_reverse') )
	{
		return array_reverse($array);
	}
	else
	{
		$a_new = array();
		for( $i = count($array) - 1; $i >= 0; $i--)
		{
			$a_new[] = $array[$i];
		}
		return $a_new;
	}
}
//$DEBUG = "<pre>";


function thwb_query($query)
{
	global $DEBUG, $g_user;
	global $config;
	global $all_t;
	
	$start = microtime();

	$result = mysql_query($query);

	$end = microtime();
	
	if( !$result )
	{
		print '<pre><b>ThWboard Error</b><br>MySQL: ' . mysql_error() . '
Query: ' . $query . '</pre>';
		exit;
	}
	if ( $g_user['userisadmin'] && $config['debugmode'] ) 
	{
		// Extended DEBUG Mody By Andy
		$start_t = explode(" ", $start);
		$end_t = explode(" ", $end);

		$start_t = $start_t[0] + $start_t[1];
		$end_t = $end_t[0] + $end_t[1];
		$full_t = $end_t - $start_t;
		$all_t = $all_t + $full_t;
		if( !$all_t )
		{
			$all_t = 0;
		}

		$DEBUG .= "</center><pre><font color=\"black\">$query\n\n<b>Zeit vor der Abfrage: $start_t\n Zeit nach der Abfrage: $end_t\n Abfragezeit: $full_t\n</b>";
		$DEBUG .= "<pre><b>Bisherige gesamte Abfragedauer: <font color=\"red\">$all_t</font>, in Sekunden: <font color=\"red\">";
		$DEBUG .= substr($all_t, 0, 4);
		$DEBUG .= "</b></PRE><BR><BR>";
	}

	return $result;
}

function chopstring($string, $maxchars)
{
	if( strlen($string) > $maxchars )
		$string = substr($string, 0, ($maxchars - 3)) . '...';
	
	return $string;
}

function killshout($string)
{
	// ignore pretty short topics
	if( strlen( $string ) > 3 )
	{
		$caps = 0;
		for( $i = 0; $i < strlen($string); $i++ )
		{
			if( $string[$i] > 'A' && $string[$i] < 'Z' )
				$caps++;
		}
		
		$ratio = $caps / strlen($string);
		if( $ratio > 0.3 )
		{
			$words = @explode(' ', $string);
			while( list(, $word) = each($words) )
			{
				if( strlen($word) > 1 )
					$new_words[] = substr($word, 0, 1) . strtolower( substr( $word, 1 ) );
				else
					$new_words[] = $word;
			}
			return @implode(' ', $new_words);
		}
	}
	return $string;
}

function updateboard($boardid)
{
	global $pref;
	// updates last posttime/thread/author of a board ..
	$r_thread = thwb_query("SELECT threadid, threadtopic, threadtime, threadlastreplyby FROM ".$pref."thread WHERE threadlink='0' AND boardid='".intval($boardid)."'  GROUP BY threadid ORDER BY threadtime DESC LIMIT 1");

	if( mysql_num_rows($r_thread) < 1 )
	{
		thwb_query("UPDATE ".$pref."board SET
			boardlastpost='0',
			boardthreadid='0',
			boardthreadtopic='',
			boardlastpostby='',
                        boardposts='0',
                        boardthreads='0'
		WHERE boardid='".intval($boardid)."'");
	}
	else
	{
		$thread = mysql_fetch_array($r_thread);

		$r_thread = thwb_query("SELECT COUNT(threadid) AS threadcount, SUM(threadreplies) AS postcount FROM ".$pref."thread WHERE boardid=$boardid");
		$thread = array_merge($thread, mysql_fetch_array($r_thread));

		$thread['postcount'] += $thread['threadcount']; // threads without replies.
				
		thwb_query("UPDATE ".$pref."board SET
			boardlastpost='$thread[threadtime]',
			boardthreadid='$thread[threadid]',
			boardthreadtopic='" . addslashes($thread['threadtopic']) . "',
			boardlastpostby='" . addslashes($thread['threadlastreplyby']) . "',
                        boardposts='".addslashes($thread['postcount'])."',
                        boardthreads='".addslashes($thread['threadcount'])."'
		WHERE boardid='".intval($boardid)."'");
	}
}

function updatethread($threadid)
{
	global $pref;
	// update thread stuff when deleting posts
	$r_post = thwb_query("SELECT posttime, userid, postguestname FROM ".$pref."post WHERE threadid='".intval($threadid)."' ORDER BY posttime DESC LIMIT 1");
	$post = mysql_fetch_array($r_post);

	if( $post['userid'] != 0 )
	{
		$r_user = thwb_query("SELECT username FROM ".$pref."user WHERE userid=$post[userid]");
		$user = mysql_fetch_array($r_user);
		
		$author = $user['username'];
	}
	else
	{
		$author = $post['postguestname'];
	}

	thwb_query("UPDATE ".$pref."thread SET threadtime=$post[posttime], threadlastreplyby='" . addslashes($author) . "' WHERE threadid='".intval($threadid)."'");
}

function checksize($ic_avatar)
{
	global $err_msg, $config;
	if ( $ic_avatarsize = @GetImageSize($ic_avatar) ) 
	{
		if ( $ic_avatarsize[0] > $config['avatarwidth'] )
		{ 
			$err_msg .= 'Das Avatar-Bild ist zu breit.<br>';
		}
		if ( $ic_avatarsize[1] > $config['avatarheight'] )
		{ 
			$err_msg .= 'Das Avatar-Bild ist zu hoch.<br>';
		}
		if ( $ic_avatarsize[2] > 3 )
		{
			$err_msg .= 'Das Avatar-Bild hat ein ungültiges Format.<br>';
		}
	}
	else
	{
		$err_msg .= 'Das Avatar-Bild konnte nicht geladen werden.<br>'; 
	}
}

// Funktion zur Ersetzung von gebannten Wörtern (groß/klein egal)
function check_banned($text)
{
	global $pref;
	
	$r_bwords = thwb_query("SELECT banword, modword FROM $pref"."bannedwords");
	if( mysql_num_rows($r_bwords) != 0 )
	{
		$bwords = array();
		$mwords = array();
		while( list($bword, $mword) = mysql_fetch_row($r_bwords) )
		{
			$bwords[] = "/([a-z])*(" . $bword . ")([a-z])*/i";
			$mwords[] = $mword;
		}
		mysql_free_result($r_bwords);
		$text = preg_replace($bwords, $mwords, $text);
	}

	return $text;
}

function compress_page($output)
{
	global $HTTP_SERVER_VARS, $config;

	if( isset($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING']) && strstr($HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip')
		&& function_exists('gzcompress') && $config['compression'] )
	{
		$output .= '<!-- gzcompress()ed -->';
		
		// http://www.p hp.net/manual/en/function.gzcompress.p hp -- comments
		header('Content-Encoding: gzip');
		$size = strlen($output);
		$crc = crc32($output);
		$output = gzcompress($output, 1);

		print "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		print substr($output, 0, strlen($output) - 4);
		print pack('V', $crc); 
		print pack('V', $size);
	}
	else
	{
		print $output;
	}
}

// POLLHACK

function verifyedittime()
{
global $config, $thread;
if ( ($config['pollmaxedittime'] == "-1") OR (intval($thread['threadcreationtime']) + ($config['pollmaxedittime']*60)) < time() )
{
$edittimecheck = 1;
}
else
{
$edittimecheck = 0;
}
return $edittimecheck;
}

function getvoteid($pollid)
{
global $pref;
$r_list = thwb_query("SELECT pollusers FROM ".$pref."poll WHERE pollid = $pollid");
$list = mysql_fetch_array($r_list);
$users = explode("@@",$list['pollusers']);
$count = sizeof($users);
for ($i=0;$i<$count;$i++)
{
$userarray = explode("@",$users[$i]);
$user['id'][$i] = $userarray[0];
}
return $user['id'];
}

function getvotenames($pollid)
{
global $pref, $style;
$r_list = thwb_query("SELECT pollusers FROM ".$pref."poll WHERE pollid = $pollid");
$list = mysql_fetch_array($r_list);
$users = explode("@@",$list['pollusers']);
$count = sizeof($users)-1;
$table = '<table width="350" border="0"><tr><td>'.$style['smallfont'];
for ($i=0;$i<$count;$i++)
{
$userarray = explode("@",$users[$i]);
$user['id'][$i] = $userarray[0];
$user['name'][$i] = $userarray[1];
$table .= "<a href=\"".build_link("v_profile.php?userid=".$user['id'][$i])."\">".$user['name'][$i]."</a>";
if ($i<($count-1))
{
$table .= ", ";
}
}
$table .= $style['smallfontend'].'</table>';
return $table;

}

// POLLHACK
?>
