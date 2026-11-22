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
/*  Do some foo... again.
*/
    global $db;

    $template->set_filenames(array(
        'body' => 'viewarticle_body.tpl')
    );
    
/*  ... and set page title.
*/
    $page_title = "Schriftst&uuml;ck - ";

/*  Looks for article and so on...
*/

    if ( isset($HTTP_GET_VARS['a']) )
    {
    	$topic_id = $HTTP_GET_VARS['a'];
    } else {
      message_die(GENERAL_ERROR, 'Keine Artikel gefunden', '', __LINE__, __FILE__, $sql);
    }

    $sql = "SELECT topic_replies, topic_title, topic_poster
      FROM " . TOPICS_TABLE . "
      WHERE topic_id=$topic_id";
    if ( !($result_topic = $db->sql_query($sql)) )
    {
      message_die(GENERAL_ERROR, 'Keine Artikel gefunden', '', __LINE__, __FILE__, $sql);
    }
    
    $row_topic = $db->sql_fetchrow($result_topic);
    $topic_replies = $row_topic['topic_replies'];
    $topic_title = $row_topic['topic_title'];
    $topic_poster_id = $row_topic['topic_poster'];
    
    $page_title .= $topic_title;
    
    $sql = "SELECT post_id
      FROM " . POSTS_TABLE . "
      WHERE topic_id=$topic_id
      ORDER BY post_id ASC
      LIMIT 1";
    if ( !($result_post = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Artikel gefunden', '', __LINE__, __FILE__, $sql);
    }
    $row_post = $db->sql_fetchrow($result_post);
    $post_id = $row_post['post_id'];
    
    $sql = "SELECT post_text, bbcode_uid
      FROM " . POSTS_TEXT_TABLE . "
      WHERE post_id=$post_id";
    if ( !($result_text = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Artikel gefunden', '', __LINE__, __FILE__, $sql);
    }
    $row_text = $db->sql_fetchrow($result_text);
    $post_text = bbencode_second_pass($row_text['post_text'], $row_text['bbcode_uid']);
    $post_text = smilies_pass($post_text);
    $post_text = str_replace("\n", "\n<br />\n", $post_text);
    
    $sql = "SELECT username
    FROM " . USERS_TABLE . "
    WHERE user_id=$topic_poster_id";
    if ( !($result_poster = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
    }
    $row_poster = $db->sql_fetchrow($result_poster);
    $topic_poster = $row_poster['username'];


include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title . $topic_title,
    'ARTICLE_TITLE' => $topic_title,
    'ARTICLE_POSTER_ID' => $topic_poster_id,
    'ARTICLE_POSTER' => $topic_poster,
    'ARTICLE_TEXT' => $post_text,
    'ARTICLE_ID' => $topic_id,
    'ARTICLE_REPLIES' => $topic_replies
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>