<?
/*  Get sure to show active players this page only
*/
function CheckPlayer($user_id)
{
    global $db;
    $sql = "SELECT COUNT(*) AS counter
      FROM " . USERS_TABLE . "
      WHERE user_id=$user_id
      AND user_active=1";
    $result = $db->sql_query($sql);
    $player = false;
    while($row = $db->sql_fetchrow($result))
    {
      if($row['counter'] > 0)
      {
        $player = true;
      }
    }
    return $player;
}

function ErrorMessage($text)
{
    return "<br /><font color=\"red\">$text</font>\n";
}

function CheckCharacter($user_id)
{
    global $db;
    
    $sql = "SELECT *
        FROM runuo_charcreation
        WHERE user_id=$user_id";
    $return = $db->sql_query($sql);
    $row = $db->sql_fetchrow($return);
    
    $messages = "";
    
    if( strlen($row['charname']) < 5 )
      $messages .= ErrorMessage("Charaktername zu kurz!");
    
    if( $row['height'] == "" || $row['height'] < 81 )
      $messages .= ErrorMessage("Die K&ouml;rpergr&ouml;&szlig;e ist nicht angegeben oder zu kurz!");
    
    if( $row['profile'] == "" )
      $messages .= ErrorMessage("Das Profil ist leer!");
      
    $statsum = 0;
    $char_attributes = array("St&auml;rke", "Konstitution", "Gewandtheit", "Fingerfertigkeit", "Intelligenz", "Charisma");
    $attributes_names = array("str", "con", "dex", "ms", "intel", "charisma");
    for($i = 0; $i < 6; $i++)
    {
      $messages .= ( $row[$attributes_names[$i]] > 9 && $row[$attributes_names[$i]] < 101 ) ? "" : ErrorMessage($char_attributes[$i] . " ist zu hoch oder zu niedrig (muss zwischen 9 und 101 liegen)!");
      $statsum += $row[$attributes_names[$i]];
    }
  
    if( $statsum < 300 )
      $messages .= ErrorMessage("Du kannst noch " . (300 - $statsum) . " Punkte auf die Stats aufteilen!");
    else if( $statsum > 300 )
      $messages .= ErrorMessage("Du hast " . ($statsum - 300) . " Punkte zuviel verteilt!");
    
    $messages .= CheckSkillCaps($userdata['user_id'], "Combat");
    $messages .= CheckSkillCaps($userdata['user_id'], "ManualSkills");
    $messages .= CheckSkillCaps($userdata['user_id'], "Crafting");
    $messages .= CheckSkillCaps($userdata['user_id'], "Harvesting");
    $messages .= CheckSkillCaps($userdata['user_id'], "Knowledge");
  
    return $messages;
}

function CheckSkillCaps($user_id, $table)
{
    global $db, $charcreation_data;
    
    $sql = "SELECT prof, $table
        FROM runuo_charcreation
        WHERE user_id=$user_id";
    $return = $db->sql_query($sql);
    $row = $db->sql_fetchrow($return);
    
    $skill0 = $charcreation_data["Skillcaps"][$row['prof']][$table][0];
    $skill25 = $charcreation_data["Skillcaps"][$row['prof']][$table][1];
    $skill50 = $charcreation_data["Skillcaps"][$row['prof']][$table][2];
    $skill75 = $charcreation_data["Skillcaps"][$row['prof']][$table][3];
    $skill100 = $charcreation_data["Skillcaps"][$row['prof']][$table][4];
    $skill110 = $charcreation_data["Skillcaps"][$row['prof']][$table][5];
    
    for( $i = 0; $i < strlen($row["$table"]); $i++)
    {
      $selected = $row["$table"]{$i};
      switch($selected){
        case 0: $skill0--; break;
        case 1: $skill25--; break;
        case 2: $skill50--; break;
        case 3: $skill75--; break;
        case 4: $skill100--; break;
        case 5: $skill110--; break;
      }
    }
    
    $returnvar = "";
    
    if( $skill25 < 0 || $skill50 < 0 || $skill75 < 0 || $skill100 < 0 || $skill110 < 0)
      $returnvar = "<br /><font color=\"red\">Ein oder mehr Skills haben einen zu hohen oder niedrigen Capwert!</font>\n";
    else if( $skill25 > 0 || $skill50 > 0 || $skill75 > 0 || $skill100 > 0 || $skill110 > 0)
      $returnvar = "<br /><font color=\"red\">Du kannst noch ein paar Caps verteilen!</font>\n";

    return $returnvar;
}

