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
/*  Set page title
*/
    $page_title = 'Der Beginn';


/*  The News-System
*/
    global $db;
    
    $sql = "SELECT *
    FROM " . TOPICS_TABLE . "
    WHERE forum_id=2
      ORDER BY topic_id DESC
      LIMIT 1";
    if ( !($result = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
    }
    
    $row = $db->sql_fetchrow($result);
    
    $news_topic_title = $row['topic_title'];
    $news_topic_poster_id = $row['topic_poster'];
    $news_topic_post_time = create_date($board_config['default_dateformat'], $row['topic_time'], $board_config['board_timezone']);
    $news_topic_replies = $row['topic_replies'];
    $news_topic_id = $row['topic_id'];
    
    $sql = "SELECT post_id
      FROM " . POSTS_TABLE . "
      WHERE topic_id=$news_topic_id
      ORDER BY post_id ASC
      LIMIT 1";
    if ( !($result = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
    }
    $row = $db->sql_fetchrow($result);
    
    $news_post_id = $row['post_id'];
    
    $sql = "SELECT post_text, bbcode_uid
    FROM " . POSTS_TEXT_TABLE . "
    WHERE post_id=$news_post_id";
    if ( !($result = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
    }
    $row = $db->sql_fetchrow($result);
    
    $news_topic_text = $row['post_text'];
    
    $news_topic_text = bbencode_second_pass($news_topic_text, $row['bbcode_uid']);
    
    $news_topic_text = smilies_pass($news_topic_text);
    
    $news_topic_text = str_replace("\n", "\n<br />\n", $news_topic_text);
    
    $sql = "SELECT username
    FROM " . USERS_TABLE . "
    WHERE user_id=$news_topic_poster_id";
    if ( !($result = $db->sql_query($sql)) )
    {
    message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
    }
    $row = $db->sql_fetchrow($result);
    
    $news_topic_poster = $row['username'];

    //Shows links to latest 5 news entrys
    $sql = "SELECT topic_id, topic_title
      FROM " . TOPICS_TABLE . "
      WHERE forum_id=2
      ORDER BY topic_id DESC
      LIMIT 5";
    if ( !($result_topics = $db->sql_query($sql)) )
    {
      message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
    }

    while($row_topics = $db->sql_fetchrow($result_topics))
    {
      $topic_id = $row_topics['topic_id'];
      $topic_title = $row_topics['topic_title'];

      $template->assign_block_vars('newsrow', array(
          'NEWS_ID' => $topic_id,
          'NEWS_TITLE' => $topic_title
      ));
    }

/*  How many characters online?
*/
    $sql = "SELECT COUNT(isonline) AS counter
      FROM runuo_chars
      WHERE isonline=1";
    if ( !($result = $db->sql_query($sql)) )
    {
      message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
    }

    $row = $db->sql_fetchrow($result);
    $isonline_counter = $row['counter'];

    if( $isonline_counter == 1 )
    {
      $isonline_playertext = "spielt";
    } else {
      $isonline_playertext = "spielen";
    }

/*  Last active threads
*/
    $sql = "SELECT p.post_id, t.topic_title, t.topic_id
      FROM " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f
      WHERE p.topic_id = t.topic_id
        AND t.forum_id = f.forum_id
        AND f.auth_view = 0
      ORDER BY p.post_id DESC";
    
    if ( !($result = $db->sql_query($sql)) )
    {
      message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
    }
    
    $topic_ids = null;
    $i = 0;
    $topicrows = 0;
    while(($row = $db->sql_fetchrow($result)) && ($topicrows < 5))
    {
      $post_id = $row['post_id'];
      $topic_title = $row['topic_title'];
      $topic_id = $row['topic_id'];

      $doubletopic = false;
      
      for( $ii = 0; $ii < $i; $ii++ )
      {
        if( $topic_ids[$ii] == $topic_id )
          $doubletopic = true;
      }
      
      $topic_ids[$i] = $topic_id;
      
      $i++;
      
      if( !$doubletopic )
      {
        $template->assign_block_vars('topicrow', array(
            'THREAD_POSTID' => $post_id,
            'THREAD_TOPICTITLE' => $topic_title
        ));
        
        $topicrows++;
      }
    }
    
include($phpbb_root_path . 'includes/page_header.'.$phpEx);

$template->set_filenames(array(
'body' => 'portal_main.tpl')
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title,
    'NEWS_TOPIC_ID' => $news_topic_id,
    'NEWS_TOPIC_TITLE' => $news_topic_title,
    'NEWS_REPLIES' => $news_topic_replies,
    'NEWS_POSTER_ID' => $news_topic_poster_id,
    'NEWS_POSTER' => $news_topic_poster,
    'NEWS_TOPIC_DATE' => $news_topic_post_time,
    'NEWS_TEXT' => $news_topic_text,
    'ISONLINE_COUNTER' => $isonline_counter,
    'ISONLINE_PLAYERTEXT' => $isonline_playertext
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>