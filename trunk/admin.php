<?php
/**
 *
 *	Taco Image Board
 *  Copyright (C) 2009-2010  Steven K. Utiger
 *
 *  This program is free software: you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License as published by the Free
 *  Software Foundation, either version 3 of the License, or any later version.
 *
 *  This program is distributed in the hope that it will be useful, but WITHOUT
 *  ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 *  FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 *  more details.
 *
 *  You should have received a copy of the GNU General Public License along with
 *  this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	This page is going to be a mess of nested switches.  When i finish 
 * everything, I will come back to sort this out...  Sorry.
 *
 */

session_start();

/**
 * Attempt to import the configuration file.
 */
if(file_exists('n/cnf.php')){require_once 'n/cnf.php';} else {die('dude, wtf');}

// TODO: set admin level threshold in database
if(!isset($_SESSION['uid']) || $USR['level'] < 70) {index();}


$P->set('title',$VAR['site_title']);
$P->set('h1',$VAR['base_header']);
$P->set('mes',$VAR['base_mes']);

$body = '<div class="body">'."\n".'<div class="conbox">'."\n";


/**
 *
 * Admin panel link list
 *
 */
//TODO: Find a more efficient way of handling these...

$menu = array(
	array('t','Site Control'),
	array('a','?mode=vars','Edit base site settings','Site Settings'),
	array('t','Board Control'),
	array('a','?mode=be','Edit Board Settings','Edit Board'),
	array('a','?mode=bn','Create A New Board','New Board'),
	array('a','?mode=bc','Remove all the threads from a board','Clear Board'),
	array('a','?mode=bd','Delete a board','Delete Board'),
	array('t','User Control'),
	array('a','?mode=um','Edit A User','Edit User'),
	array('a','?mode=ban','Ban a user, email or IP','Ban Hammer'),
	array('t','Staff Control'),
	array('a','?mode=sp','Promote or demote a staff member','Staff Levels')
);

$body .= '<div class="menu"><ul>'."\n";
foreach($menu as $v) {
	if($v[0]=='t'){$body.='<li class="bold">'.$v[1]."</li>\n";} elseif
	($v[0]=='a'){
		$body.='<li><a href="'.$v[1].'" title="'.$v[2].'">'.$v[3]."</a></li>\n";
	}
}
$body .= '</ul></div><div class="pageContent">'."\n";

