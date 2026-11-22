<?php
if ( !defined('IN_PHPBB') )
{
	die("Hacking attempt");
}
include( $phpbb_root_path . "/includes/bbcode.php" );
/*  Importaint Vars
*/
//global $Portal_NewsBoardID = 2;


/*  Returns the database name of the Forum.
*/
function Portal_Woohoo()
{
  return "Weeeee";
}

/*  That sucks pretty much.
*/
function Portal_ShowLastNews()
{
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
  
  $topic_title = $row['topic_title'];
  $topic_posterID = $row['topic_poster'];
  $topic_post_time = create_date($board_config['default_dateformat'], $row['topic_time'], $board_config['board_timezone']);
  $topic_replies = $row['topic_replies'];
  $topic_id = $row['topic_id'];
  
  $sql = "SELECT post_id
    FROM " . POSTS_TABLE . "
    WHERE topic_id=$topic_id
    ORDER BY post_id ASC
    LIMIT 1";
  if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
	}
  $row = $db->sql_fetchrow($result);

  $post_id = $row['post_id'];
  
  $sql = "SELECT post_text, bbcode_uid
  FROM " . POSTS_TEXT_TABLE . "
  WHERE post_id=$post_id";
  if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
	}
  $row = $db->sql_fetchrow($result);
  
  $topic_text = $row['post_text'];
  
  $topic_text = bbencode_second_pass($topic_text, $row['bbcode_uid']);

  $topic_text = smilies_pass($topic_text);
  
  $topic_text = str_replace("\n", "\n<br />\n", $topic_text);
  
  $sql = "SELECT username
  FROM " . USERS_TABLE . "
  WHERE user_id=$topic_posterID";
  if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Keine Neuigkeiten gefunden', '', __LINE__, __FILE__, $sql);
	}
  $row = $db->sql_fetchrow($result);
  
  $topic_poster = $row['username'];
  
  return "<div class=\"headline\">Letzte Ank&uuml;ndigung:</div>\n
  <a href=\"viewtopic.php?t=$topic_id\"<strong>$topic_title</strong></a> - $topic_replies Kommentar(e)\n
  <div class=\"small\">Gepostet von <a href=\"profile.php?mode=viewprofile&u=$topic_posterID\">$topic_poster</a> - $topic_post_time</div>\n
  $topic_text";
}
?>