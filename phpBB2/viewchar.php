<?php
define('IN_PHPBB', true);
$phpbb_root_path = './';
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);
include( $phpbb_root_path . "/includes/bbcode.php" );
//
// Start session management
//
$userdata = session_pagestart($user_ip, PAGE_INDEX);
init_userprefs($userdata);
//
// End session management
//

if( !isset($HTTP_GET_VARS['c']) || empty($HTTP_GET_VARS['c']) )
{
  echo "Kein g&uuml;ltiger Charakter";
  die;
}

global $db;

$sql = "SELECT *
  FROM runuo_chars
  WHERE serial='" . str_replace("\'", "''", $HTTP_GET_VARS['c']) . "'";
if ( !($result = $db->sql_query($sql)) )
{
message_die(GENERAL_ERROR, 'Kein g&uuml;ltiger Charakter', '', __LINE__, __FILE__, $sql);
}

$row = $db->sql_fetchrow($result);

if( empty($row['char_name']) )
{
  echo "Kein g&uuml;ltiger Charakter";
  die;
}

$page_title = 'Charakterbogen von ' . $row['char_name'];
$playtime = "Tage: " . date("z", $row['char_playtime']) . "<br />Stunden: " . (date("G", $row['char_playtime']) - 1) . "<br />Minuten: ". date("i", $row['char_playtime']);


include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
'body' => 'viewchar_body.tpl')
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title,
    'CHAR_NAME' => $row['char_name'],
    'CHAR_PROFILE' => $row['char_profile'],
    'CHAR_PLAYTIME' => $playtime,
    'CHAR_RACE' => $row['race']
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>