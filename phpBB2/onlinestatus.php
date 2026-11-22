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
    $page_title = 'Onlinestatus';

/*  Who is online?
*/
    global $db;
    
    $sql = "SELECT char_name, race, accname, serial
      FROM runuo_chars
      WHERE isonline=1";
    if ( !($result_ingame = $db->sql_query($sql)) )
    {
      message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
    }

    while($row_ingame = $db->sql_fetchrow($result_ingame))
    {
      $char_name = $row_ingame['char_name'];
      $char_race = $row_ingame['race'];
      $char_acc = $row_ingame['accname'];
      $char_serial = $row_ingame['serial'];
      
      $sql = "SELECT forum_id, isgm
        FROM runuo_accounts
        WHERE accname=\"$char_acc\"";
      if ( !($result_acc = $db->sql_query($sql)) )
      {
        message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
      }
      $row_acc = $db->sql_fetchrow($result_acc);
      
      $forumuser_id = $row_acc['forum_id'];
      $isgm = $row_acc['isgm'];

      $sql = "SELECT username, user_level
        FROM " . USERS_TABLE . "
        WHERE user_id=$forumuser_id";
      if ( !($result_usracc = $db->sql_query($sql)) )
      {
        message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
      }
      $row_usracc = $db->sql_fetchrow($result_usracc);
      
      $forumuser_name = $row_usracc['username'];

      $template->assign_block_vars('playeronlinerow', array(
          'CHAR_NAME' => $char_name,
          'CHAR_RACE' => $char_race,
          'CHAR_FORUM' => $forumuser_name,
          'CHAR_FORUM_ID' => $forumuser_id,
          'CHAR_SERIAL' => $char_serial
      ));
      if( $isgm )
      {
        $template->assign_block_vars('playeronlinerow.switch_is_gm',array() );
      } else {
        $template->assign_block_vars('playeronlinerow.switch_isno_gm',array() );
      }
    }


include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
'body' => 'onlinestatus_body.tpl')
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>