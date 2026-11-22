<?php
/* $Id: versioninfo.php,v 1.4 2004/01/04 02:03:41 thed_o_n Exp $ */
/*
          ThWboard - PHP/MySQL Bulletin Board System
        ==============================================
            (c) 2000, 2001 by
               Paul Baecher         <paul@thewall.de>
               Felix Gonschorek   <funner@thewall.de>

          download the latest version:
            http://www.thwboard.de

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================

*/
include "common.inc.php";

tb_header();

print '<b>Generic information</b><br>';

print '
ThWboard-version: <font color="darkblue">'.$config['version'].'</font><br>
PHP-version: <font color="darkblue">'.phpversion().'</font><br>
MySQL-version: <font color="darkblue">'.mysql_get_server_info().'</font><br>';

$ver = @file('/proc/version');
if( $ver )
{
	print 'OS-version: <font color="darkblue">'.$ver[0].'</font><br>';
}

print '<br>';

$a_dir = array('../', '../inc/', './');
$a_dev = array(
	'dkreuer' => 'Daniel Kreuer',
	'deandy' => 'Andy Karpow',
	'slier' => 'Sascha Liehr',
	'superhausi' => 'Stephan Hauser',
	'pbaecher' => 'Paul Baecher',
	'thetinysteini' => 'Sebastian Steinlechner',
	'thed_o_n' => 'Maximilian Marx',
	'mr_nase' => 'Dominik Hahn'
);

print '<b>ThWboard file versions</b><br>';
print '<table width="100%" border="0" cellspacing="0" cellpadding="3">';
print '  <tr>
	    <td><i>Filename</i></td>
	    <td><i>Version</i></td>
	    <td><i>Modified</i></td>
	    <td><i>Last Author</i></td>
	  </tr>
	';

while( list(, $dir) = each($a_dir) )
{
	$dp = opendir($dir);
	$a_file = array();
	while( $file = readdir($dp) )
	{
		if( substr($file, -4) == '.php' )
			$a_file[] = $file;
	}
	closedir($dp);

	sort($a_file);

	if(!strcmp('./', $dir))
	  {
	    $printdir = './admin/';
	  }
	else
	  {
	    $printdir = substr($dir, 1);
	  }
	

	print '  <tr>
	    <td colspan="4"><br><font color="darkblue">Directory '.$printdir.'</font></td>
	  </tr>
	';
	
	$i = 0;
	while( list(, $file) = each($a_file) )
	{
		$fp = fopen($dir.$file, 'r');
		$data = fread($fp, 128);
		fclose($fp);

		//^.[$()|*+?{\
		if( ereg('/\* \$'.'Id'.': ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) ([^[:space:]]*) \$ \*/', $data, $regs) )
		{
			print '  <tr bgcolor="'.( $i % 2 == 0 ? '#E5E5E5' : '#F2F2F2').'">
    <td><font size="1">'.$file.'</font></td>
    <td><font size="1">'.$regs[2].'</font></td>
    <td><font size="1">'.$regs[3].' '.$regs[4].'</font></td>
    <td><font size="1">'.$a_dev[$regs[5]].'</font></td>
  </tr>';
  			$i++;
		}
	}
}
print '</table>';

tb_footer();