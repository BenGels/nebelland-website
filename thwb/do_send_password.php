<?php
/* $Id: do_send_password.php,v 1.7 2003/10/11 18:01:34 thed_o_n Exp $ */
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

/* 11/12/2001: since passwords are encrypted we need to
 *   to send the current *passwordhash* to the email address
 *   and wait for a confirmation with this hash.
 */

$text = '';

if( $config['use_email'] )
{
	$r_user = thwb_query("SELECT username, userpassword, useremail FROM ".$pref."user WHERE username='".addslashes($username)."' AND useremail='".addslashes($email)."'");
	if( mysql_num_rows($r_user) != 1 )
	{
		message("Fehler", "Es gibt leider keinen Benutzer mit diesem Namen und dieser Email!");
	}
	else
	{
		$user = mysql_fetch_array($r_user);
		if( !isset($hash) || !$hash )
		{
		  if(!isset($HTTP_POST_VARS['Submit']))
		    {
		      die("denied");
		    }
			$TMail = new Template("./templates/mail/send_password_request.mail");

			$username = rawurlencode($user['username']);
			eval($TMail->GetTemplate("text"));

			@mail($email, "Passwort vergessen - $config[board_name]", $text, "From: $config[board_admin]");
			message("Best&#xE4;tigung", "Es wurde eine Mail an die angegebene Email Adresse geschickt. Bitte lesen Sie diese, um die Passwort&#xE4;nderung zu vervollst&#xE4;ndigen.");
		}
		else
		{
			if( $hash != $user['userpassword'] )
			{
				message("Fehler", "Ung&#xFC;ltiger Hash.");
			}
			else
			{
				$TMail = new Template("./templates/mail/send_password.mail");

				/* generate a new password */
				$user['userpassword'] = substr(md5(microtime()), 0, 16);

				thwb_query("UPDATE ".$pref."user SET userpassword='" . md5($user['userpassword']) . "' WHERE username='" . addslashes($username) . "'");

				eval($TMail->GetTemplate("text"));
				@mail($email, "Passwort vergessen - $config[board_name]", $text, "From: $config[board_admin]");

				message("Passwort gesendet", "Ein neues Passwort wurde generiert und an Ihre Email-Adresse geschickt.");
			}
		}
	}
}
else
{
	message('Sorry',
		'Der Forumadministrator hat diese Funktion leider deaktiviert. Wenn Sie Ihr Passwort vergessen haben, wenden
		Sie sich bitte an den <a href="mailto:' . $config['board_admin'] . '">Forumadministrator</a>.');
}

?>
