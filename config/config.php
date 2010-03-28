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
error_reporting(E_ALL);
//REMOVE
if(file_exists('../../untitled.php'))
{
	require_once '../../untitled.php';
}
//END REMOVE

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
	die('MySQLi Connect Error ('.$SQL->connect_errno.') '.$SQL->connect_error);
}
/**
 * Required Files
 *
 * Here we will check the existence of and then import files that are needed for
 * the script to run.
 */
/**
 *
 * File includes
 *
 */

$REQUIRED_FILES = array(

//	'name' => 'path/to/file' (relative to the base directory for this script)
	'Page Builder' 		=> 'classes/pcc.php',
	'Error Handler'		=> 'classes/err.php',
	'Form Validator'	=> 'classes/frm.php',
	'Form Builder' 		=> 'classes/nfr.php',
	'Post Builder'		=> 'classes/pst.php',
	'BBCode Parser'		=> 'classes/bbc.php'

);

$thisPage = explode('/',$_SERVER['PHP_SELF']);
array_pop($thisPage);
unset($thisPage[0]);
$cd = '';
if(count($thisPage) > 0)
{
	foreach($thisPage as $v)
	{
		$cd .= '../';
	}
}

foreach($REQUIRED_FILES as $key => $value)
{
	// Check for and remove the leading forword slash for the path
	if(substr($value,0,1) == '/')
	{
		$value = substr($value,1);
	}
	$path = $cd.$value;
	if(file_exists($path))
	{
		require_once $path;
	}
	else
	{
		die('FATAL ERROR: Could not find the '.$key.' file.');
	}
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

class navBar
{
	private $array_Links = array();

	public function addLink($url, $title, $desc ='', $class ='', $id ='')
	{
		$array_LinkArray = array('type'=>'link', 'href'=> $url, 'text'=>$title);
		if($desc != '') $array_LinkArray['tip'] = $desc;
		if($class != '') $array_LinkArray['class'] = $class;
		if($id != '') $array_LinkArray['id'] = $id;
		$this->array_Links[] = $array_LinkArray;
	}
	public function addGroup($title, $class = '', $id = '')
	{
		$array_LinkArray = array('type'=>'group', 'text'=>$title);
		if($class != '') $array_LinkArray['class'] = $class;
		if($id != '') $array_LinkArray['id'] = $id;
		$this->array_Links[] = $array_LinkArray;
	}
	public function assemble($class='',$id='')
	{

		$string_HTML = '<ul'.(($class != '')?' class="'.$class.'"':'').
			(($id != '')?' id="'.$id.'"':'').'>';
		foreach($this->array_Links as $linkArray)
		{
			if($linkArray['type'] == 'group')
			{
				$string_HTML .= '<li class="group"><h3'.((isset(
					$linkArray['class']))?' class="'.$linkArray['class'].'"':'')
					.((isset($linkArray['id']))?' id="'.$linkArray['id'].'"':'')
					.'>'.$linkArray['text'].'</h3></li>';
			}
			elseif($linkArray['type'] == 'link')
			{
				$string_HTML .= '<li class="link"><a href="'.$linkArray['href'].
					'" title="'.((isset($linkArray['tip'])) ? $linkArray['tip']:
					$linkArray['title']).'" '.((isset($linkArray['id']))?' id="'
					.$linkArray['id'].'"' : '').((isset($linkArray['class'])) ?
					' class="'.$linkArray['class'].'"':'').'>'.
					$linkArray['text'].'</a></li>';
			}
		}
		$string_HTML .= '</ul>';
		return $string_HTML;
	}
}



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
if($userBox = $SQL->query('SELECT DISTINCT `o`.`user_id`,`a`.* FROM `user_online` `o` INNER JOIN `user_accounts` `a` ON `o`.`user_id` = `a`.`user_id` WHERE `o`.`last_ping` >= \''.($timeNow - 300).'\' ORDER BY `o`.`last_ping` DESC'))
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
		$tempList[$returnValue['rank']] = $returnValue['level'];
	}
}

$VAR['userLevelList'] = $tempList;

unset($userInfoResult,$cheese,$nim);
/**
 * Random string generator
 *
 * 	Used in generating keys for posts, names for files etc...
 *
 * @param int $length Length of the return string
 * @param string $chars Character pool to pull from
 * @return string
 */
