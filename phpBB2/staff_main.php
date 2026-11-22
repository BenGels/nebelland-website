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
    $page_title = 'Staffmen&uuml;';


/*  Gets all applications
*/
    global $db;
    $sql = "SELECT COUNT(*) AS counter
      FROM runuo_playerinfo
      WHERE applicant=1";
    if ( !($result = $db->sql_query($sql)) )
    {
      message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
    }
    $row = $db->sql_fetchrow($result);
    $applicant_counter = $row['counter'];
    $applications = "$applicant_counter";

include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
'body' => 'staff/main_body.tpl')
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title,
    'APPLICATIONS' => $applications
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>