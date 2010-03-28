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
if(file_exists('admin_functions.php'))
{
	require_once 'admin_functions.php';
}
else
{
	die('I have no idea whats going on right now.');
}

// If the user is not an admin, show the index
if($USR['level'] < $VAR['userLevelList']['Administrator'])
{
	index();
}

$string_MyURL = $VAR['base_url'].'/admin/index.php';
/**
 * Instance of the navBar class for the top nav bar
 *
 * @var object
 */
$object_TopNav = new navBar();
/**
 * Instance of the navBar class for the side nav menu
 *
 * @var object
 */
$object_SubNav = new navBar();
/**
 * String containing the HTML to fill the body variable in the template
 *
 * @var string
 */
$string_BodyHTML = '<div id="admin_body">'."\n";
/**
 * String containing the current section of the admin panel the user is in,
 * if this value is blank or null the script will default to the 'General
 * Settings' section.
 *
 * @var string
 */
$string_Section	= $_GET['section'];
/**
 * String containing the current action or function the user is viewing /
 * attempting in the admin panel section they are in.  If this value is blank or
 * null the script will default to the home page for the current section.
 *
 * @var string
 */
$string_Mode	= $_GET['mode'];

/**
 * Set the Header for the page
 */
$P->set('h1','Halcyon Admin Panel');

/**
 * Build the top navigation bar
 */
$object_TopNav->addLink($string_MyURL, 'General', 'General Site Settings', (($string_Section == '' || $string_Section == NULL) ? 'here' : ''));
$object_TopNav->addLink($string_MyURL.'?section=boards', 'Boards', 'Board Settings', (($string_Section == 'boards') ? 'here' : ''));
$object_TopNav->addLink($string_MyURL.'?section=users', 'Users', 'User Management', (($string_Section == 'users') ? 'here':''));
$object_TopNav->addLink($string_MyURL.'?section=modules', 'Modules', 'Plugin and module management', (($string_Section == 'modules') ? 'here' : ''));
$object_TopNav->addLink($string_MyURL.'?section=info', 'Info', 'Stats and information', (($string_Section == 'info') ? 'here':''));
$object_TopNav->addLink($VAR['base_url'], 'Home', 'Go back to the boards');

/**
 * Functions for the boards section.
 */
