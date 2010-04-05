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

/**
 * User Management
 *
 * 	This page handles most of the user handling functions...
 */

session_start();

/**
 * Attempt to import the configuration file.
 */
if(file_exists('config/config.php')){require_once 'config/config.php';} else {die('dude, wtf');}
// Set some base page variables
$P->set('title',$VAR['site_title'].' / User Management');
$P->set('h1','User Management');

// Start the body
$strPageHTML = '<div class="body">'."\n";

switch($_GET['mode'])
{


// TODO: PHP, USER: Fix login script error handler
// Login
case 'login':

	// Start the form variable array for the login form.

	$P->set('mes','User Login');

	// Check to see if were trying to log the user in or not...
	if(count($_POST)) {

		$P->set('mes','Attempting Login...');

		$e = 0;

		// Clean these to make sure no one does anything funny...
		$cleanUserName = $SQL->real_escape_string($_POST['unme']);
		$cleanPassword = md5($_POST['upss']);

		// More security stuff
		$e += (!$FORM->length($cleanUserName)) ? 1 : 0;

		if($e == 0) {

			// Try and pull the user info from the database
			$selUser = $SQL->query('SELECT * FROM `'.$databaseTables['user_list'].'` WHERE `name` = \''.$cleanUserName.'\' AND `password` = \''.$cleanPassword.'\'');

			// If the database returns a number other than 1 then something is wrong
			if($selUser->num_rows != 1)
			{

				// If the number is greater than 1, you have duplicate entries in your database.
				if($selUser->num_rows > 1)
				{
					ERROR::report('Duplicate entries found in database for User: '.$cleanUserName);
				}

				$e += 2;

			}
			else
			{

				$userRow = $selUser->fetch_row();
				$_SESSION['user_id'] = $userRow[0];
				$_SESSION['password'] = $userRow[3];
				$HERE = (strpos($_SESSION['return'],$VAR['base_url']) === FALSE) ? $VAR['base_url'] : $_SESSION['return'];
				$P->set('headstuff','<meta http-equiv="refresh" content="3;url='.$HERE.'" />');
				unset($_SESSION['return']);
				$strPageHTML .= '<p>Login successful.<br /><br />Redirecting to <a href="'.$HERE.'" class="cd">'.$HERE.'</a> after 3 seconds.</p>'."\n";
				pingUser(TRUE);
				break;

			}

		}

		$errors = array();
		$c = array(
		4 => 'SQL ERROR',
		2 => 'Username & Password do not match.',
		1 => 'Username Must be 4 - 32 characters.');

		$formVars['unme'] = $cleanUserName;

		krsort($c,SORT_NUMERIC);

		foreach($c as $k => $v) {
			if($e >= $k) {
				$e -= $k;
				$errors[] = $v;
			}
		}

		$strPageHTML .= '<p class="error">';
		$strPageHTML .= implode("<br />\n",$errors);
		$strPageHTML .= '</p>';

	} else {
		$_SESSION['return'] = $_SERVER['HTTP_REFERER'];
	}



	$form = new newForm('usr.php?mode=login','post',FALSE,FALSE,FALSE,'miniform');
	$form->fieldstart('Login');
	$form->inputText('unme','Username');
	$form->inputPassword('upss','Password');
	$form->inputSubmit('Login');
	$form->inputHTML('<a href="usr.php?mode=nac" title="Register a new account.">Register New Account</a>');
	$Lform = $form->formReturn();
	$P->set('page_content',$Lform);

	unset($cleanPassword,$cleanUserName,$selUser,$userRow,$e,$c,$errors,$formVars);

	$strPageHTML .= $P->vars['page_content'];
	break;

	// Logout
case 'logout':
	session_destroy();
	$HERE = (strpos($_SERVER['HTTP_REFERER'],$VAR['base_url']) === FALSE) ? $VAR['base_url'] : $_SERVER['HTTP_REFERER'];
	$P->set('headstuff','<meta http-equiv="refresh" content="3;url='.$HERE.'" />');
	$strPageHTML .= '<p>Session successfully terminated.<br /><br />Redirecting to <a href="'.$HERE.'" class="cd">'.$HERE.'</a> after 3 seconds.</p>';
	break;

	// New User Account
case 'nac':

	$e = 0;
	// Set some page variables
	$P->set('mes','Register New Account');
	$regForm = new newForm('usr.php?mode=nac');
	$regForm->fieldStart('New User Registration');
	$regForm->inputText('unme','Username');
	$regForm->inputText('mail','Email');
	$regForm->inputPassword('upss','Password');
	$regForm->inputPassword('vpss','Verify Password');
	$regForm->inputSubmit('Register');
		// Check to see if the user has submited the form
	if(count($_POST))
	{

		// Validate username
		$e += (!$FORM->validate_text($_POST['unme'])) ? 1 : 0;

		// Validate Email
		$e += (!$FORM->validate_email($_POST['mail'])) ? 2 : 0;

		// Verify passwords match
		$e += ($_POST['upss'] != $_POST['vpss']) ? 4 : 0;

		// If no errors have occured, then continue
		if($e === 0)
		{

			// TODO: HTML, FORMS: make a note for the user that mentions the passwords not being recoverable
			// TODO: PHP, USER: make a password reset page
			// TODO: PHP, USER: don't show the register page for logged in users to prevent problems
			// TODO: PHP, USER: create user page
			// TODO: PHP, ADMIN: set up email verification as an admin option
			// TODO: PHP, MISC: Auto login on registration

			$pass = md5($_POST['upss']);

			// Try and insert the new user
			if($SQL->query('INSERT INTO `'.$databaseTables['user_list'].'` VALUES (NULL,\''.$_POST['unme'].'\',2,\''.$_POST['mail'].'\',\''.$pass.'\',0,\'anon.png\')'))
			{

				// If it went without any problems, say so and end.
				$strPageHTML .= '<p class="green">Registration Complete, you may now log in.</p>';
				break;

			}
			// If we hit this, there was an sql problem
			ERROR::report('SQL Error on user registration, database said: '.$SQL->error);
			$e += 8;
		}
		$errors = array();

		// The error code array
		$ErrorCodes = array(
		1 => 'Invalid Username',
		2 => 'Invalid Email Address',
		4 => 'Passwords do not match',
		8 => 'SQL Error, reported to admins'
		);

		krsort($ErrorCodes,SORT_NUMERIC);

		foreach($ErrorCodes as $k => $v)
		{
			if($e >= $k)
			{
				$e -= $k;
				$errors[] = $v;
			}
		}

		// Let the user know what went wrong
		$strPageHTML .= '<p class="error">';
		$strPageHTML .= implode("<br />\n",$errors);
		$strPageHTML .= '</p>';


		// Set some vars before returning the form
		$formVars['unme'] = $FORM->scrub_text($_POST['unme']);
		$formVars['mail'] = $FORM->scrub_text($_POST['mail']);

	}

	// If we have reached this then just show the form
	$strPageHTML .= $regForm->formReturn();
	break;

case 'uac':
$P->set('h1','Account Management');
$P->set('mes','Set / Change your account options');
	$BINFO['dir'] = 'Account';
	$form = new newForm();
	$form->fieldStart('Avatar Settings');
	$form->inputCheckbox('grav',1,'Use Gravatar?',FALSE,FALSE,TRUE);
	$form->inputSubmit();
	$strPageHTML .= $form->formReturn();
	break;

default:
	$strPageHTML .= 'Why are you here again?';
	break;
}
$strPageHTML .= "\n".'</div>';
$navbar = navbuild($SQL);
$P->set('navbar',$navbar);
$P->set('body',$strPageHTML);
$P->load('themes/templates/'.$VAR['template_dir'].'base.php');
$P->render();
?>