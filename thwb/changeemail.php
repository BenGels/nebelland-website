<?php
/* $Id: changeemail.php,v 1.4 2003/09/27 23:15:10 thed_o_n Exp $ */
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

$navpath .= 'Email Adresse &auml;ndern';

if( $g_user['userid'] == 0 )
{
	message('Fehler', 'Bitte erst einloggen.');
}
else
{
	$user = ((isset($HTTP_POST_VARS['user'])) ? $HTTP_POST_VARS['user'] : '');

	if( !isset($change) || !$change )
	{
		$TFrame = new Template('./templates/'.$style['styletemplate'].'/frame.html');
		$TChangeemail = new Template('./templates/'.$style['styletemplate'].'/changeemail.html');

		$t_changewarning = '';

		if( $config['use_email'] )
		{
			$TChangeemail_warning = new Template('./templates/'.$style['styletemplate'].'/changeemail_warning.html');
			eval($TChangeemail_warning->GetTemplate('t_changewarning'));
		}

		eval($TChangeemail->GetTemplate("CONTENT"));
		eval($TFrame->GetTemplate());
	}
	else
	{
		$r_user = thwb_query("SELECT userpassword, username FROM ".$pref."user WHERE userid='$g_user[userid]'");
		$dbuser = mysql_fetch_array($r_user);

		if( !$user['useroldpassword'] || $dbuser['userpassword'] != md5($user['useroldpassword']) )
		{
			message('Fehler', 'Das Passwort ist leider nicht korrekt.');
		}
		else if( !check_email($user['usernewemail']) )
		{
			message('Fehler', 'Sie haben keine g&uuml;ltige E-Mailadresse angegeben!<br>Eine g&uuml;ltige E-Mailadresse hat das Format <b>name@example.com</b> .');
		}
		else
		{
			if( $config['use_email'] )
			{
				// generate a new password
				$user['userpassword'] = substr(md5(microtime()), 0, 6);
				
				thwb_query("UPDATE ".$pref."user SET userpassword='".addslashes(md5($user['userpassword']))."', useremail='".addslashes($user['usernewemail'])."'
					WHERE userid=$g_user[userid]");


				$TMail = new Template("./templates/mail/send_password.mail");
				$user['username'] = $dbuser['username'];
				eval($TMail->GetTemplate("text"));

				@mail($user['usernewemail'], "Login Daten - $config[board_name]", $text, "From: $config[board_admin]");
				
				setcookie("thwb_cookie");
				
				message('Hinweis',
					'Ihre E-Mailadresse wurde geändert und ein neuen Passwort generiert. Das Passwort wurde an Ihre neue E-Mailadresse
					geschickt. Bitte <a href="login.php">loggen</a> Sie sich jetzt neu ein.');
			}
			else
			{
				thwb_query("UPDATE ".$pref."user SET useremail='$user[usernewemail]'
					WHERE userid=$g_user[userid]");
				
				message('Hinweis',
					'Ihre Email-Adresse wurde erfolgreich geändert.');
			}
		}
	}
}

?>