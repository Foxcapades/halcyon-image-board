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
$welcomepost = 'Welcome to [url=http://bbs.halcyonbbs.com]Halcyon Image Board[/url] Pre-Alpha 2.

Please remember this is not a finished image board, and [b]SHOULD NOT BE USED FOR ANYTHING BUT TESTING[/b].  Many functions, especially in the admin panel are not finished yet.

Also, this release will more than likely not be at all compatible with any later release. This is being released for the few people that are helping test and come up with ideas.

That being said, here&#039;s
[b]HOW TO EDIT THIS BOARD[/b]
If you are logged in to the administrator account, you should see a link on the right of the navbar that says "APanel".  Click that link and you will be brought to the administrative control panel.  Once you are there, click either "Edit Board" to edit and keep this board, or "Delete Board" to remove this board completely.
If you choose to keep this board, this post will remain, as there is no function to delete individual posts or threads yet.

Everything else in the admin panel should be fairly self explanatory.

Thank you for choosing Halcyon BBS';
if(!file_exists('classes/nfr.php'))
{
	die('<br><br><h1>Fatal Error</h1><p>Could Not find one of the required files, please re-upload all files.</p>');
}
else
{
	require_once 'classes/nfr.php';
}
if(!file_exists('classes/pcc.php'))
{
	die('<br><br><h1>Fatal Error</h1><p>Could not find one of the required files, please re-upload all files.</p>');
}
else
{
	require_once 'classes/pcc.php';
}
$fileList=array(
'classes/bbc.php',
'classes/bbsrc.js',
'classes/err.php',
'classes/frm.php',
'classes/index.html',
'classes/nfr.php',
'classes/pcc.php',
'classes/pst.php',
'i/av/index.html',
'i/av/anon.png',
'i/up/index.html',
'i/up/images/index.html',
'i/up/thumbs/index.html',
'i/index.html',
'i/001.png',
'i/002.png',
'i/crown-bronze.png',
'i/crown.png',
'i/crown-silver.png',
'config/config.php',
'config/index.php',
's/base.css',
's/html.css',
's/bbcd.css',
's/index.html',
't/base.php',
't/admin.php',
't/forms.php',
't/index.html',
'admin.php',
'b.php',
'faq.php',
'favicon.ico',
'index.php',
't.php',
'u.php',
'usr.php'
);
foreach($fileList as $v)
{
	if(!file_exists($v))
	{
		die('<br><br><h1>Fatal Error</h1><p>Could Not Find the required file: '.$v.', please re-upload this file.</p>');
	}
}
$formBase	= new newForm('#','post',FALSE,FALSE,FALSE,'admForm');
$page		= new templateForge();
$html		= '<div style="width:750px; margin:0px auto;">';
$vars		= array('title'=>'Halcyon Image Board Installation');
$ok			= TRUE;
function check_email_address($email)
{
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex)
	{
		$isValid = false;
	}
	else
	{
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64)
		{
			// local part length exceeded
			$isValid = false;
		}
		else if ($domainLen < 1 || $domainLen > 255)
		{
			// domain part length exceeded
			$isValid = false;
		}
		else if ($local[0] == '.' || $local[$localLen-1] == '.')
		{
			// local part starts or ends with '.'
			$isValid = false;
		}
		else if (preg_match('/\\.\\./', $local))
		{
			// local part has two consecutive dots
			$isValid = false;
		}
		else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		{
			// character not valid in domain part
			$isValid = false;
		}
		else if (preg_match('/\\.\\./', $domain))
		{
			// domain part has two consecutive dots
			$isValid = false;
		}
		else if
(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
					  str_replace("\\\\","",$local)))
		{
			// character not valid in local part unless
			// local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/',
				 str_replace("\\\\","",$local)))
			{
				$isValid = false;
			}
		}
		if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
		{
			// domain not found in DNS
			$isValid = false;
		}
	}
	return $isValid;
}

