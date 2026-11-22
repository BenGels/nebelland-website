<?php
/* The Quote DB Hack   by dp */
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
include './inc/header.inc.php';

define('QUOTES_PER_PAGE', 10);

function format_quote($string)
{
	return '<span style="font-family: courier new, lucida console, fixed; font-size: small;">'.nl2br(htmlspecialchars($string)).'</span>';
}

function calc_rating($plus, $minus)
{
	return (-$minus + $plus);
}

$TFrame = new Template('./templates/'.$style['styletemplate'].'/frame.html');
$TQdb = new Template('./templates/'.$style['styletemplate'].'/qdb.html');
$TQdb_listrow = new Template('./templates/'.$style['styletemplate'].'/qdb_listrow.html');
$TQdb_singlequote = new Template('./templates/'.$style['styletemplate'].'/qdb_singlequote.html');
$TQdb_addform = new Template('./templates/'.$style['styletemplate'].'/qdb_addform.html');
$TQdb_navrow = new Template('./templates/'.$style['styletemplate'].'/qdb_navrow.html');

$navpath .= 'Quote Datenbank';


$r_quote = thwb_query("SELECT COUNT(quoteid) FROM $pref"."quote");
list($quotecount) = mysql_fetch_row($r_quote);
mysql_free_result($r_quote);


