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

/**
 * Try and include the config file
 */
if(file_exists('../config/config.php'))
{
	require_once '../config/config.php';
}
else
{
	die('Could not include config.php.');
}

/**
 * Try and include the functions file
 */
if(file_exists('admin_functions.php'))
{
	require_once 'admin_functions.php';
}
else
{
	die('Could not include admin_functions.php.');
}

/**
 * If the user is not an admin, show the index
 */
if($USR['level'] < $VAR['userLevelList']['Administrator'])
{
	index();
}


/**
 * The full url to the current file
 *
 * @var string
 */
$string_MyURL = $VAR['base_url'].$_SERVER['PHP_SELF'];

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
$string_HTML_Body = '<div id="admin_body">'."\n";

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
	/**
	 * Board setup and management
	 */
	case 'boards':

		/**
		 * Set the sub-header
		 */
		$P->set('mes','Board Settings and Options');

		$object_SubNav->addGroup('Board Management');
		$object_SubNav->addLink($string_MyURL.'?section=boards', 'View Boards', 'View and edit the boards', (($string_Mode == '' || $string_Mode == NULL) ? 'here' : ''));
		$object_SubNav->addLink($string_MyURL.'?section=boards&mode=newBoard', 'Create a Board', 'Create a new board', (($string_Mode == 'newBoard') ? 'here' : ''));
		$string_HTML_Body .= '<div id="admin_nav_menu"><h2>Navigation</h2>'.$object_SubNav->assemble().'</div>';

		switch($string_Mode) {
			case 'newBoard':
				if(file_exists('board_create.php'))
				{
					require_once 'board_create.php';
				}
				else
				{
					die('A required admin function is missing.');
				}
				break;
			case 'delBoard':
				if(file_exists('board_delete.php'))
				{
					require_once 'board_delete.php';
				}
				else
				{
					die('A required admin function is missing.');
				}
				break;
			default:
				if(file_exists('board_view.php'))
				{
					require_once 'board_view.php';
				}
				else
				{
					die('A required admin function is missing.');
				}
				break;
		}
		$string_HTML_Body .= '<div id="admin_body_content"><h2>Active Boards</h2>'.$string_HTML_Return.'</div>';
		break;

	/**
	 * User control and manegment menu
	 */
	case 'users':
		$P->set('mes','User Management');
		$object_SubNav->addGroup('User Control');
		$object_SubNav->addLink($string_MyURL.'?section=users','User Stats','View User Stats',(($string_Mode == '' || $string_Mode == NULL) ? 'here' : ''));
		$object_SubNav->addLink($string_MyURL.'?section=users&mode=selEdit','Edit User','Select and Edit a user',(($string_Mode == 'selEdit' || $string_Mode == 'edit') ? 'here' : ''));
		$string_HTML_Body .= '<div id="admin_nav_menu"><h2>Navigation</h2>'.$object_SubNav->assemble().'</div>';
		break;
	case 'modules':
	case 'info':

	/**
	 * This will show the admin panel home page when the user hits the home page
	 * as well as when the script doesn't recognize the section the user tried.
	 */
	default:

		/**
		 * Set the sub-header
		 */
		$P->set('mes','General Site Settings');
		$object_SubNav->addGroup('Site Control');
		$object_SubNav->addLink($string_MyURL,'View Status','View the sites current status',(($string_Mode == '' || $string_Mode == NULL) ? 'here' : ''));
		$object_SubNav->addLink($string_MyURL.'?mode=base','Edit Settings','Edit the base settings for the site',(($string_Mode == 'base' || $string_Mode == 'baseEdit') ? 'here' : ''));
		$object_SubNav->addLink($string_MyURL.'?mode=adv','Advanced Settings','Edit the advanced options for the site',(($string_Mode == 'adv') ? 'here' : ''));
		$string_HTML_Body .= '<div id="admin_nav_menu"><h2>Navigation</h2>'.$object_SubNav->assemble().'</div>';

		/**
		 * Copy the site vars so we can alter them
		 */
		$array_VarsCopy = $VAR;
		switch($string_Mode)
		{
			case 'baseEdit':
				if(file_exists('general_settings.php'))
				{
					require_once 'general_settings.php';
				}
				else
				{
					die('A required admin function is missing.');
				}
				$string_HTML_Body .= '<div id="admin_body_content"><h2>Settings Update</h2>'.$string_HTML_Return.'</div>';
				if($continue) {break;}
			case 'base':
				$object_Form_Base = new newForm('?mode=baseEdit');
				$object_Form_Base->inputHTML($ERRORMES);
				$object_Form_Base->inputHTML('<div class="long_form">');
				$object_Form_Base->inputText('site_title','Site Title',$array_VarsCopy['site_title']);
				$object_Form_Base->inputHTML('<div class="form_explain">The title for the site. (used with the HTML &lt;title&gt; tag)</div></div><div class="long_form">');
				$object_Form_Base->inputText('base_header','Site Header',$array_VarsCopy['base_header']);
				$object_Form_Base->inputHTML('<div class="form_explain">The default header for the site. (shows on the home page)</div></div><div class="long_form">');
				$object_Form_Base->inputText('base_mes','Site Header Message',$array_VarsCopy['base_mes']);
				$object_Form_Base->inputHTML('<div class="form_explain">The header message for the site. (shows on the home page)</div></div><div class="long_form">');
				$object_Form_Base->inputText('base_url','Base URL',$array_VarsCopy['base_url']);
				$object_Form_Base->inputHTML('<div class="form_explain">The base url for the site, CANNOT CONTAIN A TRAILING SLASH (good:http://a.b.c/path bad:http://a.b.c/path/).</div></div>');
				$object_Form_Base->inputSubmit();
				$string_HTML_Body .= '<div id="admin_body_content"><h2>Basic Site Settings</h2>'.$object_Form_Base->formReturn().'</div>';
				break;
			default:
				if(file_exists('general_view.php'))
				{
					require_once 'general_view.php';
				}
				else
				{
					die('A required admin function is missing.');
				}
				break;
		}
		break;
}
$string_HTML_Body .= '</div>';
$P->set('body',$string_HTML_Body);
$P->set('navbar',$object_TopNav->assemble());
$P->load('base.php');
$P->render();
?>