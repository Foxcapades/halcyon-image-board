<?php
/*
	Halcyon Image Board
	Copyright (C) 2010 Steven Utiger

  This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or any later version.

  This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

  You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.

*/
session_start();
if(file_exists('../config/config.php'))
{
	require_once '../config/config.php';
}
else
{
	die('I have no idea whats going on right now.');
}

$object_TopNav = new navBar();
$object_SubNav = new navBar();

$P->set('h1','Halcyon Admin Panel');

/**
 * Build the top navigation bar
 */

$array_QString = explode('&',$_SERVER['QUERY_STRING']);
$a=$b=$c=$d=$e=$f=FALSE;
switch($array_QString[0])
{
	case 'control=boards':
		$b = TRUE;
		$P->set('mes','Board Setup and options');
		break;
	case 'control=users':
		$c = TRUE;
		$P->set('mes','User Management');
		break;
	case 'control=modules':
		$d = TRUE;
		$P->set('mes','Plugin and Module management');
		break;
	case 'control=info':
		$e = TRUE;
		$P->set('mes','Stats &amp; Information');
		break;
	default:
		$a = TRUE;
		$P->set('mes','Site Control');
		break;
}

$object_TopNav->addLink($VAR['base_url'].'/admin/index.php','General','General Site Settings',(($a)?'here':''));
$object_TopNav->addLink($VAR['base_url'].'/admin/index.php?control=boards','Boards','Board Settings',(($b)?'here':''));
$object_TopNav->addLink($VAR['base_url'].'/admin/index.php?control=users','Users','User Management',(($c)?'here':''));
$object_TopNav->addLink($VAR['base_url'].'/admin/index.php?control=modules','Modules','Plugin and module management',(($d)?'here':''));
$object_TopNav->addLink($VAR['base_url'].'/admin/index.php?control=info','Info','Stats and information',(($e)?'here':''));

unset($a,$b,$c,$d,$e,$f);

$P->set('navbar',$object_TopNav->assemble());
$P->load('base.php');
$P->render();
?>