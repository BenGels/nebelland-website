<?php
/* $Id: polls.php,v 1.0 2004/06/13 13:39:10 narodnaja Exp $ */
/*
                 Pollhack fuer ThWBoard 2.84
        ==============================================
          (c) 2004 by
          Florian Bauer    <florian.bauer@faenger.de>
			   
          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================

*/

include "common.inc.php";

$t = 1;

if( $action == "logout" )
{
	session_destroy();
	header("Location: index.php");
   exit;
}

tb_header();


/*
 * ==========================================
 *              Poll Settings
 * ==========================================
 */
if (!$action)
{
	print '
<form method="post" action="polls.php">
  <table width="100%" border="0" cellspacing="0" cellpadding="4">';

	// key/val
	$a_registry = array();
	$r_registry = query("SELECT keyname, keyvalue, keytype, keydescription, keydetails, keygroupid, 
		keydisplayorder
		FROM $pref"."registry WHERE keyname LIKE '%poll%' ORDER BY keydisplayorder ASC");
	//print(mysql_num_rows($r_registry));
	$i = 0;
		print '
    <tr>
      <td colspan="2" bgcolor="#999999">
        <font size="2" color="white"><b>Poll Options</b></font>
      </td>
    </tr>';
	while( $registry = mysql_fetch_array($r_registry) )
	{
		$a_registry[$registry['keygroupid']][] = $registry;

	print '
    <tr>
      <td'.($i % 2 == 0 ? ' bgcolor="#eeeeee"' : '').' valign="top" width="50%"><b>'.$registry['keydescription'].'</b>';
	  		if( $registry['keydetails'] )
			{
				print '<font size="1"><br>'.$registry['keydetails'].'</font>';
			}
			print '</td>';
	  
			print'
      <td'.($i % 2 == 0 ? ' bgcolor="#eeeeee"' : '').' valign="top" width="50%">';

			switch( $registry['keytype'] )
			{
				case 'boolean':
					print '
<input type="radio" name="Xconfig['.$registry['keyname'].']" value="1"' . ( $registry['keyvalue'] ? " checked" : "" ) . '>
Yes 
<input type="radio" name="Xconfig['.$registry['keyname'].']" value="0"' . ( !$registry['keyvalue'] ? " checked" : "" ) . '>
No';						
					break;
				
				case 'integer':
					print '<input class="tbinput" type="text" size="6" name="Xconfig['.$registry['keyname'].']" value="'.intval($registry['keyvalue']).'">';
					break;
					
				case 'array':
					print '<textarea class="tbinput" cols="60" rows="8" name="Xconfig['.$registry['keyname'].']">'.htmlspecialchars($registry['keyvalue']).'</textarea>';
					break;
					
					
				case 'string':
					print '<input type="text" class="tbinput" name="Xconfig['.$registry['keyname'].']" value="'.htmlspecialchars($registry['keyvalue']).'">';
					break;
			}
	  
			print '
      </td>
    </tr>';
			$i++;
		}			
	
	print '</table>
	<br>
	<center>
	<input type="hidden" name="session" value="'.$session.'">
	<input type="hidden" name="action" value="WriteSettings">
	<input type="submit" name="update_settings" value="Save settings">
	</center>
	</form>';
}



/*
 * ==========================================
 *              WriteSettings
 * ==========================================
 */
elseif( $action == "WriteSettings" )
{
	//var_dump($Xconfig);
	
	if ($Xconfig['pollmaxoptions'] < $Xconfig['pollminoptions'])
	{
		print '<b>Error</b><br>
		Your maximum poll options value is lower than your minimum poll options value ! <a href="javascript:history.back();">Back to settings</a>';
		break;
	}
	
	while( list($key, $value) = each($Xconfig) )
	{
		query("UPDATE $pref"."registry SET keyvalue='".addslashes($value)."' WHERE keyname='".$key."'");
	}
	print '<b>Settings saved</b><br>
	<br>
	Settings have been saved.';
}


tb_footer();
?>