switch($_GET['mode']) {


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Board Management

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 *
 * New Board
 *
 */
case 'bn':

if(!count($_POST)) {
	$P->formtovar('content','forms.php','newboard');
	$body .= $P->vars['content'];
} else {
	/**
	 * bnm = board name
	 * bttl = board title
	 * bms = board message
	 * blvl = visibility level
	 * plvl = level required to post
	 * lkd = locked
	 * hdn = hidden
	 */
	$ern = 0;
	$ern += ($FORM->validate_text($_POST['bnm'],1,10)) ? 0 : 1;
	$ern += ($FORM->validate_text($_POST['bttl'],3,48)) ? 0 : 2;
	$ern += ($FORM->validate_text($_POST['bms'],3,128)) ? 0 : 4;
	$ern += (is_numeric($_POST['blvl']) && $_POST['blvl'] < 100  && $_POST['blvl'] >= 0) ? 0 : 8;
	$ern += (is_numeric($_POST['plvl']) && $_POST['plvl'] < 100  && $_POST['plvl'] >= 0) ? 0 : 16;
	$_POST['lkd'] += ($_POST['lkd'] != 1) ? 0 : 1;
	$_POST['hdn'] += ($_POST['hdn'] != 1) ? 0 : 1;

	if($ern == 0) {

		$body.= '<div>Attempting to create board...<br />';

		if($SQL->query('INSERT INTO `ste_boards` VALUES (NULL,\''.$_POST['bnm'].'\',\''.$_POST['bttl'].'\',\''.$_POST['bms'].'\',\''.$_POST['hdn'].'\',\''.$_POST['lkd'].'\',\''.$_POST['plvl'].'\',\''.$_POST['blvl'].'\')')) {

			if($posarr = $SQL->query('SELECT `position` FROM `ste_navbar` WHERE `position` < 250 ORDER BY `position` DESC LIMIT 0,1')) {

				if($chud = $SQL->query('SELECT `id` FROM `ste_boards` WHERE `dir` = \''.$_POST['bnm'].'\' ORDER BY `id` DESC LIMIT 0,1')) {

					$nurm = $posarr->fetch_assoc();
					$hurp = $chud->fetch_assoc();
					$nurm['position'] += 1;

					// TODO: Allow setting the class and max user level.
					// TODO: find a way to do this with fewer queries
					// TODO: auto attempt cleanup on failure

					if($SQL->query('INSERT INTO `ste_navbar` VALUES (NULL,\''.$nurm['position'].'\',\'b.php?board='.$_POST['bnm'].'\',\''.$_POST['bttl'].'\',\''.$_POST['bnm'].'\',\'\',\''.$_POST['blvl'].'\',\'0\',\''.$hurp['id'].'\')')) {
						
						// TODO: verify this
						$body .= '<span class="green">Board Created</span>';
					}else{
						$body .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED3</span>';
					}
				}
			} else {
				$body .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED2</span>';
			}
		} else {
			$body .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED1</span>';
		}
	} else {echo $ern;}
}
break;

/**
 * 
 * Delete Board
 * 
**/
case 'bd':

// To delete the board we will need to remove all posts and files associated with the board, so it may be slow

// If the form HAS NOT been submited, then show the form
if(!count($_POST) && $_GET['yesdb'] != 'yes') {

	// Pull a list of all the boards in the database
	$sqlResult = $SQL->query('SELECT `id`,`dir` FROM `ste_boards`');

	// Form Variable array
	$formVars = array('ninjas' => '?mode=bd');

	// Inputs and parts for the form
	$formVars['contents'] = '<fieldset><legend>Delete Boards</legend><label for="bdel">Select Boards</label><select multiple="multiple" size="8" name="bdel[]" id="bdel">';

	// Insert the options into the <select> in the form
	while($fetch = $sqlResult->fetch_assoc()) {
		$formVars['contents'] .= '	<option value="'.$fetch['id'].'">'.$fetch['dir'].'</option>'."\n";
	}

	// Finish the form parts
	$formVars['contents'] .= '</select><input type="submit" value="Delete Board(s)" /></fieldset>';

	// Load the form from the template
	$P->formtovar('content','forms.php','',$formVars);

	// Add the loaded form to the body
	$body .= $P->vars['content'];

// If the form HAS been submited, then handle the input
} else {

	// Double check to make sure this is what the user wants to do as
	//  there is no going back after this...
	if($_GET['yesdb'] != 'yes') {

		// Alert the user they cannot undo this
		$pageHTML = '<p>You are about to delete the following boards:<br /><br /><ul>';

		// Make sure all the given IDs are valid
		foreach($_POST['bdel'] as $bdel) {
			if(strlen($bdel) == 8 && $bdel != 0) {
				$boards[] = $bdel;
			}
		}

		// Sort out what the form returned and put it in a usable form
		$boards = (count($boards) > 1) ? implode('\',\'',$boards) : $boards[0];

		// Set this for later use
		$_SESSION['boards'] = $boards;

		// Get the names of the boards they want to delete
		$sqlResult = $SQL->query('SELECT `dir` FROM `ste_boards` WHERE `id` IN (\''.$boards.'\')');
		while($fetch = $sqlResult->fetch_assoc()) {
			$pageHTML .= '<li>'.$fetch['dir'].'</li>';
		}

		// Continue the alert
		$pageHTML .= '</ul><br />This action cannot be undone, are you sure you want to continue?</p><br />';
		$pageHTML .= '<a href="?mode=bd&yesdb=yes" title="Continue">Continue</a>';

		// Send the built HTML to the body
		$body .= $pageHTML;

	} else {

		//ok, were going to delete the board now, lets start by getting a list of whats contained on this board

		// This is going be dumped straight into the $body var
		// Start the output
		$body .= "<div id=\"innercontent\"><ul>\n";

		// Alert the user we are now pulling thread IDs
		$body .= '	<li>Pulling thread IDs from database...';

		// Pull the threads
		if($threadQuery = $SQL->query('SELECT * FROM `pst_threads` WHERE `board` IN (\''.$_SESSION['boards'].'\')')) {

			// If all went well alert the user and tell them whats next
			$body .= '<span class="green">OK.</span></li>'."\n";
			$body .= '	<li>Sorting thread IDs...';

			// sort out the info we got from the database and store it for later
			while($threadResult = $threadQuery->fetch_assoc()) {
				$thread[] = $threadResult;
			}

			// Initialise the variable for use later
			$tids = '';

			// Build a list of ids for use in the next query and
			foreach($thread as $k => $v) {

				$tids .= ($k > 0) ? '\',\'' : '';
				$tids .= $v['tid'];

			}

			// Keep the user up to date
			$body .= '<span class="green">OK.</span></li>'."\n";
			$body .= '	<li>Pulling posts from database...';

			// Next query, lets get all the posts contained in the affected threads
			if($postQuery = $SQL->query('SELECT * FROM `pst_posts` WHERE `thread` IN (\''.$tids.'\')')) {

				// More updates for the user
				$body .= '<span class="green">OK.</span></li>'."\n";
				$body .= '	<li>Getting Image Names...';

				// Store the posts for later
				while($postResult = $postQuery->fetch_assoc()) {
					$post[] = $postResult;
				}

				// get the image names from the affected posts
				foreach($post as $v) {

					if($v['image'] != '') {$pimg[] = $v['image'];}

				}

				// more updates
				$body .= '<span class="green">OK.</span></li>'."\n";
				$body .= '	<li>Attempting to delete images...<ul>';

				// Lets begin shall we, starting from the lowest part of the chain and moving up...
				// Attempt to remove all the images that go with our posts
				foreach($pimg as $v) {

					// The original image
					$body .= '		<li>'.$VAR['updir'].$v.'...';
					if(unlink($VAR['updir'].$v)) {
						$body .= '<span class="green">OK.</span></li>'."\n";
					} else {
						ERROR::report('could not delete: '.$VAR['updir'].$v);
						$body .= '<span class="error">FAILED</span></li>'."\n";
					}

					// The Thumbnail image
					$body .= '		<li>'.$VAR['thdir'].$v.'...';
					if(unlink($VAR['thdir'].$v)) {
						$body .= '<span class="green">OK.</span></li>'."\n";
					} else {
						ERROR::report('could not delete: '.$VAR['thdir'].$v);
						$body .= '<span class="error">FAILED</span></li>'."\n";
					}
				}
				$body .= '</ul></li>';

				$body .= '	<li>Attempting to delete posts...';

				// Now lets hit the posts
				if($SQL->query('DELETE FROM `pst_posts` WHERE `thread` IN (\''.$tids.'\')')) {
					$body .= '<span class="green">OK.</span></li>'."\n";
					$body .= '	<li>Attempting to delete threads...';

					// Followed by the threads
					if($SQL->query('DELETE FROM `pst_threads` WHERE `board` IN (\''.$_SESSION['boards'].'\')')) {
						$body .= '<span class="green">OK.</span></li>'."\n";
						$body .= '	<li>Attempting to delete board...';

						// And now the board itself
						if($SQL->query('DELETE FROM `ste_boards` WHERE `id` IN (\''.$_SESSION['boards'].'\')')) {
							$body .= '<span class="green">OK.</span></li>'."\n";
							$body .= '	<li>Deleting Link...';

							// And the last thing to remove is the link
							if($SQL->query('DELETE FROM `ste_navbar` WHERE `delmo` IN (\''.$_SESSION['boards'].'\')')) {
								$body .= '<span class="green">OK.</span></li>'."\n";
								$body .= '	<li>Verifying...';

								// Now verify all traces were removed...

							} else {$body .= '<span class="error">FAILED</span></li>';}
						} else {$body .= '<span class="error">FAILED</span></li>';}
					} else {$body .= '<span class="error">FAILED</span></li>';}
				} else {$body .= '<span class="error">FAILED</span></li>';}
			} else {$body .= '<span class="error">FAILED</span></li>';}
		} else {$body .= '<span class="error">FAILED</span></li>';}
		$body .= '</ul></div>';
	}
}
break;
// Edit Board
case 'be':
$durm = $SQL->query('SELECT * FROM `ste_boards` ORDER BY `id` ASC');

break;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * User Management

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	case 'user':
		break;
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Staff Management

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	case 'staff':

		break;
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Site Management

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	case 'vars':
			$P->formtovar('content','forms.php','sitesettings');
			$body .= $P->vars['content'];

		break;
/* Someone shouldn't be here... */
	default:
		$body .= 'No Default Message.';
		break;
}
$body .= "\n".'</div></div></div>';
$P->set('body',$body);
$navbar = navbuild($SQL);
$P->set('navbar',$navbar);
$P->load('base.php');
$P->render();
?>