/*  Gayest thing I have ever done
*/
    $charcreation_data = array(
        //Modification values of the attributes by race
        "Attribute_Mods" => array(
              //   STR CON DEX  MS INT CHR
              //Lichtelb
              array(-1, -2,  1,  0,  1,  1),
              //Mensch
              array( 0,  0,  0,  0,  0,  0),
              //Nachtelb
              array( 1, -1,  1, -1,  0,  0),
              //Zwerg
              array( 2,  2, -2,  0, -1, -1)
        ),//Modification values end here
        
        //List of skillcaps
        "Skillcaps" => array(
              //Professions
              //Crafter
              array(
                      //Groups                  0   25  50  75  100 110
                      "Combat"        => array( 3,  2,  3,  0,  0,  0),
                      "Crafting"      => array( 5,  4,  1,  1,  1,  1),
                      "Harvesting"    => array( 0,  0,  1,  1,  2,  0),
                      "Knowledge"     => array( 4,  3,  7,  0,  2,  0),
                      "ManualSkills" => array( 2,  0,  2,  4,  1,  0)
              ),
              //Warrior
              array(
                      //Groups                  0   25  50  75  100 110
                      "Combat"        => array( 0,  3,  2,  1,  1,  1),
                      "Crafting"      => array( 6,  5,  2,  0,  0,  0),
                      "Harvesting"    => array( 0,  0,  2,  1,  1,  0),
                      "Knowledge"     => array( 4,  3,  5,  3,  1,  0),
                      "ManualSkills" => array( 2,  2,  2,  1,  2,  0)
              ),
              //Magican
              array(
                      //Groups                  0   25  50  75  100 110
                      "Combat"        => array( 4,  1,  3,  0,  0,  0),
                      "Crafting"      => array( 6,  5,  2,  0,  0,  0),
                      "Harvesting"    => array( 0,  3,  1,  0,  0,  0),
                      "Knowledge"     => array( 2,  2,  2,  5,  4,  1),
                      "ManualSkills" => array( 0,  2,  4,  1,  2,  0)
              )
        ),
        
        //List of all skillnames listed in groups
        "Skillnames" => array(
              //Groups
              "Combat"        => array("Verteidigung", "Taktik", "Bogenkampf", "Armbrustkampf", "Axtkampf", "Keulenkampf", "Klingenkampf", "Faustkampf"),
              "Crafting"      => array("Alchemie", "Steinmetz", "Feinschmied", "R&uuml;stschmied", "Waffenschmied", "Schnitzen", "G&auml;rtnern", "Schreinern", "Kartenfertigen", "Kochen", "K&uuml;nstler", "Architekt", "Schneider"),
              "Harvesting"    => array("Prospektor", "Fischen", "Holzhacken", "Minen"),
              "Knowledge"     => array("Heilen", "Tierwissen", "Tierz&auml;hmen", "Tierheilkunde", "Anatomie", "Intelligenz Beurteilen", "Leichenkunde", "Inschrift", "Hirten", "Magie", "Geisterkunde", "Meditation", "Beruhigen", "Enttarnen", "Pflanzenkunde", "Pfadfinden"),
              "ManualSkills" => array("Verstecken", "Schlossknacken", "Magieresistenz", "Spionieren", "Vergiften", "Musizieren", "Schleichen", "Stehlen", "Entsch&auml;rfen")
        ),
        
        //Skill describtions
        "Skilldesc" => array(
              //Groups
              "Combat"        => array(
              "Ausweichwert des Charakters gegen Angriffe, zur Nutzung ist kein Schild erforderlich.",
              "Spiegelt das allgemeine Verständnis für Kampfsituationen wieder. Mindestskill für Rüstungsnutzung.",
              "Wird benötigt um einen Bogen zu benutzen.",
              "Wird benötigt um eine Armbrust zu benutzen.",
              "Wird benötigt um eine Axtwaffe zu benutzen.",
              "Wird benötigt um eine Keulenwaffe zu benutzen.",
              "Wird benötigt um eine Klingenwaffe zu benutzen.",
              "Spiegelt wieder wie gut der Charakter Boxen kann."),
              
              "Crafting"      => array(
              "Das Talent wird benötigt um Tränke mit besonderer Wirkung herzustellen. Einige behaupten gar in dieser Kunst verberge sich das Geheimnis aus Blei Gold zu machen....",
              "Wird benötigt um Steine zu bearbeiten, unter anderem für den Hausbau.",
              "Wird benötigt um Feinwaren herzustellen, zum Beisspiel, Besteck, Teller, Dietriche, Uhren, Schmuck und viele dinge mehr.",
              "Ratet mal ... stimmt ein R&uuml;stschmied stellt Rüstungen aus Metall her.",
              "Das Waffenschmieden wird zur herstellung von Nahkampfwaffen genutzt.",
              "Bögen, Armbrüste, Geschosse, aber auch Figuren und andere kleine Holzdinge stellt ein Schnitzer her.",
              "Das Züchten von Pflanzen und das töpfern ihrer behältnisse hat der Gärtner gelernt.",
              "Der Schreiner stellt Möbel her.",
              "Das lesen und anfertigen von verschiedenen Karten zum Beisspiel für die Seefahrt.",
              "Sollte sich wohl von selbst erklären.",
              "Künstler malen Bilder oder fertigen Statuetten und Statuen.",
              "Plant und baut Änderungen an Haus und Hof.",
              "Eine grosse Kleidungsauswahl und Lederrüstungen stellt der Schneider her."),
              
              "Harvesting"    => array(
              "Damit baut man verschiedene Steinsorten und Edelsteine ab.",
              "Damit fängt man Fische mit einer Angel.",
              "Damit baut man verschiedene Holzsorten ab.",
              "Damit baut man verschiedene Erzsorten ab."),
              
              "Knowledge"     => array(
              "Erste Hilfe mit Verbänden, bekämpfen von Krankheiten...Heilkundige kennen sich aus mit Wehwehchen und schweren Verletzungen.",
              "Wird benötigt um mit Tieren umgehen zu können.",
              "Wird benötigt um Wilde Tiere zähmen zu können.",
              "Im Gegensatz zur normalen Heilkunde kann man hiermit nur Tiere behandeln.",
              "Den Körper und seine Funktionen zu kennen ist wichtig für die Heilkunst.",
              "Versucht die Intelligenz anderer Wesen einzuschätzen.",
              "Gibt informationen über eine Leiche.",
              "Damit schreibt man und kopiert Bücher.",
              "Damit kann man Tiere führen die nicht auf einen hören wollen.",
              "Alle Charaktere wlche übernatürliche Fähigkeiten haben aus den Zaubererklassen brauchen dieses Talent zur Ausübung ihrer Fähigkeiten.",
              "Ist wichtig um die Geister zu kennen und anbeten zu können.",
              "Fähigkeit die beeinfluss wie schnell der Charakter seine Geisteskraft wieder erholt.",
              "Wird zusammen mit Musikinstrumenten und Musizieren benutzt um aggressive Wesen zu beruhigen.",
              "Damit kann man versuchen Versteckte oder Unsichtbare Wesen zu finden.",
              "Das Wissen um Pflanzen und die korrekte Art selbige zu sammeln.",
              "Pfadfinder können die Spuren der Wildnis finden und diese auch erkennen und zuordnen."),
              
              "ManualSkills" => array(
              "Zeigt an wie gut der Charakter sich verstecken kann.",
              "Der Umgang mit dem Dietrich um sich an Schlössern zu versuchen spiegelt sich hier wieder. Auf Nebelland sind in vielen Dungeons Kisten aufgestellt in denen Gold liegt während die NPC's eher wenig bis gar kein Gold im Loot haben. Dietriche und dieses Talent sind also unter Umständen zumindest minimal wichtig und sollten nicht vernachlässigt werden.",
              "Die eigene Willenstärke um der Beeinflussung durch Magie zu widerstehen.",
              "Damit kann man versuchen anderen in ihre Taschen zu sehen.",
              "Die eigene befähigung Waffen oder Nahrung zu vergiften wird hier angezeigt.",
              "Zeigt an wie gut der Charakter es versteht ein Instrument zu benutzen.",
              "Die unerkannte und ungehörte Bewegung. Nur wer sich erfolgreich versteckt hat kann auch Schleichen.",
              "Damit kann man versuchen Sachen aus Taschen zu stehlen in die man schaut.",
              "Damit kann man Fallen unwirksam machen, zum Beisspiel die sicherungen alter Schätze in tiefen Gewölben.")
        ) 
    );
    
    
    $caprow = array("0", "25", "50", "75", "100", "110");
?>