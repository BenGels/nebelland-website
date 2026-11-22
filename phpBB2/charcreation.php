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

global $db, $charcreation_data, $caprow;

$sql = "SELECT COUNT(*) AS counter
  FROM runuo_charcreation
  WHERE user_id=" . $userdata['user_id'];
$return = $db->sql_query($sql);
$row = $db->sql_fetchrow($return);

if( $row['counter'] == 0 )
{
  $sql = "INSERT INTO runuo_charcreation
    (user_id)
    VALUES (" . $userdata['user_id'] . ")";
  $result = $db->sql_query($sql);
}

if( isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 1 )
{
  $sql = "UPDATE runuo_charcreation
    SET charname='" . str_replace("\'", "''", $HTTP_POST_VARS['a_charname']) . "', race=" . $HTTP_POST_VARS['a_race'] . ", gender=" . $HTTP_POST_VARS['a_gender'] . ", weight=" . $HTTP_POST_VARS['a_weight'] . ", height='" . str_replace("\'", "''", $HTTP_POST_VARS['a_height']) . "', profile='" . str_replace("\'", "''", $HTTP_POST_VARS['a_profile']) . "'
    WHERE user_id=" . $userdata['user_id'];
  if ( !($result = $db->sql_query($sql)) )
  {
  	message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
  }
} else if ( isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 2 )
{
  $sql = "UPDATE runuo_charcreation
    SET prof=" . $HTTP_POST_VARS['a_prof'] . ", str='" . str_replace("\'", "''", $HTTP_POST_VARS['a_str']) . "', con='" . str_replace("\'", "''", $HTTP_POST_VARS['a_con']) . "', dex='" . str_replace("\'", "''", $HTTP_POST_VARS['a_dex']) . "', ms='" . str_replace("\'", "''", $HTTP_POST_VARS['a_ms']) . "', intel='" . str_replace("\'", "''", $HTTP_POST_VARS['a_int']) . "', charisma='" . str_replace("\'", "''", $HTTP_POST_VARS['a_chr']) . "'
    WHERE user_id=" . $userdata['user_id'];
  if ( !($result = $db->sql_query($sql)) )
  {
  	message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
  }
} else if ( isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 3 )
{
  $combat_skills = $manual_skills = "";
  for( $i = 0; $i < 8; $i++ )
  {
    $combat_skills .= $HTTP_POST_VARS["a_combat$i"];
  }
  
  for( $i = 0; $i < 9; $i++ )
  {
    $manual_skills .= $HTTP_POST_VARS["a_manual$i"];
  }
  
  $sql = "UPDATE runuo_charcreation
    SET combat='" . $combat_skills . "', manualskills='" . $manual_skills . "'
    WHERE user_id=" . $userdata['user_id'];
  if ( !($result = $db->sql_query($sql)) )
  {
  	message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
  }
} else if ( isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 4 )
{
  $crafting_skills = $harvesting_skills = "";
  for( $i = 0; $i < 13; $i++ )
  {
    $crafting_skills .= $HTTP_POST_VARS["a_crafting$i"];
  }
  
  for( $i = 0; $i < 4; $i++ )
  {
    $harvesting_skills .= $HTTP_POST_VARS["a_harvesting$i"];
  }
  
  $sql = "UPDATE runuo_charcreation
    SET crafting='" . $crafting_skills . "', harvesting='" . $harvesting_skills . "'
    WHERE user_id=" . $userdata['user_id'];
  if ( !($result = $db->sql_query($sql)) )
  {
  	message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
  }
} else if ( isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 5 )
{
  $knowledge_skills = "";
  for( $i = 0; $i < 16; $i++ )
  {
    $knowledge_skills .= $HTTP_POST_VARS["a_knowledge$i"];
  }
  
  $sql = "UPDATE runuo_charcreation
    SET knowledge='" . $knowledge_skills . "'
    WHERE user_id=" . $userdata['user_id'];
  if ( !($result = $db->sql_query($sql)) )
  {
  	message_die(GENERAL_ERROR, '', '', __LINE__, __FILE__, $sql);
  }
}


