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
include($phpbb_root_path . "includes/staff_inc.php" );
//
// End session management
//
$staffler = CheckStaffler($userdata['user_id'], $staff_groupid);
if(!$staffler){ die; }
/*  Set page title
*/
    $page_title = 'Erweiterte Suche';


/*  Let's have a look on the search function
*/
global $db;
if( isset($HTTP_GET_VARS['a']) && $HTTP_GET_VARS['a'] == "submit" )
{
  $counter = 0;
  $template->assign_block_vars('switch_result', array());
  
  if( $HTTP_POST_VARS['a_type'] == 0 )
  {
    $sql = "SELECT user_id, name
      FROM runuo_playerinfo
      WHERE name like '%" . str_replace("\'", "''", $HTTP_POST_VARS['a_crit']) . "%'";
    $result = $db->sql_query($sql);
    
    while($row = $db->sql_fetchrow($result))
    {
      $counter++;

      $sql = "SELECT username, user_email, user_icq
        FROM " . USERS_TABLE . "
        WHERE user_id=" . $row['user_id'];
      $result_usr = $db->sql_query($sql);
      $row_usr = $db->sql_fetchrow($result_usr);
      
      $template->assign_block_vars('switch_result.resultrow', array(
          'RESULT_NAME' => $row['name'],
          'RESULT_NICK' => $row_usr['username'],
          'RESULT_ICQ' => $row_usr['user_icq'],
          'RESULT_EMAIL' => $row_usr['user_email'],
          'RESULT_USERID' => $row['user_id'],
          'RESULT' => $row['name'],
          'RESULT_COUNTER' => $counter
      ));
    }
  } else if( $HTTP_POST_VARS['a_type'] == 1 ) {
    $sql = "SELECT username, user_email, user_id
      FROM " . USERS_TABLE . "
      WHERE user_icq='" . str_replace("\'", "''", $HTTP_POST_VARS['a_crit']) . "'";
    $result = $db->sql_query($sql);
    
    while($row = $db->sql_fetchrow($result))
    {
      $counter++;

      $sql = "SELECT name
        FROM runuo_playerinfo
        WHERE user_id=" . $row['user_id'];
      $result_inf = $db->sql_query($sql);
      $row_inf = $db->sql_fetchrow($result_inf);
      
      $template->assign_block_vars('switch_result.resultrow', array(
          'RESULT_NAME' => $row_inf['name'],
          'RESULT_NICK' => $row['username'],
          'RESULT_ICQ' => $HTTP_POST_VARS['a_crit'],
          'RESULT_EMAIL' => $row['user_email'],
          'RESULT_USERID' => $row['user_id'],
          'RESULT' => $HTTP_POST_VARS['a_crit'],
          'RESULT_COUNTER' => $counter
      ));
    }
  } else if( $HTTP_POST_VARS['a_type'] == 2 ) {
    $sql = "SELECT username, user_icq, user_id, user_email
      FROM " . USERS_TABLE . "
      WHERE user_email like '%" . str_replace("\'", "''", $HTTP_POST_VARS['a_crit']) . "%'";
    $result = $db->sql_query($sql);
    
    while($row = $db->sql_fetchrow($result))
    {
      $counter++;

      $sql = "SELECT name
        FROM runuo_playerinfo
        WHERE user_id=" . $row['user_id'];
      $result_inf = $db->sql_query($sql);
      $row_inf = $db->sql_fetchrow($result_inf);
      
      $template->assign_block_vars('switch_result.resultrow', array(
          'RESULT_NAME' => $row_inf['name'],
          'RESULT_NICK' => $row['username'],
          'RESULT_ICQ' => $row['user_icq'],
          'RESULT_EMAIL' => $HTTP_POST_VARS['a_crit'],
          'RESULT_USERID' => $row['user_id'],
          'RESULT' => $row['user_email'],
          'RESULT_COUNTER' => $counter
      ));
    }
  } else if( $HTTP_POST_VARS['a_type'] == 3 ) {
    $sql = "SELECT forum_id, accname
      FROM runuo_accounts
      WHERE accname like '%" . str_replace("\'", "''", $HTTP_POST_VARS['a_crit']) . "%'";
    $result = $db->sql_query($sql);
    
    while($row = $db->sql_fetchrow($result))
    {
      $counter++;

      $sql = "SELECT name
        FROM runuo_playerinfo
        WHERE user_id=" . $row['forum_id'];
      $result_inf = $db->sql_query($sql);
      $row_inf = $db->sql_fetchrow($result_inf);
      
      $sql = "SELECT username, user_email, user_icq
        FROM " . USERS_TABLE . "
        WHERE user_id=" . $row['forum_id'];
      $result_usr = $db->sql_query($sql);
      $row_usr = $db->sql_fetchrow($result_usr);
      
      $template->assign_block_vars('switch_result.resultrow', array(
          'RESULT_NAME' => $row_inf['name'],
          'RESULT_NICK' => $row_usr['username'],
          'RESULT_ICQ' => $row_usr['user_icq'],
          'RESULT_EMAIL' => $row_usr['user_email'],
          'RESULT_USERID' => $row['forum_id'],
          'RESULT' => $row['accname'],
          'RESULT_COUNTER' => $counter
      ));
    }
  } else if( $HTTP_POST_VARS['a_type'] == 4 ) {
    $sql = "SELECT accname, char_name
      FROM runuo_chars
      WHERE char_name like '%" . str_replace("\'", "''", $HTTP_POST_VARS['a_crit']) . "%'";
    $result_char = $db->sql_query($sql);
    while($row_char = $db->sql_fetchrow($result_char))
    {
      $sql = "SELECT forum_id
        FROM runuo_accounts
        WHERE accname='" . str_replace("\'", "''", $row_char['accname']) . "'";
      $result = $db->sql_query($sql);
      
      while($row = $db->sql_fetchrow($result))
      {
        $counter++;
        
        $sql = "SELECT name
          FROM runuo_playerinfo
          WHERE user_id=" . $row['forum_id'];
        $result_inf = $db->sql_query($sql);
        $row_inf = $db->sql_fetchrow($result_inf);
        
        $sql = "SELECT username, user_email, user_icq
          FROM " . USERS_TABLE . "
          WHERE user_id=" . $row['forum_id'];
        $result_usr = $db->sql_query($sql);
        $row_usr = $db->sql_fetchrow($result_usr);
        
        $template->assign_block_vars('switch_result.resultrow', array(
            'RESULT_NAME' => $row_inf['name'],
            'RESULT_NICK' => $row_usr['username'],
            'RESULT_ICQ' => $row_usr['user_icq'],
            'RESULT_EMAIL' => $row_usr['user_email'],
            'RESULT_USERID' => $row['forum_id'],
            'RESULT' => $row_char['char_name'],
            'RESULT_COUNTER' => $counter
        ));
      }
    }
  }
}

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
'body' => 'staff/search_body.tpl')
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>