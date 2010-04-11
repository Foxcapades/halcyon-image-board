<?php
/**
 *
 *	Halcyon Image Board
	Copyright (C) 2010 Halcyon Bulletin Board Systems
 *
 *    This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or any later version.
 *
 *    This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 *    You should have received a copy of the GNU General Public License along
 * with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//header('Location: ./install.php'); exit;
session_start();


if(!file_exists('config/config.php'))
{
	die('<br><br><h1>Fatal Error</h1><p>Could Not find one or more of the required files.</p>');
}
else
{
	require_once 'config/config.php';
}

// Start the $strPageHTML variable, if there was an error earlier in the script, this
//  will set the error box, otherwise the $strPageHTML variable is set to null.
$strPageHTML = (isset($errorBoxHtml)) ? $errorBoxHtml : '';

// Begin the thread list
$cherp = '<div id="thread">'."\n";
$cherp .= '<h2 class="block">Recently Active Threads</h2>';

// Retrieve the thread list from the database
$dumo = $SQL->query('

SELECT `a`.*, COUNT(*) AS `count`,COUNT(DISTINCT `image`) AS `image_count`
FROM (

	SELECT
		`u`.`user_id`, `u`.`name`, `u`.`level`, `u`.`email`, `u`.`avatar`,
		`o`.`last_ping`,
		`p`.`post_id`, `p`.`title` AS `post_title`, `p`.`post_time`, `p`.`text`, `p`.`image`,
		`t`.`thread_id`, `t`.`posted`, `t`.`title`
	FROM `'.DB_TABLE_THREAD_LIST.'` AS `t`
	INNER JOIN (

		`'.DB_TABLE_POST_LIST.'` AS `p`
		LEFT JOIN (

			`'.DB_TABLE_USER_LIST.'` AS `u`
			LEFT JOIN `user_online` AS `o`
			USING (`user_id`)

		) USING (`user_id`)

	) USING (`thread_id`)
	ORDER BY `p`.`post_id` ASC
	LIMIT 0,10

) AS `a`
GROUP BY `a`.`thread_id`
ORDER BY `a`.`posted` DESC');
// Sift through the results and enter them into an array
$durr = array();

while($mrd = $dumo->fetch_assoc())
{
	if($mrd['name'] == NULL || $mrd['name'] == '')
	{
		$mrd['user_id'] = '0000000001';
		$mrd['name'] = 'Anonymous';
		$mrd['level'] = 1;
	}
	$durr[] = $mrd;
}

// Close the open $SQL connection
$dumo->close();


// Create a blank instance of POST for the following loop
$POST = new POST('', '', '', '', '', '', '', '', '', '', '');

if(count($durr))
{
	foreach($durr as $v)
	{

		$v['text'] = $BBC->parse($v['text']);
		$cherp .= $POST->threadview($v);

	}
}

// End the thread list
$cherp .= "</div>\n";


// Add the thread list to the $strPageHTML var
$strPageHTML .= $cherp;

// Render the page
$userInfoArray = array(

'current_user_name' => $USR['name'],
'current_user_avatar' => $VAR['avdir'].$USR['avatar'],
'current_user_rank' => 0,
'current_user_unread_posts' => 0,
'current_user_unread_messages' => 0,
'current_user_total_messages' => 0

);
$side_nav = new navBar_mysqli('boards',$SQL,$USR['level'],TRUE);
$P->set('title',$VAR['site_title']);
$P->set('h1',$VAR['base_header']);
$P->set('mes',$VAR['base_mes']);
$navbar = new navBar_mysqli('main',$SQL,$USR['level']);
$navbar->pre_load();
$P->set('navbar',$navbar->assemble());
$P->set('side_nav',$side_nav->assemble());
$P->set('thread_list', $strPageHTML);
$P->set($userInfoArray);
$P->loadtovar('body','themes/templates/'.$VAR['template_dir'].'thread_list.php');
$P->load('themes/templates/'.$VAR['template_dir'].'base.php');
$P->render();

?>