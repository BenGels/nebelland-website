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
include($phpbb_root_path . "includes/acc_inc.php" );
//
// End session management
//
$player = CheckPlayer($userdata['user_id']);
if(!$player){ die; }

/*  Set page title
*/
    $page_title = 'Accountmanagement';

/*  Gets all characters from the accounts of the player
*/
    global $db;
    $sql = "SELECT accname, isgm
      FROM runuo_accounts
      WHERE forum_id='" . $userdata['user_id'] . "'";
    $result = $db->sql_query($sql);
    
    $accounts = "";
    
    while($row = $db->sql_fetchrow($result))
    {
      if( $row['isgm'] == 0 )
      {
        $accname = $row['accname'];
        $sql = "SELECT char_id, race, char_name
          FROM runuo_chars
          WHERE accname='" . str_replace("\'", "''", $row['accname']) . "'";
        $result_char = $db->sql_query($sql);
        while($row_char = $db->sql_fetchrow($result_char))
        {
          $template->assign_block_vars('charrow', array(
              'CHAR_ID' => $row_char['char_id'],
              'CHAR_NAME' => $row_char['char_name'],
              'CHAR_RACE' => $row_char['race']
          ));
        }
      }
      $accounts .= " (" . $row['accname'] . ")";
    }

if( isset($HTTP_GET_VARS['a']) && $HTTP_GET_VARS['a'] == "passw" )
{
  if( $HTTP_POST_VARS['a_passw'] == $HTTP_POST_VARS['a_passw2'] && !empty($HTTP_POST_VARS['a_passw']) )
  {
    $passw = str_replace("\'", "''", $HTTP_POST_VARS['a_passw']);
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUE ('modacc', 'pw', '$passw', '$accname')";
    $result = $db->sql_query($sql);
  }
}
    
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
'body' => 'account_body.tpl')
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title,
    'ACCOUNTS' => $accounts
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>