switch($string_Section)
{
	case 'boards':

		/**
		 * Set the sub-header
		 */
		$P->set('mes','Board Settings and Options');

		/**
		 * Result class for the query that should pull all info for all the boards
		 * in the database.
		 *
		 * @var object
		 */
		$object_boardResult = $SQL->query(
'SELECT *
FROM `ste_boards`
ORDER BY `board_id` ASC'
		);

		/**
		 * Array that will contain a set of sub arrays that represent the boards on
		 * the site.
		 *
		 * @var array
		 */
		$array_boardList = array();

		/**
		 * A string that will hold the HTML for the list of boards on the home page
		 * for the boards section.
		 *
		 * @var string
		 */
		$string_BoardListHTML  = '<table id="admin_board_list">'."\n";
		$string_BoardListHTML .= '	<col class="bdLstCol1" />'."\n";
		$string_BoardListHTML .= '	<col class="bdLstCol2" />'."\n";
		$string_BoardListHTML .= "	<tbody>\n";

		/**
		 * Put together a table of the boards with links to various options to
		 * manipulate them
		 */
		while($temp = $object_boardResult->fetch_assoc())
		{
			$array_boardList[] = $temp;
			$string_BoardListHTML .= '		<tr id="btd_'.$temp['board_id'].'">'."\n";
			$string_BoardListHTML .= '			<td class="bdLstTitle">'.$temp['name']."</td>\n";
			$string_BoardListHTML .= "			<td class=\"bdLstOps\">\n";
			$string_BoardListHTML .= "				<ul>\n";
			$string_BoardListHTML .= '					<li><a href="'.$VAR['base_url'].'/admin/index.php?section=boards&mode=delBoard&board='.$temp['board_id'].'" title="Delete this board?"><img src="../i/cross.png" alt="Delete" /></a></li>'."\n";
			$string_BoardListHTML .= '					<li><a href="'.$VAR['base_url'].'/admin/index.php?section=boards&mode=editBoard&board='.$temp['board_id'].'" title="Edit this board?"><img src="../i/gear.png" alt="Edit" /></a></li>'."\n";
			$string_BoardListHTML .= "				</ul>\n";
			$string_BoardListHTML .= "			</td>\n";
			$string_BoardListHTML .= "		</tr>\n";
		}
		$string_BoardListHTML .= "	</tbody>\n";
		$string_BoardListHTML .= "</table>\n";

		$object_SubNav->addGroup('Board Management');
		$object_SubNav->addLink($string_MyURL.'?section=boards', 'View Boards', 'View and edit the boards', (($string_Mode == '' || $string_Mode == NULL) ? 'here' : ''));
		$object_SubNav->addLink($string_MyURL.'?section=boards&mode=newBoard', 'Create a Board', 'Create a new board', (($string_Mode == 'newBoard') ? 'here' : ''));
		$string_BodyHTML .= '<div id="admin_nav_menu">'.$object_SubNav->assemble().'</div>';
		$string_BodyHTML .= '<div id="admin_body_content">'.$string_BoardListHTML.'</div>';
		break;
	case 'users':
		$P->set('mes','User Management');
		$object_SubNav->addGroup('User Control');
		$object_SubNav->addLink($string_MyURL.'?section=users','User Stats','View User Stats',(($string_Mode == '' || $string_Mode == NULL) ? 'here' : ''));
		$object_SubNav->addLink($string_MyURL.'?section=users&mode=selEdit','Edit User','Select and Edit a user',(($string_Mode == 'selEdit' || $string_Mode == 'edit') ? 'here' : ''));
		$string_BodyHTML .= '<div id="admin_nav_menu">'.$object_SubNav->assemble().'</div>';
		break;
	case 'modules':
	case 'info':
	case NULL:
	case '':
	default:
		/**
		 * Set the sub-header
		 */
		$P->set('mes','General Site Settings');
		$object_SubNav->addGroup('Site Control');
		$object_SubNav->addLink($string_MyURL,'View Status','View the sites current status',(($string_Mode == '' || $string_Mode == NULL) ? 'here' : ''));
		$object_SubNav->addLink($string_MyURL.'?mode=base','Edit Settings','Edit the base settings for the site',(($string_Mode == 'base') ? 'here' : ''));
		$string_BodyHTML .= '<div id="admin_nav_menu">'.$object_SubNav->assemble().'</div>';
		$array_SiteStats = array();
		$object_StatsResult = $SQL->query('SELECT * FROM `site_stats`');
		while($temp = $object_StatsResult->fetch_assoc())
		{
			$array_SiteStats[$temp['stat']] = $temp['value'];
		}
		$string_TableHTML  = '
	<table id="admin_stat_table">
		<thead>
			<tr>
				<th>Description</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Installed</td>
				<td>'.date('l F d, Y',$array_SiteStats['installed']).'</td>
			</tr>
			<tr>
				<td>Board Version</td>
				<td>'.$VAR['version'].'</td>
			</tr>
			<tr>
				<td>Posts</td>
				<td>'.$array_SiteStats['posts'].'</td>
			<tr>
			<tr>
				<td>Images</td>
				<td>'.$array_SiteStats['image_posts'].'</td>
			<tr>
						<tr>
				<td>Threads</td>
				<td>'.$array_SiteStats['threads'].'</td>
			<tr>
			<tr>
				<td>Registered Users</td>
				<td>'.$array_SiteStats['reg_users'].'</td>
			<tr>
		</tbody>
	</table>
';
		$string_BodyHTML .= '<div id="admin_body_content">'.$string_TableHTML.'</div>';
		break;
}
$string_BodyHTML .= '</div>';
$P->set('navbar',$object_TopNav->assemble());
$P->set('body',$string_BodyHTML);
$P->load('base.php');
$P->render();
?>