function rand_str($length = 24, $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890_-')
{

	$poolcount = strlen($pool) - 1;

	$string = $pool{rand(0, $poolcount)};

	for ($i = 1; $i < $length; $i = strlen($string))
	{

		$r = $pool{rand(0, $poolcount)};
		if ($r != $string{$i - 1}) $string .=  $r;

	}

	return $string;

} // - END rand_str()
// FIXME unsecure mimetype discovery
function mime_type($filename)
{

	$mime_types = array(
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'png' => 'image/png',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff'
	);

	$ext = strtolower(array_pop(explode('.',$filename)));
	if (array_key_exists($ext, $mime_types))
	{
		return $mime_types[$ext];
	}
	else
	{
		return FALSE;
	}
}
/**
 * Image Thumbnail generator
 *
 * 	Takes the user uploaded image and attempts to create a thumbnail for it.
 *
 * TODO: Use a generic thumbnail on error
 * TODO: Simplify function, 'cause i know there are some extra steps
 *
 * @param string $image Source Image Path - 'i/up/images/name.file'
 * @param string $thumb Target Thumbnail Path - 'i/up/thumbs/name.file'
 * @return boolean
 */
function makethumb($image,$thumb)
{

	$memin = getimagesize($image);
	$size = (memory_get_usage()+ceil($memin[0]*$memin[1]*$memin['bits']))*1.5;

	ini_set('memory_limit', $size);

	$mType = mime_type($image);

	if($memin[0] != $memin[1])
	{
		if($memin[0] > $memin[1])
		{

			$newx = 150;
			$tarp = round(150/$memin[0],4);
			$newy = round($memin[1]*$tarp);

		}
		else
{
			$newy = 150;
			$tarp = round(150/$memin[1],4);
			$newx = round($memin[0]*$tarp);

		}

	}
	else
{
		$newx = 150;
		$newy = 150;

	}

	if($mType == 'image/jpeg')
	{
	$gump = imagecreatefromjpeg($image);}
	elseif ($mType == 'image/gif')
	{
	$gump = imagecreatefromgif($image);}
	elseif ($mType == 'image/png')
	{
	$gump = imagecreatefrompng($image);}
	else
	{
	return FALSE;}

	$gimp = imagecreatetruecolor($newx,$newy);
	imagecopyresampled($gimp,$gump,0,0,0,0,$newx,$newy,$memin[0],$memin[1]);

	imagedestroy($gump);

	if($mType == 'image/jpeg')
	{
	imagejpeg($gimp,$thumb);}
	elseif ($mType == 'image/gif')
	{
	imagegif($gimp,$thumb);}
	elseif ($mType == 'image/png')
	{
	imagepng($gimp,$thumb);}
	else
	{
	return FALSE;}

	imagedestroy($gimp);
	ini_restore('memory_limit');
	return TRUE;

} // - END makethumb()function index()
function index()
{
	global $P,$VAR;
	$P->set('title',$VAR['site_title']);
	$P->set('h1',$VAR['base_header']);
	$P->set('mes',$VAR['base_mes']);
	$strPageHTML = "<div class=\"boards\">\n";
	$strPageHTML .= navbuild($SQL);
	$strPageHTML .= "	\n</div>";
	$P->set('body',$strPageHTML);
	$P->load('base.php');
	$P->render();
	die();
}
function navbuild(&$sql)
{
	Global $SQL,$USR,$BINFO;
	$cheese = $SQL->query('SELECT * FROM `ste_navbar` ORDER BY `position` ASC');
	$out="<ul>\n";
	while($nim = $cheese->fetch_assoc())
	{
		$nim['usr_max'] = ($nim['usr_max'] == 0) ? 9001 : $nim['usr_max'];
		if($USR['level'] >= $nim['usr_thresh'] && $USR['level'] <= $nim['usr_max'])
		{

			$out .= '<li><a href="'.$nim['href'].'" title="'.$nim['title'].'"';

			$out .= ($nim['class']!='' && $nim['text'] == $BINFO['dir']) ? ' class="'.$nim['class'].' here"' : '';
			$out .= ($nim['class']!='' && $nim['text'] =! $BINFO['dir']) ? ' class="'.$nim['class'].'"' : '';
			$out .= ($nim['class']=='' && $nim['text'] == $BINFO['dir']) ? ' class="here"' : '';

			$out .= '>'.$nim['text'].'</a></li>';
		}
	}
	return $out."</ul>\n";
}
function pingUser($forced = FALSE)
{
	global $SQL,$USR;
	$timeNow = time();
	$time5Ago = $timeNow - 300;

	$SQL->query('DELETE FROM `user_online` WHERE `last_ping` <= \''.($time5Ago-300).'\'');

	$countVerify = $SQL->query('SELECT * FROM `user_online` WHERE `current_ip` = \''.$_SERVER['REMOTE_ADDR'].'\' AND `user_id` = \''.$_SESSION['user_id'].'\'');
	if($countVerify->num_rows == '0')
	{

		$SQL->query('INSERT INTO `user_online` VALUES (\''.$_SESSION['user_id'].'\',\''.$timeNow.'\',\''.$_SERVER['REMOTE_ADDR'].'\')');

	}
	else
	{

		$SQL->query('UPDATE `user_online` SET `last_ping` = \''.$timeNow.'\' WHERE `user_id` = \''.$_SESSION['user_id'].'\' AND `current_ip` = \''.$_SERVER['REMOTE_ADDR'].'\'');

	}

}
?>