<?php
/*
	Halcyon Image Board
	Copyright (C) 2010 Halcyon Bulletin Board Systems

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
/**
 * User "Profile" Page
 */

/**
 * Attempt to import the configuration file.
 */
if(file_exists('config/config.php')){require_once 'config/config.php';} else {die('dude, wtf');}

$cleanUserID = $FORM->scrub_text($_GET['view']);

if(isset($cleanUserID) && $cleanUserID != '' && $cleanUserID > 1 && strlen($cleanUserID) < 11 && is_numeric($cleanUserID)) {

	$currentUserQuery = $SQL->query(

'SELECT *
FROM `'.DB_TABLE_USER_LIST.'`
WHERE `user_id` = \''.$cleanUserID.'\'
LIMIT 0,1'

	);
	$currentUserInfo = $currentUserQuery->fetch_assoc();

	$P->set('title',$VAR['site_title'].' / '.$currentUserInfo['name']);
	$P->set('navbar',navbuild($SQL));
	$P->set('h1',$currentUserInfo['name']);
	$P->set('mes','Viewing User Page');

	$body = '<div class="body">'."\n";
	$body .= '<img src="'.$VAR['avdir'].$currentUserInfo['avatar'].'" alt="Avatar" />';
	$body .= '</div>'."\n";


	$P->set('body',$body);
	$P->load('themes/templates/'.$VAR['template_dir'].'base.php');
	$P->render();

} elseif($cleanUserID == 'mine'){index();} else {index();}
?>