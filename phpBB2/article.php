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

/*  Yeah, do some foo
*/
    global $db;
    
    
    $template->set_filenames(array(
        'body' => 'article_body.tpl')
    );


/*  Set page title
*/
    $page_title = 'Die Schriftst&uuml;cksammlung';

/*  Let's get articles out of database
*/
    $sql = "SELECT topic_id, topic_replies, topic_title
      FROM " . TOPICS_TABLE . "
      WHERE forum_id=3
      ORDER BY topic_title";
    if ( !($result_topics = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Artikel gefunden', '', __LINE__, __FILE__, $sql);
    }

    while($row_topics = $db->sql_fetchrow($result_topics))
    {
      $topic_id = $row_topics['topic_id'];
      $topic_title = $row_topics['topic_title'];
      $topic_replies = $row_topics['topic_replies'];

      $sql = "SELECT post_id
        FROM " . POSTS_TABLE . "
        WHERE topic_id=$topic_id
        ORDER BY post_id ASC
        LIMIT 1";
      if ( !($result_posts = $db->sql_query($sql)) )
      {
      message_die(GENERAL_ERROR, 'Keine Artikel gefunden', '', __LINE__, __FILE__, $sql);
      }
      $row_posts = $db->sql_fetchrow($result_posts);

      $post_id = $row_posts['post_id'];

      $sql = "SELECT post_text
        FROM " . POSTS_TEXT_TABLE . "
        WHERE post_id=$post_id";
      if ( !($result_text = $db->sql_query($sql)) )
      {
      message_die(GENERAL_ERROR, 'Keine Artikel gefunden', '', __LINE__, __FILE__, $sql);
      }
      $row_text = $db->sql_fetchrow($result_text);
      $post_text = preg_replace('/\:[0-9a-z\:]+\]/si', ']', substr($row_text['post_text'], 0, 254)) . " ...";

      $template->assign_block_vars('articlerow', array(
          'ARTICLE_ID' => $topic_id,
          'ARTICLE_NAME' => $topic_title,
          'ARTICLE_REPLIES' => $topic_replies,
          'ARTICLE_SMALL_TEXT' => $post_text
      ));
    }
/*  End this crap shit.
*/


include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title,
    'ARTICLE_BOARD_ID' => "3"
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>