$stepno = (isset($HTTP_GET_VARS['step'])) ? $HTTP_GET_VARS['step'] : $HTTP_GET_VARS['savestep'];
$page_title = 'Charaktererstellung - Schritt ' . $stepno;
$template_site = 'charcreation/step' . $stepno . '_body.tpl';
if( (isset($HTTP_GET_VARS['step']) && $HTTP_GET_VARS['step'] == 1) ||
    (isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 1))
{
  $sql = "SELECT charname, race, gender, weight, height, profile
    FROM runuo_charcreation
    WHERE user_id=" . $userdata['user_id'];
  $return = $db->sql_query($sql);
  $row = $db->sql_fetchrow($return);


  $char_race = "";
  $possible_races = array("Lichtelb", "Mensch", "Nachtelb", "Zwerg");
  for($i = 0; $i < 4; $i++)
  {
    $selected = ( $i == $row['race'] ) ? " selected=\"selected\"" : "";
    $char_race .= "<option value=\"$i\"$selected>" . $possible_races[$i] . "</option>";
  }
  
  
  $char_gender = "";
  $possible_genders = array("m&auml;nnlich", "weiblich");
  for($i = 0; $i < 2; $i++)
  {
    $selected = ( $i == $row['gender'] ) ? " selected=\"selected\"" : "";
    $char_gender .= "<option value=\"$i\"$selected>" . $possible_genders[$i] . "</option>";
  }
  
  
  $char_weight = "";
  $possible_weights = array("schmachtig", "schlank", "normal", "bullig", "kr&auml;ftig", "fett");
  for($i = 0; $i < 6; $i++)
  {
    $selected = ( $i == $row['weight'] ) ? " selected=\"selected\"" : "";
    $char_weight .= "<option value=\"$i\"$selected>" . $possible_weights[$i] . "</option>";
  }
  
  $messages = "";
  $possible_errors = array("Kein Name vorhanden!", "Kein Profil vorhanden!");
  $field_names = array("charname", "profile");
  for($i = 0; $i < 3; $i++)
  {
    $messages .= ( $row[$field_names[$i]] == "" ) ? "<br /><font color=\"red\">" . $possible_errors[$i] . "</font>" : "";
  }
  
  $template->assign_vars(array(
      'CHAR_NAME' => $row['charname'],
      'RACE' => $char_race,
      'GENDER' => $char_gender,
      'WEIGHT' => $char_weight,
      'HEIGHT' => $row['height'],
      'CHAR_PROFILE' => $row['profile'],
      'MESSAGES' => $messages
  ));
} else if( (isset($HTTP_GET_VARS['step']) && $HTTP_GET_VARS['step'] == 2) ||
           (isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 2))
{
  $sql = "SELECT prof, str, con, dex, ms, intel, charisma, race
    FROM runuo_charcreation
    WHERE user_id=" . $userdata['user_id'];
  $return = $db->sql_query($sql);
  $row = $db->sql_fetchrow($return);

  $char_prof = "";
  $possible_profs = array("Handwerker", "K&auml;mpfer/Sch&uuml;tze", "Magier");
  for($i = 0; $i < 3; $i++)
  {
    $selected = ( $i == $row['prof'] ) ? " selected=\"selected\"" : "";
    $char_prof .= "<option value=\"$i\"$selected>" . $possible_profs[$i] . "</option>";
  }
  
  $messages = "";
  $statsum = 0;
  $char_attributes = array("St&auml;rke", "Konstitution", "Gewandtheit", "Fingerfertigkeit", "Intelligenz", "Charisma");
  $attributes_names = array("str", "con", "dex", "ms", "intel", "charisma");
  for($i = 0; $i < 6; $i++)
  {
    $messages .= ( $row[$attributes_names[$i]] > 9 && $row[$attributes_names[$i]] < 101 ) ? "" : "<br /><font color=\"red\">" . $char_attributes[$i] . " ist zu hoch oder zu niedrig!</font>\n";
    $statsum += $row[$attributes_names[$i]];
  }
  if( $statsum < 300 )
    $messages .= "<br \><font color=\"red\">Du kannst noch " . (300 - $statsum) . " Punkte auf die Stats aufteilen!</font>\n";
  else if( $statsum > 300 )
    $messages .= "<br \><font color=\"red\">Du hast " . ($statsum - 300) . " Punkte zuviel verteilt!</font>\n";
  
  $template->assign_vars(array(
      'PROFESSION' => $char_prof,
      'CHAR_STR' => $row['str'],
      'CHAR_CON' => $row['con'],
      'CHAR_DEX' => $row['dex'],
      'CHAR_MS' => $row['ms'],
      'CHAR_INT' => $row['intel'],
      'CHAR_CHR' => $row['charisma'],
      'MOD_STR' => $charcreation_data["Attribute_Mods"][$row['race']][0],
      'MOD_CON' => $charcreation_data["Attribute_Mods"][$row['race']][1],
      'MOD_DEX' => $charcreation_data["Attribute_Mods"][$row['race']][2],
      'MOD_MS' => $charcreation_data["Attribute_Mods"][$row['race']][3],
      'MOD_INT' => $charcreation_data["Attribute_Mods"][$row['race']][4],
      'MOD_CHR' => $charcreation_data["Attribute_Mods"][$row['race']][5],
      'CAL_HP' => (int)(80 + ($row['con']/ 2)),
      'CAL_REF' => (int)(80 + ( ($row['intel'] + $row['dex']) / 4 )),
      'CAL_STAM' => (int)(80 + ($row['str'] / 2)),
      'CAL_ASP' => (int)(80 + ( ($row['charisma'] + $row['intel']) / 4)),
      'MESSAGES' => $messages
  ));
} else if( (isset($HTTP_GET_VARS['step']) && $HTTP_GET_VARS['step'] == 3) ||
           (isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 3))
{
  $sql = "SELECT prof, combat, manualskills
    FROM runuo_charcreation
    WHERE user_id=" . $userdata['user_id'];
  $return = $db->sql_query($sql);
  $row = $db->sql_fetchrow($return);
  
  for( $i = 0; $i < 8; $i++ )
  {
    $selected_cap = $row['combat']{$i};
    $skillcap = "<td nowrap>";
    for( $ii = 0; $ii < 6; $ii++ )
    {
      $selected = ( $selected_cap == $ii ) ? " checked=\"checked\"" : "";
      $skillcap .= "<input type=\"radio\" name=\"a_combat$i\" value=\"$ii\"$selected>" . $caprow[$ii];
    }
    $skillcap .= "</td>\n";
    $template->assign_block_vars('combatskillrow', array(
        'NAME' => $charcreation_data["Skillnames"]["Combat"][$i],
        'SKILLCAP' => $skillcap,
        'DESCRIPTION' => $charcreation_data["Skilldesc"]["Combat"][$i]
    ));
  }
  
  for( $i = 0; $i < 9; $i++ )
  {
    $selected_cap = $row['manualskills']{$i};
    $skillcap = "<td nowrap>";
    for( $ii = 0; $ii < 6; $ii++ )
    {
      $selected = ( $selected_cap == $ii ) ? " checked=\"checked\"" : "";
      $skillcap .= "<input type=\"radio\" name=\"a_manual$i\" value=\"$ii\"$selected>" . $caprow[$ii];
    }
    $skillcap .= "</td>\n";
    $template->assign_block_vars('otherskillrow', array(
        'NAME' => $charcreation_data["Skillnames"]["ManualSkills"][$i],
        'SKILLCAP' => $skillcap,
        'DESCRIPTION' => $charcreation_data["Skilldesc"]["ManualSkills"][$i]
    ));
  }
  
  $combat_skills = $other_skills = "";
  for( $i = 0; $i < 6; $i++ )
  {
    $combat_skills .= "| <strong>" . $charcreation_data["Skillcaps"][$row['prof']]["Combat"][$i] . "</strong> mal " . $caprow[$i] . " ";
    $other_skills .= "| <strong>" . $charcreation_data["Skillcaps"][$row['prof']]["ManualSkills"][$i] . "</strong> mal " . $caprow[$i] . " ";
  }
  
  $messages = "";
  $messages .= CheckSkillCaps($userdata['user_id'], "combat");
  $messages .= CheckSkillCaps($userdata['user_id'], "ManualSkills");
  
  $template->assign_vars(array(
      'COMBAT_SKILLS' => $combat_skills,
      'OTHER_SKILLS' => $other_skills,
      'MESSAGES' => $messages
  ));
} else if( (isset($HTTP_GET_VARS['step']) && $HTTP_GET_VARS['step'] == 4) ||
           (isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 4))
{
  $sql = "SELECT prof, crafting, harvesting
    FROM runuo_charcreation
    WHERE user_id=" . $userdata['user_id'];
  $return = $db->sql_query($sql);
  $row = $db->sql_fetchrow($return);

  for( $i = 0; $i < 13; $i++ )
  {
    $selected_cap = $row['crafting']{$i};
    $skillcap = "<td nowrap>";
    for( $ii = 0; $ii < 6; $ii++ )
    {
      $selected = ( $selected_cap == $ii ) ? " checked=\"checked\"" : "";
      $skillcap .= "<input type=\"radio\" name=\"a_crafting$i\" value=\"$ii\"$selected>" . $caprow[$ii];
    }
    $skillcap .= "</td>\n";
    $template->assign_block_vars('craftingskillrow', array(
        'NAME' => $charcreation_data["Skillnames"]["Crafting"][$i],
        'SKILLCAP' => $skillcap,
        'DESCRIPTION' => $charcreation_data["Skilldesc"]["Crafting"][$i]
    ));
  }
  
  for( $i = 0; $i < 4; $i++ )
  {
    $selected_cap = $row['harvesting']{$i};
    $skillcap = "<td nowrap>";
    for( $ii = 0; $ii < 6; $ii++ )
    {
      $selected = ( $selected_cap == $ii ) ? " checked=\"checked\"" : "";
      $skillcap .= "<input type=\"radio\" name=\"a_harvesting$i\" value=\"$ii\"$selected>" . $caprow[$ii];
    }
    $skillcap .= "</td>\n";
    $template->assign_block_vars('harvestingskillrow', array(
        'NAME' => $charcreation_data["Skillnames"]["Harvesting"][$i],
        'SKILLCAP' => $skillcap,
        'DESCRIPTION' => $charcreation_data["Skilldesc"]["Harvesting"][$i]
    ));
  }
  
  $crafting_skills = $harvesting_skills = "";
  for( $i = 0; $i < 6; $i++ )
  {
    $crafting_skills .= "| <strong>" . $charcreation_data["Skillcaps"][$row['prof']]["Crafting"][$i] . "</strong> mal " . $caprow[$i] . " ";
    $harvesting_skills .= "| <strong>" . $charcreation_data["Skillcaps"][$row['prof']]["Harvesting"][$i] . "</strong> mal " . $caprow[$i] . " ";
  }
  
  $messages = "";
  $messages .= CheckSkillCaps($userdata['user_id'], "Crafting");
  $messages .= CheckSkillCaps($userdata['user_id'], "Harvesting");
  
  $template->assign_vars(array(
      'CRAFTING_SKILLS' => $crafting_skills,
      'HARVESTING_SKILLS' => $harvesting_skills,
      'MESSAGES' => $messages
  ));
} else if( (isset($HTTP_GET_VARS['step']) && $HTTP_GET_VARS['step'] == 5) ||
           (isset($HTTP_GET_VARS['savestep']) && $HTTP_GET_VARS['savestep'] == 5))
{
  $sql = "SELECT prof, knowledge
    FROM runuo_charcreation
    WHERE user_id=" . $userdata['user_id'];
  $return = $db->sql_query($sql);
  $row = $db->sql_fetchrow($return);

  for( $i = 0; $i < 16; $i++ )
  {
    $selected_cap = $row['knowledge']{$i};
    $skillcap = "<td nowrap>";
    for( $ii = 0; $ii < 6; $ii++ )
    {
      $selected = ( $selected_cap == $ii ) ? " checked=\"checked\"" : "";
      $skillcap .= "<input type=\"radio\" name=\"a_knowledge$i\" value=\"$ii\"$selected>" . $caprow[$ii];
    }
    $skillcap .= "</td>\n";
    $template->assign_block_vars('knowledgeskillrow', array(
        'NAME' => $charcreation_data["Skillnames"]["Knowledge"][$i],
        'SKILLCAP' => $skillcap,
        'DESCRIPTION' => $charcreation_data["Skilldesc"]["Knowledge"][$i]
    ));
  }
  
  $knowledge_skills = "";
  for( $i = 0; $i < 6; $i++ )
  {
    $knowledge_skills .= "| <strong>" . $charcreation_data["Skillcaps"][$row['prof']]["Knowledge"][$i] . "</strong> mal " . $caprow[$i] . " ";
  }
  
  $messages = "";
  $messages .= CheckSkillCaps($userdata['user_id'], "Knowledge");
  
  $template->assign_vars(array(
      'KNOWLEDGE_SKILLS' => $knowledge_skills,
      'MESSAGES' => $messages
  ));
} else if (isset($HTTP_GET_VARS['step']) && $HTTP_GET_VARS['step'] == 6) {
  $messages = CheckCharacter($userdata['user_id']);
  
  if($messages == "")
    $template->assign_block_vars('switch_chardone', array());
  else
    $template->assign_block_vars('switch_charnotdone', array());
  
  $template->assign_vars(array(
      'MESSAGES' => $messages
  ));
    
} else if (isset($HTTP_GET_VARS['a']) && $HTTP_GET_VARS['a'] == "resetchar") {
  $sql = "DELETE FROM runuo_char
        WHERE user_id=" . $userdata['user_id'];
  $result = $db->sql_query($sql);
} else if (isset($HTTP_GET_VARS['a']) && $HTTP_GET_VARS['a'] == "createchar") {
  $messages = CheckCharacter($userdata['user_id']);
  
  if( $messages == "" )
  {
    $sql = "SELECT *
            FROM runuo_charcreation
            WHERE user_id=" . $userdata['user_id'];
    $return = $db->sql_query($sql);
    $row = $db->sql_fetchrow($return);

    $sql2 = "SELECT accname
             FROM runuo_accounts
             WHERE forum_id=" . $userdata['user_id'];
    $return_acc = $db->sql_query($sql2);
    $row_acc = $db->sql_fetchrow($return_acc);
    
    
    //crtcha
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('crtcha', '" . str_replace("\'", "''", $row['charname']) . "', '" . $row['gender'] . "', '" . str_replace("\'", "''", $row_acc['accname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha race
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'race', '" . $row['race'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha weight
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'weight', '" . $row['weight'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha height
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'height', '" . $row['height'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha profile
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'profile', '" . str_replace("\'", "''", $row['profile']) . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha profession
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'prof', '" . $row['prof'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha str cap
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'strcap', '" . $row['str'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha con cap
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'concap', '" . $row['con'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha dex cap
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'dexcap', '" . $row['dex'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha ms cap
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'mscap', '" . $row['ms'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha int cap
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'intcap', '" . $row['intel'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha cha cap
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'chacap', '" . $row['charisma'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha combat skills
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'combat', '" . $row['combat'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha manual skills
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'manualskills', '" . $row['manualskills'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha crafting skills
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'crafting', '" . $row['crafting'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha harvesting skills
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'harvesting', '" . $row['harvesting'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
    
    //modcha knowledge skills
    $sql = "INSERT INTO runuo_cmd (cmd, cmdProperty, cmdValue, cmdWhere) VALUEs ('modcha', 'knowledge', '" . $row['knowledge'] . "', '" . str_replace("\'", "''", $row['charname']) . "')";
    $return = $db->sql_query($sql);
  }
  
  $template_site = 'charcreation/done.tpl';
  $page_title = 'Charaktererstellung - Vorwort';
} else {
  $template_site = 'charcreation/foreword_body.tpl';
  $page_title = 'Charaktererstellung - Vorwort';
}


include($phpbb_root_path . 'includes/page_header.'.$phpEx);


$template->set_filenames(array(
  'body' => $template_site)
);

$template->assign_vars(array(
    'PAGE_TITLE' => $page_title
));

$template->pparse('body');

include($phpbb_root_path . 'includes/page_tail.'.$phpEx);
?>