switch($_SESSION['steps'])
{
	case 'install':
		require_once 'config/config.php';
		$vars['h1'] = 'Installing Tables';
		$vars['mes']= 'Almost finished...';
		$html .= '<div class="box">Attempting to create tables...</div><br />';
		$html .= 'Creating Post table: ';
		if(!$SQL->query(
'CREATE TABLE `pst_posts` (
`post_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
`thread_id` int(10) unsigned zerofill NOT NULL,
`user_id` int(10) unsigned zerofill NOT NULL,
`post_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
`image` varchar(255) NOT NULL, `text` text NOT NULL,
PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Creating Thread table: ';
		if(!$SQL->query(
'CREATE TABLE `pst_threads` (
`thread_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
`board_id` mediumint(8) unsigned zerofill NOT NULL,
`key` varchar(10) DEFAULT NULL,`title` varchar(128) NOT NULL,
`user` int(10) unsigned zerofill NOT NULL,
`posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`thread_id`),
KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Creating Board table: ';
		if(!$SQL->query(
'CREATE TABLE `ste_boards` (
`board_id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
`dir` varchar(10) NOT NULL,
`name` varchar(32) NOT NULL,
`mes` varchar(128) NOT NULL,
`hidden` enum(\'0\',\'1\') NOT NULL DEFAULT \'0\',
`disabled` enum(\'0\',\'1\') NOT NULL DEFAULT \'0\',
`post_min_lvl` mediumint(2) NOT NULL,
`view_min_lvl` mediumint(2) NOT NULL,
`reply_min_lvl` mediumint(2) NOT NULL DEFAULT \'0\',
PRIMARY KEY (`board_id`),
UNIQUE KEY `dir` (`dir`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Creating NavBar table: ';
		if(!$SQL->query(
'CREATE TABLE `ste_navbar` (
`id` smallint(5) unsigned zerofill NOT NULL AUTO_INCREMENT,
`position` tinyint(3) unsigned NOT NULL,
`href` varchar(255) NOT NULL,
`title` varchar(255) NOT NULL,
`text` varchar(255) NOT NULL,
`class` varchar(48) NOT NULL,
`usr_thresh` tinyint(3) unsigned NOT NULL DEFAULT \'0\',
`usr_max` tinyint(4) NOT NULL DEFAULT \'0\',
`board_id` mediumint(8) unsigned zerofill NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Creating SiteVars table: ';
		if(!$SQL->query(
'CREATE TABLE `ste_vars` (
`key` varchar(64) NOT NULL,
`value` varchar(255) NOT NULL,
PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Creating User Accounts table: ';
		if(!$SQL->query(
'CREATE TABLE `user_accounts` (
`user_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
`name` varchar(32) NOT NULL,
`level` smallint(2) NOT NULL DEFAULT \'2\',
`email` varchar(48) NOT NULL,
`password` varchar(32) NOT NULL,
`posts` int(6) unsigned NOT NULL,
`avatar` varchar(128) NOT NULL DEFAULT \'anon.png\',
PRIMARY KEY (`user_id`),
UNIQUE KEY `name` (`name`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Creating User Level table: ';
		if(!$SQL->query(
'CREATE TABLE `user_levels` (
`level` smallint(6) NOT NULL,
`rank` varchar(64) NOT NULL,
PRIMARY KEY (`level`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Creating Online Users table: ';
		if(!$SQL->query(
'CREATE TABLE `user_online` (
`user_id` int(10) unsigned zerofill NOT NULL,
`last_ping` int(11) NOT NULL,
`current_ip` varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br /><br />';
		$html .= '<div class="box">Attempting to insert default Values into the database...</div>';
		$html .= '<br />';
		$html .= 'Adding NavBar Links: ';
		if(!$SQL->query(
'INSERT INTO `ste_navbar` (`id`, `position`, `href`, `title`, `text`, `class`, `usr_thresh`, `usr_max`, `board_id`) VALUES
(00001, 0, \'index.php\', \'Home Page\', \'Home\', \'\', 0, 0, 00000000),
(00006, 253, \'usr.php?mode=login\', \'Login\', \'Login\', \'\', 0, 1, 00000000),
(00005, 252, \'usr.php?mode=uac\', \'Account Control Panel\', \'Account\', \'\', 2, 0, 00000000),
(00007, 254, \'usr.php?mode=logout\', \'Logout\', \'Logout\', \'\', 2, 0, 00000000),
(00008, 251, \'faq.php\', \'Frequently Asked Questions\', \'FAQ\', \'\', 0, 0, 00000000),
(00009, 255, \'admin.php\', \'Administration Panel\', \'APanel\', \'\', 70, 0, 00000000),
(00010, 1, \'b.php?board=Welcome\', \'Sample Board\', \'Welcome\', \'\', 1, 0, 00000001)'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Adding Default Site Vars: ';
		if(!$SQL->query(
'INSERT INTO `ste_vars` (`key`, `value`) VALUES
(\'site_title\', \''.$_SESSION['array']['ttl'].'\'),
(\'base_header\', \''.$_SESSION['array']['shd'].'\'),
(\'base_mes\', \''.$_SESSION['array']['bms'].'\'),
(\'board_active\', \'1\'),
(\'base_url\', \''.$_SESSION['array']['url'].'\'),
(\'updir\', \'i/up/images/\'),
(\'avdir\', \'i/av/\'),
(\'thdir\', \'i/up/thumbs/\'),
(\'maxFileSize\', \'2621440\')'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Adding Users: ';
		if(!$SQL->query(
'INSERT INTO `user_accounts` (`user_id`, `name`, `level`, `email`, `password`, `posts`, `avatar`) VALUES
(0000000001, \'Anonymous\', 1, \'\', \'\', 0, \'anon.png\'),
(0000000002, \''.$_SESSION['array']['anm'].'\', \'99\', \''.$_SESSION['array']['ami'].'\',\''.$_SESSION['array']['aps'].'\',\'0\',\'anon.png\')'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Adding Example Board: ';
		if(!$SQL->query(
'INSERT INTO `ste_boards` (`board_id`, `dir`, `name`, `mes`, `hidden`, `disabled`, `post_min_lvl`, `view_min_lvl`, `reply_min_lvl`) VALUES
(00000001, \'Welcome\', \'Sample Board\', \'You can edit this board in the control panel\', \'0\', \'0\', \'2\', \'1\', \'2\')'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Adding Example Thread: ';
		if(!$SQL->query(
'INSERT INTO `pst_threads` (`thread_id`, `board_id`, `title`, `user`) VALUES
(0000000001, 00000001, \'Welcome to your new board!\', 0000000002)'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Adding Example Post: ';
		if(!$SQL->query(
'INSERT INTO `pst_posts` (`post_id`, `thread_id`, `user_id`, `image`, `text`) VALUES
(0000000001, 0000000001, 0000000002, \'sample.jpg\', \''.$welcomepost.'\')'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Adding User Rank List: ';
		if(!$SQL->query(
'INSERT INTO `user_levels` (`level`, `rank`) VALUES
(1, \'Anonymous\'),
(2, \'Registered\'),
(30, \'Moderator\'),
(50, \'Global Moderator\'),
(70, \'Administrator\'),
(90, \'Site Owner\'),
(0, \'Banned User\')'
		))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}

		$_SESSION['steps'] = 'admin';
		$html .= ($ok) ? '<div class="green">Tables Installed</div>' : '<div class="error">Something went wrong</div>';
		$html .= '<br />';
		$html .= '<a href="'.$_SESSION['array']['url'].'" title="continue">Continue</a>';
		break;

	case '1492':
		$username = '';
		$database = '';
		$admin_name = '';
		$admin_pass = '';
		$admin_veri = '';
		$admin_mail = '';
		$host	=  'localhost';
		$port	=  '3306';
		$site_title = 'Halcyon Image Board';
		$site_header = 'Halcyon Image Board';
		$base_mes = 'Here we go again';
		$base_url = 'http://www.example.com';
		if(count($_POST))
		{
			$vars['h1']	= 'MySQL Connection';
			$vars['mes']= 'Attempting connection...';
			$username	= htmlspecialchars($_POST['username']);
			$password	= htmlspecialchars($_POST['password']);
			$database	= htmlspecialchars($_POST['database']);
			$host		= htmlspecialchars($_POST['host']);
			$port		= htmlspecialchars($_POST['port']);
			$site_title	= htmlspecialchars($_POST['site_title']);
			$site_header= htmlspecialchars($_POST['site_header']);
			$base_mes	= htmlspecialchars($_POST['base_mes']);
			$base_url	= htmlspecialchars($_POST['base_url']);
			$admin_name	= $_POST['admin_name'];
			$admin_pass	= $_POST['admin_pass'];
			$admin_veri	= $_POST['admin_veri'];
			$admin_mail	= $_POST['admin_mail'];
			if(strlen($site_title) < 4)
			{
				$ok = FALSE;
				$html .= '<div class="error">The Site Title must be at least 4 characters.</div>';
			}
			if(strlen($site_header) < 4)
			{
				$ok = FALSE;
				$html .= '<div class="error">The Site header must be at least 4 characters.</div>';
			}
			if(!preg_match('#^http(?:s)?://[a-z0-9_-]+\.[a-z0-9-_]+(?:\.[a-z0-9-_]+)*(?:/[a-z0-9-_]+)*$#',$base_url))
			{
				$ok = FALSE;
				$html .= '<div class="error">Please enter a valid url.</div>';
			}
			if(strlen($admin_name) < 4)
			{
				$ok = FALSE;
				$html .= '<div class="error">Admin username must be at least 4 characters.</div>';
			}
			if(strlen($admin_pass) < 4)
			{
				$ok = FALSE;
				$html .= '<div class="error">Admin password must be at least 4 characters.</div>';
			}
			if(!check_email_address($admin_mail))
			{
				$ok = FALSE;
				$html .= '<div class="error">Please enter a valid email address.</div>';
			}
			if($admin_veri != $admin_pass)
			{
				$ok = FALSE;
				$html .= '<div class="error">Admin passwords did not match</div>';
			}
			if($ok)
			{
				@$sql = new mysqli($host,$username,$password,$database,$port);
				if($sql->connect_error)
				{
					$html .= '<div class="error">Connect Error ('.$sql->connect_errno.') '.$sql->connect_error.'</div>';
				}
				else
				{
					$html .= '<div class="green">DATABASE CONNECTION SUCCESSFUL</div>';
					$html .= '<p class="box">Attempting to write login info to the config file.</p>';
					$conf = file_get_contents('config/config.php');
					$with = array('\''.$host.'\'','\''.$username.'\'','\''.$password.'\'','\''.$database.'\'','\''.$port.'\'');
					$replace = array('REPLACE_HOST_NAME','REPLACE_USERNAME','REPLACE_PASSWORD','REPLACE_DATABASE_NAME','REPLACE_DATABASE_PORT');
					$newconf = str_replace($replace,$with,$conf);
					if(file_put_contents('config/config.php',$newconf))
					{
						$html .= '<div class="green">Configuration successfully altered.</div>';
						$html .= '<br />';
						$html .= '<a href="install.php" title="continue">Install Tables</a>';
						$_SESSION['steps'] = 'install';
						$_SESSION['array'] = array(
							'anm' => $admin_name,
							'aps' => md5($admin_pass),
							'ami' => $admin_mail,
							'url' => $base_url,
							'ttl' => $site_title,
							'shd' => $site_header,
							'bms' => $base_mes
						);
						break;
					}
					$html .= '<div ="error">Could not write to the config file, please check your server\'s permissions and try again.</div>';
				}
			}
		}
		$vars['h1'] = 'Site Setup';
		$vars['mes'] = 'Collecting information to setup the imageboard...';
		$formBase->fieldStart('Database Login Info');
		$formBase->inputHTML('<div>');
		$formBase->inputText('username','Database Username',$username);
		$formBase->inputHTML('<div class="admfexp">The username that the board will use to access the database.</div></div><div>');
		$formBase->inputPassword('password','Database Password');
		$formBase->inputHTML('<div class="admfexp">The password that the board will use to access the database.</div></div><div>');
		$formBase->inputText('database','Database Name',$database);
		$formBase->inputHTML('<div class="admfexp">Name of the database that will be used.  This database should be empty.</div></div><div>');
		$formBase->inputText('host','Database Address',$host);
		$formBase->inputHTML('<div class="admfexp">The address of the database, the most common is "localhost".</div></div><div>');
		$formBase->inputText('port','Database Port',$port);
		$formBase->inputHTML('<div class="admfexp">The port that will be used to connect to the database. Normally "3306"</div></div>');
		$formBase->fieldStart('Site Information');
		$formBase->inputHTML('<div>');
		$formBase->inputText('site_title','Site Title',$site_title);
		$formBase->inputHTML('<div class="admfexp">The Title of your image board (will show in the browser title).</div></div><div>');
		$formBase->inputText('site_header','Site Header',$site_header);
		$formBase->inputHTML('<div class="admfexp">The Header of your image board (will show on the home page and as the default header).</div></div><div>');
		$formBase->inputText('base_mes','Default Sub-header',$base_mes);
		$formBase->inputHTML('<div class="admfexp">The default message to show under the header (only shows with the default site header).</div></div><div>');
		$formBase->inputText('base_url','Base URL',$base_url);
		$formBase->inputHTML('<div class="admfexp">The base URL for your site DO NOT INCLUDE A TRAILING FORWARD SLASH (correct:"http://a.b.c" incorrect:"http://a.b.c/").</div></div>');
		$formBase->fieldStart('Create an Admin account');
		$formBase->inputHTML('<div>');
		$formBase->inputText('admin_name','Admin Username',$admin_name);
		$formBase->inputHTML('<div class="admfexp">The username of the admin account on the board.</div></div><div>');
		$formBase->inputPassword('admin_pass','Admin Password',$admin_pass);
		$formBase->inputHTML('<div class="admfexp">The password for the admin account.</div></div><div>');
		$formBase->inputPassword('admin_veri','Verify Password',$admin_veri);
		$formBase->inputHTML('<div class="admfexp">Verify the password.</div></div><div>');
		$formBase->inputText('admin_mail','Admin Email',$admin_mail);
		$formBase->inputHTML('<div class="admfexp">Email address for the admin account (nothing is sent here by default).</div></div>');
		$formBase->inputSubmit('Attempt Connection');
		$formBase->fieldEnd();
		$html .= $formBase->formReturn();
		break;

	case 'connect':
		break;
	default:
		$vars['h1'] = 'Verifying Permissions';
		$vars['mes'] = 'Checking to see if we can create and delete files on the server';
		$html .= 'Attempting to create test file...';
		if(!file_put_contents('potato.flarg','I AM FROM IRELAND'))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		$html .= 'Verifying test file...';
		if(!($contents = file_get_contents('potato.flarg')))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			if($contents != 'I AM FROM IRELAND')
			{
				$html .= '<span class="error">FAILED</span>';
				$ok = FALSE;
			}
			else
			{
				$html .= '<span class="green">OK</span>';
			}
		}
		$html .= '<br />';
		$html .= 'Attempting to delete test file...';
		if(!unlink('potato.flarg'))
		{
			$html .= '<span class="error">FAILED</span>';
			$ok = FALSE;
		}
		else
		{
			$html .= '<span class="green">OK</span>';
		}
		$html .= '<br />';
		if($ok)
		{
			$html .= '<p>It appears that everything checks out, click the link below to continue to the next step.</p><br />';
			$_SESSION['steps'] = '1492';
			$html .= '<a href="install.php" title="continue to next step">Continue to setup site</a>';
		}
		else
		{
			$html .= '<p>It seems there was an error in the filesystem verification, please check to make sure you have write permissions on your host server.';
		}
		break;
}
$page->set($vars);
$page->set('body',$html.'</div>');
$page->load('base.php');
$page->render();
?>