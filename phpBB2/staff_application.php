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
    $page_title = 'Spielerbewerbungen';


global $db;
if( isset($HTTP_GET_VARS['a']) && $HTTP_GET_VARS['a'] == "submit" )
{
  $sql = "UPDATE runuo_playerinfo
    SET applicant=0, comment='" . str_replace("\'", "''", $HTTP_POST_VARS['a_comment']) . "'
    WHERE user_id=" . $HTTP_POST_VARS['a_uid'];
  $db->sql_query($sql);
  
  if( $HTTP_POST_VARS['a_accept'] == 1 )
  {
    
    $sql = "UPDATE " . USERS_TABLE . "
      SET user_active=1
      WHERE user_id=" . $HTTP_POST_VARS['a_uid'];
    $db->sql_query($sql);
    
    $sql = "SELECT accname
      FROM runuo_accounts
      WHERE isgm=0
      ORDER BY accname ASC";
    $result = $db->sql_query($sql);
    $accnumber = 0;
    while($row = $db->sql_fetchrow($result))
    {
      if(substr($row['accname'], 0, 7) == "spieler")
      {
        $sub = substr($row['accname'], 7, 4);
        if($accnumber < $sub){
          $accnumber = $sub;
        }
      }
    }
    $accnumber++;
    while(strlen($accnumber) < 4)
    {
      $accnumber = "0" . $accnumber;
    }
    
    $sql = "SELECT password, user_id
      FROM runuo_playerinfo
      WHERE user_id=" . $HTTP_POST_VARS['a_uid'];
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUE ('crtacc', 'spieler$accnumber', '" .  str_replace("\'", "''", $row['password']) . "', '" . $row['user_id'] . "')";
    $db->sql_query($sql);
  }
}
/*  Gets all applications
*/
    $sql = "SELECT user_id, name, age
      FROM runuo_playerinfo
      WHERE applicant=1";
    $result = $db->sql_query($sql);
    $counter = 0;
    while($row = $db->sql_fetchrow($result))
    {
      $counter++;
      $app_user_id = $row['user_id'];
      $app_name = $row['name'];
      $app_age = $row['age'];
      
      $sql = "SELECT username, user_email, user_icq
        FROM " . USERS_TABLE . "
        WHERE user_id=$app_user_id";
      $result_usr = $db->sql_query($sql);
      $row_usr = $db->sql_fetchrow($result_usr);
      $template->assign_block_vars('approw', array(
          'APP_NAME' => $app_name,
          'APP_AGE' => $app_age,
          'APP_NICK' => $row_usr['username'],
          'APP_ICQ' => $row_usr['user_icq'],
          'APP_EMAIL' => $row_usr['user_email'],
          'APP_USERID' => $app_user_id
      ));
    }


include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
    'body' => 'staff/application_body.tpl')
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>