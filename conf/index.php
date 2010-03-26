<?php
/**
 *	Halcyon Image Board
 *  Copyright (C) 2010  Steven Utiger
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
 *
 * Configuration file
 *
 * To Do's:
 * TODO: User's Avatars will be named with the user's ID
 * TODO: Allow up to 4 uploads per post, as an option in the admin panel
 * TODO: User pages
 * TODO: Admin Panel
 * TODO: ADD Title box to all posts
 * TODO: Sort out stickies and announcements
 * TODO: control max image upload size from the admin panel
 * TODO: control the allowed upload mime types from the admin panel.
 * TODO: allow choosing nav bar position when creating a board
 *
 */
//REMOVE
if(file_exists('../../untitled.php'))
{
require_once '../../untitled.php';}
//END REMOVE
/**
 * Required Files
 *
 * Here we will check the existence of and then import files that are needed for
 * the script to run.
 */
//Error Handling Class
if(file_exists('c/err.php'))
{
	require_once 'c/err.php';
}
else
{
	die('lol wat');
}
//Page Building Class
if(file_exists('c/pcc.php'))
{
	require_once 'c/pcc.php';
}
else
{
	ERROR::dead('Could not find page building class.');
}
//BBCode Parsing Class
if(file_exists('c/bbc.php'))
{
	require_once 'c/bbc.php';
}
else
{
	ERROR::dead('Could not find bbCode class.');
}
//Form Validation Class
if(file_exists('c/frm.php'))
{
	require_once 'c/frm.php';
}
else
{
	ERROR::dead('Could not find form validating class.');
}
//Form Building Class
if(file_exists('c/nfr.php'))
{
	require_once 'c/nfr.php';
}
else
{
	ERROR::dead('Could not find form validating class.');
}
//Post Box Class
if(file_exists('c/pst.php'))
{
	require_once 'c/pst.php';
}
else
{
	ERROR::dead('Could not find post creation class.');
}
if(file_exists('conf/functions.php'))
{
	require_once 'conf/functions.php';
}
else
{
	ERROR::dead('Could not find required functions.');
}


/**
 * Basic Instances
 *
 * These are 'single instance' classes that will be used throughout the script.
 *
 */

//Open an instance of the Page Building Class
$P = new templateForge();


//Open an instance of the Form Validation Class
$FORM = new formValidate();


//Open an instance of the BBCode Parser
$BBC = new BBCode();


/**
 * Here we are starting our MySQLi instance... You will need to set your own
 * login details, or else you will see an angry error message when you try and
 * run the script.
 *
 * TODO:Move this somewhere easier to access for the users.
 *
 */
$SQL = new mysqli(

// Hostname or url of your database.
			REPLACE_HOST_NAME

// Login / username to access your database
,			REPLACE_USERNAME

// Password for login
,			REPLACE_PASSWORD

// Name of the DB we are connecting to.
,			REPLACE_DATABASE_NAME

// Database port (normally 3306)
,			REPLACE_DATABASE_PORT
);


// This will let you know if something went wrong...
if ($SQL->connect_error)
{
ERROR::dead('Connect Error ('.$SQL->connect_errno.') '.$SQL->connect_error);}

/**
 * User Sessions
 *
 * Here we see if you have a valid userID already in session, and if you don't
 * we assign you the userID for the Anonymous account.
 */
if(!isset($_SESSION['user_id']))
{
$_SESSION['user_id'] = 1;}
if(!$FORM->length($_SESSION['user_id'],1,10))
{
$_SESSION['user_id'] = 1;}
/**
 * Pull your info from the database
 * @var mixed
 */
$userInfoResult = $SQL->query('SELECT * FROM `user_accounts` WHERE `user_id`=\''.$_SESSION['user_id'].'\'');
/**
 * If there are multiple results for the same userID then there is a database
 * issue that needs to be sorted out.  Since we can't know for sure what result
 * you belong to, we will stop here and error out.
 */
if($userInfoResult->num_rows > 1)
{
	ERROR::dead('Duplicate entries found for UID:'.$_SESSION['user_id']);
}
/**
 * If that userID doesn't show up in the database at all then we will call you
 * anonymous and continue
 */
if ($userInfoResult->num_rows == 0)
{
	ERROR::report('Invalid UID:'.$_SESSION['user_id'].' for IP:'.$_SERVER['REMOTE_ADDR'].'. Changed to anon.');
	$_SESSION['user_id'] = 1;
	$userInfoResult = $SQL->query('SELECT * FROM `user_accounts` WHERE `user_id`=\'1\'');
}
if($userInfoResult->num_rows > 0)
{
/**
 * User Information
 *
 * An array that contains all the information contained in the database about
 * the current user.
 *
 * @var array
 */
	$USR = $userInfoResult->fetch_assoc();
}
/**
 * As you can probably guess by the name of this function, it builds the navbar.
 * In all reality it just assembles the links in a list format for later use.
 */
/**
 * Currently Online Table
 */
pingUser();
if($userBox = $SQL->query('SELECT DISTINCT `o`.`user_id`,`a`.* FROM `user_online` `o` INNER JOIN `user_accounts` `a` ON `o`.`user_id` = `a`.`user_id` WHERE `o`.`last_ping` >= \''.$time5Ago.'\' ORDER BY `o`.`last_ping` DESC'))
{
	$userbox = "<ul>\n";
	if($userBox->num_rows > 0)
	{
		while($infoLoop = $userBox->fetch_assoc())
		{
			$userbox .= '	<li class="ulv'.$infoLoop['level'].'">'.$infoLoop['name']."</li>\n";
			$onlineUsers[$infoLoop['user_id']] = $infoLoop['name'];
		}
	}
	else
{
		$userbox .= "	<li>No Online Users</li>\n";
	}
	$userbox .= '</ul>';
}

/**
 *
 * @param $sql
 * @return unknown_type
 */
/**
 * This randomly placed code assembles an array of site vars that are stored in
 * the database.
 */
$cheese = $SQL->query('SELECT * FROM `ste_vars`');
$VAR = array();
if($cheese->num_rows > 0)
{
	while($nim = $cheese->fetch_assoc())
	{
		$VAR[$nim['key']]=$nim['value'];
	}
}
//TODO: move this to a more fitting location:
$P->set('base_url',$VAR['base_url']);
$P->set('thumbdir',$VAR['thdir']);
$P->set('imagedir',$VAR['updir']);

$tempList = array();

$levelListResult = $SQL->query('SELECT * FROM `user_levels`');
if($levelListResult->num_rows > 0)
{
	while($returnValue=$levelListResult->fetch_assoc())
	{
		$tempList[$returnValue['level']] = $returnValue['rank'];
	}
}

$VAR['userLevelList'] = $tempList;

unset($userInfoResult,$cheese,$nim);
?>