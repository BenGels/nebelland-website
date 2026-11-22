<?php
define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
//
// Start session management
//
$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);
//
// End session management
//
/*  Set page title
*/
    $page_title = 'Bewerbungsschreiben';

    
global $db;

if( isset($HTTP_GET_VARS['a']) && $HTTP_GET_VARS['a'] == "submit" )
{
  if( (isset($HTTP_POST_VARS['a_username']) && strlen(trim($HTTP_POST_VARS['a_username'])) >= 3) &&
      (isset($HTTP_POST_VARS['a_passw']) && strlen($HTTP_POST_VARS['a_passw']) >=3) &&
      (isset($HTTP_POST_VARS['a_passw2']) && strlen($HTTP_POST_VARS['a_passw2']) >=3) &&
      (isset($HTTP_POST_VARS['a_name']) && !empty($HTTP_POST_VARS['a_name'])) &&
      (isset($HTTP_POST_VARS['a_age']) && !empty($HTTP_POST_VARS['a_age'])) &&
      (isset($HTTP_POST_VARS['a_email']) && !empty($HTTP_POST_VARS['a_email'])) &&
      (isset($HTTP_POST_VARS['a_icq']) && !empty($HTTP_POST_VARS['a_icq'])) &&
      (isset($HTTP_POST_VARS['a_agreement']) && $HTTP_POST_VARS['a_agreement'] == 1) &&
      ($HTTP_POST_VARS['a_passw'] == $HTTP_POST_VARS['a_passw2']) )
  {
    $sql = "SELECT COUNT(*) as counter
      FROM " . USERS_TABLE . "
      WHERE username=\"" . $HTTP_POST_VARS['a_username'] . "\"";
    $sql2 = "SELECT COUNT(*) as counter
      FROM " . USERS_TABLE . "
      WHERE user_email=\"" . $HTTP_POST_VARS['a_email'] . "\"
      OR user_icq=\"" . $HTTP_POST_VARS['a_icq'] . "\"";
    if( !($result = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
    }
    $row = $db->sql_fetchrow($result);
    if( !($result = $db->sql_query($sql2)) )
    {
    message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
    }
    $row2 = $db->sql_fetchrow($result);
    
    if( $row['counter'] != 0 )
    {
      $template->assign_vars(array(
          'APP_MESSAGE' => "<font color=red>Nickname existiert bereits!</font>"
      ));
      $template->set_filenames(array(
      'body' => 'application_body.tpl')
      );
    } else if( $row2['counter'] != 0 ) {
      $template->assign_vars(array(
          'APP_MESSAGE' => "<font color=red>Deine ICQ-Nummer oder E-Mail-Adresse ist bereits registriert!</font>"
      ));
      $template->set_filenames(array(
      'body' => 'application_body.tpl')
      );
    } else if($HTTP_POST_VARS['a_age'] < 10) {
      $template->assign_vars(array(
          'APP_MESSAGE' => "<font color=red>Das Feld \"Alter\" wurde nicht korrekt ausgef&uuml;llt!</font>"
      ));
      $template->set_filenames(array(
      'body' => 'application_body.tpl')
      );
    } else {
			$sql = "SELECT MAX(user_id) AS total
				FROM " . USERS_TABLE;
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain next user_id information', '', __LINE__, __FILE__, $sql);
			}

			if( !($row = $db->sql_fetchrow($result)) )
			{
				message_die(GENERAL_ERROR, 'Could not obtain next user_id information', '', __LINE__, __FILE__, $sql);
			}
			$user_id = $row['total'] + 1;
      
      $sql = "INSERT INTO runuo_playerinfo
        (user_id, applicant, name, age, password)
        VALUES ($user_id, 1, '" . str_replace("\'", "''", $HTTP_POST_VARS['a_name']) . "', " . $HTTP_POST_VARS['a_age'] . ", '" . str_replace("\'", "''", $HTTP_POST_VARS['a_passw']) . "')";
      if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into playinfo table', '', __LINE__, __FILE__, $sql);
			}
      
      $sql = "INSERT INTO " . USERS_TABLE . "	(user_id, username, user_regdate, user_password, user_email, user_icq, user_allow_viewonline, user_notify, user_notify_pm, user_popup_pm, user_timezone, user_dateformat, user_lang, user_level, user_allow_pm, user_active) VALUES ($user_id, '" . str_replace("\'", "''", $HTTP_POST_VARS['a_username']) . "', " . time() . ", '" . str_replace("\'", "''", md5($HTTP_POST_VARS['a_passw'])) . "', '" . str_replace("\'", "''", $HTTP_POST_VARS['a_email']) . "', '" . str_replace("\'", "''", $HTTP_POST_VARS['a_icq']) . "', 1, 1, 0, 0, 1.00, 'd M Y G:i', '" . stripslashes($board_config['default_lang']) . "', 0, 1, 0)";
			if( !($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into users table', '', __LINE__, __FILE__, $sql);
			}

			$sql = "INSERT INTO " . GROUPS_TABLE . " (group_name, group_description, group_single_user, group_moderator)
				VALUES ('', 'Personal User', 1, 0)";
			if( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into groups table', '', __LINE__, __FILE__, $sql);
			}

			$group_id = $db->sql_nextid();

			$sql = "INSERT INTO " . USER_GROUP_TABLE . " (user_id, group_id, user_pending)
				VALUES ($user_id, $group_id, 0)";
			if( !($result = $db->sql_query($sql, END_TRANSACTION)) )
			{
				message_die(GENERAL_ERROR, 'Could not insert data into user_group table', '', __LINE__, __FILE__, $sql);
			}
      
      $template->set_filenames(array(
      'body' => 'application_sent.tpl')
      );
    }
    
  } else {
    $template->assign_vars(array(
        'APP_MESSAGE' => "<font color=red>Es ist ein Fehler aufgetreten, bitte &uuml;berpr&uuml;fen sie ihre Eingabe!</font>"
    ));
    $template->set_filenames(array(
    'body' => 'application_body.tpl')
    );
  }
} else {
  $template->assign_vars(array(
      'APP_MESSAGE' => ""
  ));
  $template->set_filenames(array(
  'body' => 'application_body.tpl')
  );
}

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>