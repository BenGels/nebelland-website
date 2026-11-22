<?php
/* $Id: search.php,v 1.12 2004/01/04 10:55:34 thed_o_n Exp $ */
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

define('THWB_NOSESSION_PAGE', true);

include "./inc/header.inc.php";

$config['hitsperpage'] = 20;

$navpath .= "<a class=\"bglink\" href=\"".build_link("search.php")."\">Suche</a>";

if( isset($startsearch) && $startsearch )
{

	$a_board = array();
	$r_board = thwb_query("SELECT boardid FROM $pref"."board WHERE boarddisabled='0'");
	while( $board = mysql_fetch_array($r_board) )
	{
		$a_board[] = $board['boardid'];
	}
	
	$a_boardlist = array();
	if( !isset($selectboards) || $selectedboards[0] == 0 )
	{
		// search all available boards, id = 0 - all boards
		$selectedboards = $a_board;
	}

	while( list(, $boardid) = each($selectedboards) )
	{
		$P->set_boardid($boardid);
		if( $boardid != -1 && $P->has_permission( P_VIEW ) )
		{
			if (in_array($boardid,$a_board))
			{
				$a_boardlist[] = $boardid;
			}
		}
	}
	
	$boards = implode(',', $a_boardlist);
	if( !isset($boards) )
	{
		message('Fehler', 'Sie k&#xF6;nnen keine Boards/Kategorien durchsuchen.');
	}
	
	if( !isset($page) )
	{
		$page = 0;
	}

	$where = array();
	$where[] = "(post.threadid=thread.threadid)";
	$where[] = "(thread.boardid IN (".addslashes($boards)."))";

	if( isset($repliesonly) && $repliesonly == "yes" )
	{
		$where[] = "(thread.threadreplies>0)";
	}

	if( isset($period) && $period == "yes" )
	{
		$searchstart = mktime(0, 0, 0, $startmonth, 1, $startyear);
		$searchend = mktime(0, 0, 0, $endmonth + 1, 0, $endyear);

		$where[] = "(thread.threadtime>$searchstart AND thread.threadtime<$searchend)";
	}

	if( $searchfor == "post" )
	{
		$a_words = explode(" ", $words);
		$sqlwords = array();
		while( list($key, $val) = each($a_words) )
		{
			if( strlen($val) < 3 )
			{
				message("Fehler", "Sie können nicht nach Wörtern mit weniger als 3 Zeichen suchen.");
			}
			if( $key > 5 )
			{
				message("Fehler", "Sie können maximal nach 6 Wörtern suchen.");
			}
			if( $config['slow_search'] )
				$sqlwords[] = "INSTR(LOWER(post.posttext),LOWER('" . addslashes($val) . "'))>0";
			else
				$sqlwords[] = "INSTR(post.posttext,'" . addslashes($val) . "')>0";
		}

		$where[] = '(' . implode(' ' . ($searchmethod == 'OR' ? 'OR' : 'AND') . ' ', $sqlwords) . ')';
	}
	elseif( $searchfor == "author" )
	{
		$r_user = thwb_query("SELECT userid FROM ".$pref."user WHERE LOWER(username)=LOWER('" . addslashes($words) . "')");
		$user = mysql_fetch_array($r_user);

		if( !$user['userid'] )
		{
			message("Fehler", "Der Autor nach dem Sie suchen wollen existiert nicht!");
		}

		$where[] = "(post.userid='$user[userid]')";
	}
	elseif( $searchfor == "today" )
	{
		$where[] = "(thread.threadtime)>" . (time() - (60 * 60 * 24));
	}
	elseif( $searchfor == "lastthreads" )
	{
		$where[] = "(thread.threadtime)>" . (time() - (60 * 60 * 24 * intval($days)));
	}
	else
	{
		$a_words = explode(" ", $words);
		$sqlwords = array();
		while( list($key, $val) = each($a_words) )
		{
			if( strlen($val) < 3 )
			{
				message("Fehler", "Sie können nicht nach Wörtern mit weniger als 3 Zeichen suchen.");
			}
			if( $key > 5 )
			{
				message("Fehler", "Sie können maximal nach 6 Wörtern suchen.");
			}
			if( $config['slow_search'] )
				$sqlwords[] = "INSTR(LOWER(thread.threadtopic),LOWER('" . addslashes($val) . "'))>0";
			else
				$sqlwords[] = "INSTR(thread.threadtopic,'" . addslashes($val) . "')>0";
		}

		$where[] = '(' . implode(' ' . ($searchmethod == 'OR' ? 'OR' : 'AND') . ' ', $sqlwords) . ')';
	}

	// ergebnisanzahl selecten -> seiten
	/* echo */ $query = "
			SELECT DISTINCT
				thread.threadid AS hits
			FROM
				".$pref."thread AS thread,
				".$pref."post AS post
			WHERE
				" . implode(" AND ", $where) . "
			LIMIT 100";
	$r_presearch = thwb_query($query);
	$resultcount = mysql_num_rows($r_presearch);

	/*echo*/ $query = "
				SELECT DISTINCT
				thread.threadid,
				thread.threadtopic,
				thread.boardid,
				thread.threadtime,
				thread.threadlastreplyby,
				thread.threadauthor,
				thread.threadreplies
			FROM
				".$pref."thread AS thread,
				".$pref."post AS post
			WHERE
				" . implode(" AND ", $where) . "
			ORDER BY
				thread.threadtime DESC LIMIT " . $page * $config['hitsperpage'] . ", " . $config['hitsperpage'];

	$r_search = thwb_query($query);

	if( $error = mysql_error() )
	{
		message("Fehler", "Fehler");
	}

	if( mysql_num_rows($r_search) == 0 )
	{
		message("Keine Themen gefunden", "Es wurden keine Themen gefunden die Ihren Suchkriterien entsprechen!<br><a href=\"".build_link("search.php")."\">Neue Suche</a>");
	}

	$r_board = thwb_query("
		SELECT
			board.boardid, board.boardname, category.categoryname
		FROM
			".$pref."board AS board,
			".$pref."category AS category
		WHERE
			board.categoryid=category.categoryid");
	while( $board = mysql_fetch_array($r_board) )
	{
		$boardpath[$board['boardid']] = $board['categoryname'] . ' / ' . $board['boardname'];
	}

	$frame = new Template("templates/" . $style['styletemplate'] . "/frame.html");
	$searchresult = new Template("templates/" . $style['styletemplate'] . "/searchresult.html");
	$searchresultrow = new Template("templates/" . $style['styletemplate'] . "/searchresultrow.html");

	$pages = ceil($resultcount / $config['hitsperpage']);
	$pagesstring = "";

	for( $i = 0; $i < $pages; $i++ )
	{
		if( $i == $page )
		{
			$pagesstring .= "&gt;" . ($i + 1)  . "&lt; ";
		}
		else
		{ 
			$pagesstring .= '[<a href="'.build_link('search.php?page='.$i.
				'&amp;searchfor='.$searchfor.
				'&amp;words='.((isset($words)) ? $words : '').
				'&amp;searchmethod='.((isset($searchmethod)) ? $searchmethod : '').
				'&amp;repliesonly='.((isset($repliesonly)) ? $repliesonly : '').
				'&amp;period='.((isset($period)) ? $period : '').
				'&amp;startmonth='.((isset($startmonth)) ? $startmonth : '').
				'&amp;startyear='.((isset($startyear)) ? $startyear : '').
				'&amp;endmonth='.((isset($endmonth)) ? $endmonth : '').
				'&amp;endyear='.((isset($endyear)) ? $endyear : '').
				'&amp;days='.((isset($days)) ? $days : ''));

			reset($selectedboards);
			while( list(, $selectedboard) = each($selectedboards) )
			{
				$pagesstring .= '&amp;selectedboards[]='.$selectedboard;
			}
	
			$pagesstring .= '&amp;startsearch=1">'.($i + 1).'</a>] ';
		}
	}

	$RESULT_ROWS = '';

	while( $search = mysql_fetch_array($r_search))
	{
		$i++;
		if( $i % 2 > 0 )
		{
			$rowbgcolor = $style['CellB'];
		}
		else
		{
			$rowbgcolor = $style['CellA'];
		}
		$search['threadtopic'] = parse_code($search['threadtopic']);
		$search['threadauthor'] = parse_code($search['threadauthor']);
		$search['threadlastreplyby'] = parse_code($search['threadlastreplyby']);
		$search['threadtime'] = form_date($search['threadtime']);
		$search['threadpath'] = $boardpath[$search['boardid']];
		
		$highlight = (((!empty($a_words)) && (count($a_words))) ? '&amp;highlight=' . implode(' ', $a_words) : '');
	
		eval($searchresultrow->GetTemplate("RESULT_ROWS"));
	}

	$navpath .= ' &raquo; Suchergebnisse';

	if( $resultcount == 100 )
		$resultcount = '</b>Es wurden sehr viele Suchergebnisse gefunden, bitte versuchen Sie, die Suche weiter einzuschränken.<br><b>'. $resultcount;
		
	eval($searchresult->GetTemplate("CONTENT"));
	eval($frame->GetTemplate());
}
else
{
	// select boards
	$a_board = array();
	$r_board = thwb_query("SELECT boardname, boardid, categoryid FROM $pref"."board WHERE boarddisabled='0' ORDER BY boardorder ASC");
	while( $board = mysql_fetch_array($r_board) )
	{
		$P->set_boardid($board['boardid']);
		if( $P->has_permission( P_VIEW) )
		{
			$a_board[$board['categoryid']][] = $board;
		}
	}
	
	$boards = '';
	$r_category = thwb_query("SELECT categoryname, categoryid FROM ".$pref."category ORDER BY categoryorder ASC");
	while( $category = mysql_fetch_array($r_category) )
	{
		if( isset($a_board[$category['categoryid']]) )
		{
			$boards .= '<option value="-1">' . $category['categoryname'] . '</option>';
			while( list(, $board) = @each($a_board[$category['categoryid']]) )
			{
				$boards .= '<option value="' . $board['boardid'] . '">- ' . $board['boardname'] . '</option>';
			}
		}
	}


	$getdate = getdate(time());

	$Tframe = new Template("templates/".$style['styletemplate']."/frame.html");
	$Tsearchform = new Template("templates/".$style['styletemplate']."/searchform.html");

	$navpath .= ' &raquo; Sucheinstellungen';

	eval($Tsearchform->GetTemplate("CONTENT"));
	eval($Tframe->GetTemplate());
}


?>