if( $action == 'sux' )
{
	if( !$g_user['userisadmin'] )
	{
		message('Zugriff verweigert', 'Zugriff verweigert.');
	}
	else
	{
		$quoteid = intval($quoteid);
		thwb_query("DELETE FROM $pref"."quote WHERE quoteid=".$quoteid);
		thwb_query("DELETE FROM $pref"."quotevote WHERE quoteid=".$quoteid);

		message('Quote gel&ouml;scht', 'Quote wurde gel&ouml;scht.');
	}
}
else if( $action == 'list' )
{
	if( !$pagenum )
		$pagenum = 1;

	$navopts = '&nbsp;';
	if( $pagenum != 1 )
		$navopts .= '[ <a href="qdb.php?action=list&order='.$order.'&pagenum='.($pagenum - 1).'">&laquo; Seite zur&uuml;ck</a> ]';
	if( $pagenum != ceil($quotecount / QUOTES_PER_PAGE) )
		$navopts .= '[ <a href="qdb.php?action=list&order='.$order.'&pagenum='.($pagenum + 1).'">Seite vor &raquo;</a> ]';


	eval($TQdb_navrow->GetTemplate('QDB_CONTENT'));


	$offset = (intval($pagenum) - 1) * QUOTES_PER_PAGE;

	if( $order == 'latest' )
		$order = 'time DESC';
	else
		$order = '(-minuscount + pluscount) DESC';

	$r_quote = thwb_query("SELECT quoteid, quote, pluscount, minuscount, time FROM $pref"."quote ORDER BY $order LIMIT ".$offset.", ".QUOTES_PER_PAGE);
	while( $quote = mysql_fetch_array($r_quote) )
	{

		$quote['rating'] = calc_rating($quote['pluscount'], $quote['minuscount']);
		$quote['time'] = form_date($quote['time']);
		$quote['quote'] = format_quote($quote['quote']);
		eval($TQdb_listrow->GetTemplate('QDB_CONTENT'));
	}
	eval($TQdb_navrow->GetTemplate('QDB_CONTENT'));

	eval($TQdb->GetTemplate('CONTENT'));
}
else if( $action == 'vote' )
{
	$quoteid = intval($quoteid);

	if( $g_user['userid'] == 0 )
	{
		message("Fehler", "Nur registrierte Benutzer k&ouml;nnen Quotes bewerten.");
	}
	else
	{
		$r_quotevote = thwb_query("SELECT choice FROM $pref"."quotevote WHERE userid=".$g_user['userid']."
			AND quoteid=".$quoteid);

		if( mysql_num_rows($r_quotevote) )
		{
			message('Fehler', 'Es ist nur eine Bewertung pro Quote erlaubt.');
		}
		else
		{
			if( $choice == 'good' )
			{
				thwb_query("UPDATE $pref"."quote SET pluscount=pluscount + 1 WHERE quoteid=".$quoteid);
				$choice = '+';
			}
			else
			{
				thwb_query("UPDATE $pref"."quote SET minuscount=minuscount + 1 WHERE quoteid=".$quoteid);
				$choice = '-';
			}

			thwb_query("INSERT INTO $pref"."quotevote (userid, quoteid, choice)
				VALUES (".$g_user['userid'].", ".$quoteid.", '".$choice."')");

			header('Location: qdb.php?quoteid='.$quoteid);
		}
	}
}
else if( $action == 'add' )
{
	if( $g_user['userid'] == 0 )
	{
		message("Fehler", "Nur registrierte Benutzer k&ouml;nnen Quotes hinzuf&uuml;gen.");
	}
	else
	{
		if( $insert )
		{
			if( strlen($quote) < 12 )
			{
				message('Fehler', 'Ein bisschen Text sollte es schon sein.');
			}
			else
			{
				thwb_query("INSERT INTO $pref"."quote (userid, time, quote)
					VALUES ($g_user[userid], ".time().", '".addslashes(trim($quote))."')");

				$quoteid = mysql_insert_id();
				header('Location: qdb.php?quoteid='.$quoteid);
			}
		}
		else
		{
			eval($TQdb_addform->GetTemplate("CONTENT"));
		}
	}
}
else
{
	if( !isset($quoteid) )
	{
		/* total quote count */
		$r_quote = thwb_query("SELECT COUNT(quoteid) FROM $pref"."quote");
		list($quotecount) = mysql_fetch_row($r_quote);

		$quoteoffset = rand() % $quotecount;

		/* select one randomly */
		$r_quote = thwb_query("
			SELECT quote.quoteid, quote.quote, quote.userid, quote.time, quote.views, user.username, quote.pluscount, quote.minuscount
			FROM $pref"."quote AS quote, $pref"."user AS user
			WHERE quote.userid = user.userid LIMIT ".intval($quoteoffset).", 1");
	}
	else
	{
		$r_quote = thwb_query("
			SELECT quote.quoteid, quote.quote, quote.userid, quote.time, quote.views, user.username, quote.pluscount, quote.minuscount
			FROM $pref"."quote AS quote, $pref"."user AS user
			WHERE quote.userid = user.userid AND quoteid=".intval($quoteid)."");
	}

	if( mysql_num_rows($r_quote) < 1 )
		message('Fehler', 'Noch keine Quotes vorhanden.<br><br><a href="'.build_link("qdb.php?action=add").'">Quote hinzuf&uuml;gen</a>');


	$quote = mysql_fetch_array($r_quote);
	thwb_query("UPDATE $pref"."quote SET views = views+1 WHERE quoteid=$quote[quoteid]");

	$quote['time'] = form_date($quote['time']);
	$quote['username'] = htmlspecialchars($quote['username']);
	$quote['quote'] = format_quote($quote['quote']);
	$quote['rating'] = calc_rating($quote['pluscount'], $quote['minuscount']);
	$quote['votecount'] = $quote['pluscount'] + $quote['minuscount'];

	/* select next and prev quote */
	$r_quote = thwb_query("SELECT quoteid FROM $pref"."quote WHERE quoteid > ".intval($quote['quoteid'])." ORDER BY quoteid ASC LIMIT 1");
	if( mysql_num_rows($r_quote) )
	{
		list($quoteid) = mysql_fetch_row($r_quote);
		$next = '<a href="'.build_link("qdb.php?quoteid=$quoteid").'">&raquo;</a>';
	}
	else
		$next = '&raquo;';

	$r_quote = thwb_query("SELECT quoteid FROM $pref"."quote WHERE quoteid < ".intval($quote['quoteid'])." ORDER BY quoteid DESC LIMIT 1");
	if( mysql_num_rows($r_quote) )
	{
		list($quoteid) = mysql_fetch_row($r_quote);
		$prev = '<a href="'.build_link("qdb.php?quoteid=$quoteid").'">&laquo;</a>';
	}
	else
		$prev = '&laquo;';

	eval($TQdb_singlequote->GetTemplate('QDB_CONTENT'));
	eval($TQdb->GetTemplate('CONTENT'));
}



eval($TFrame->GetTemplate());

?>
