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
function newBoard(&$strPageHTML)
{
	extract($GLOBALS);

	if(!count($_POST)) {
		$form = new newForm('?mode=bn');
		$form->fieldStart('Create a Board');
		$form->inputText('bnm','Board Name','','','',10);
		$form->inputText('bttl','Full Board Title');
		$form->inputText('bms','Header Message');
		$form->inputSelect('blvl','Viewing Level');
		$form->addOption('Anonymous',1);
		$form->addOption('Registered',2);
		$form->addOption('Moderator',10);
		$form->addOption('Administrator',90);
		$form->addOption('Banned Users',0);
		$form->inputSelect('plvl','Posting Level');
		$form->addOption('Anonymous',1);
		$form->addOption('Registered',2);
		$form->addOption('Moderator',10);
		$form->addOption('Administrator',90);
		$form->addOption('Banned Users',0);
		$form->inputSelect('rlvl','Reply Level');
		$form->addOption('Anonymous',1);
		$form->addOption('Registered',2);
		$form->addOption('Moderator',10);
		$form->addOption('Administrator',90);
		$form->addOption('Banned Users',0);
		$form->inputCheckbox('lkd',1,'Lock the board?');
		$form->inputCheckbox('hdn',1,'Hide the board?');
		$form->inputSubmit('Create Board');
		$strPageHTML .= $form->formReturn();
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
		$ern += ($FORM->validate_text(htmlentities($_POST['bnm']),1,10)) ? 0 : 1;
		$ern += ($FORM->validate_text(htmlentities($_POST['bttl']),3,48)) ? 0 : 2;
		$ern += ($FORM->validate_text(htmlentities($_POST['bms']),3,128)) ? 0 : 4;
		$ern += (is_numeric($_POST['blvl']) && $_POST['blvl'] < 100  && $_POST['blvl'] >= 0) ? 0 : 8;
		$ern += (is_numeric($_POST['plvl']) && $_POST['plvl'] < 100  && $_POST['plvl'] >= 0) ? 0 : 16;
		$ern += (is_numeric($_POST['plvl']) && $_POST['rlvl'] < 100  && $_POST['plvl'] >= 0) ? 0 : 32;
		$_POST['lkd'] += ($_POST['lkd'] != 1) ? 0 : 1;
		$_POST['hdn'] += ($_POST['hdn'] != 1) ? 0 : 1;

		if($ern == 0) {

			$strPageHTML.= '<div>Attempting to create board...<br />';

			if($SQL->query('INSERT INTO `'.$databaseTables['boards'].'` VALUES (NULL,\''.$_POST['bnm'].'\',\''.$_POST['bttl'].'\',\''.$_POST['bms'].'\',\''.$_POST['hdn'].'\',\''.$_POST['lkd'].'\',\''.$_POST['plvl'].'\',\''.$_POST['blvl'].'\',\''.$_POST['rlvl'].'\')')) {

				if($posarr = $SQL->query('SELECT `position` FROM `ste_navbar` WHERE `position` < 250 ORDER BY `position` DESC LIMIT 0,1')) {

					if($chud = $SQL->query('SELECT `board_id` FROM `'.$databaseTables['boards'].'` WHERE `dir` = \''.$_POST['bnm'].'\' ORDER BY `board_id` DESC LIMIT 0,1')) {

						$nurm = $posarr->fetch_assoc();
						$hurp = $chud->fetch_assoc();
						$nurm['position'] += 1;

						// TODO: Allow setting the class and max user level.
						// TODO: find a way to do this with fewer queries
						// TODO: auto attempt cleanup on failure

						if($SQL->query('INSERT INTO `ste_navbar` VALUES (NULL,\''.$nurm['position'].'\',\'b.php?board='.$_POST['bnm'].'\',\''.$_POST['bttl'].'\',\''.$_POST['bnm'].'\',\'\',\''.$_POST['blvl'].'\',\'0\',\''.$hurp['board_id'].'\')')) {

							// TODO: verify this
							$strPageHTML .= '<span class="green">Board Created</span>';
						}else{
							$strPageHTML .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED3</span>';
						}
					}
				} else {
					$strPageHTML .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED2</span>';
				}
			} else {
				$strPageHTML .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED<br />Database Said:<br />'.$SQL->error.'</span>';
			}
		} else {echo $ern;}
	}
}
function deleteBoard(&$strPageHTML)
{
	extract($GLOBALS);
	// To delete the board we will need to remove all posts and files associated with the board, so it may be slow

	// If the form HAS NOT been submited, then show the form
	if(!count($_POST) && $_GET['yesdb'] != 'yes')
	{

		// Pull a list of all the boards in the database
		$sqlResult = $SQL->query('SELECT `board_id`,`dir` FROM `'.$databaseTables['boards'].'`');

		$delForm = new newForm('?mode=bd');

		$delForm->fieldStart('Delete Boards');
		$delForm->inputSelect('bdel[]','Select Boards to Delete',TRUE,8);

		// Insert the options into the <select> in the form
		while($fetch = $sqlResult->fetch_assoc())
		{
			$delForm->addOption($fetch['dir'],$fetch['board_id']);
		}

		$delForm->inputSubmit('Delete Selected Board(s)');

		return $delForm->formReturn();

		// If the form HAS been submited, then handle the input
	}
	// Double check to make sure this is what the user wants to do as
	//  there is no going back after this...
	if(count($_POST) && $_GET['yesdb'] != 'yes')
	{

		// Alert the user they cannot undo this
		$pageHTML = '<p>You are about to delete the following boards:<br /><br /><ul>';

		// Make sure all the given IDs are valid
		foreach($_POST['bdel'] as $bdel)
		{
			if(strlen($bdel) == 8 && $bdel != 0)
			{
				$boards[] = $bdel;
			}
		}

		// Sort out what the form returned and put it in a usable form
		$boards = (count($boards) > 1) ? implode('\',\'',$boards) : $boards[0];

		// Set this for later use
		$_SESSION['boards'] = $boards;

		// Get the names of the boards they want to delete
		$sqlResult = $SQL->query('SELECT `dir` FROM `'.$databaseTables['boards'].'` WHERE `board_id` IN (\''.$boards.'\')');
		while($fetch = $sqlResult->fetch_assoc())
		{
			$pageHTML .= '<li>'.$fetch['dir'].'</li>';
		}

		// Continue the alert
		$pageHTML .= '</ul><br />This action cannot be undone, are you sure you want to continue?</p><br />';
		$pageHTML .= '<a href="?mode=bd&yesdb=yes" title="Continue">Continue</a>';

		// Send the built HTML to the body
		return $pageHTML;

	}

	if(count($_POST) && $_GET['yesdb'] == 'yes')
	{
		//ok, were going to delete the board now, lets start by getting a list of whats contained on this board

		// This is going be dumped straight into the $strPageHTML var
		// Start the output
		$strPageHTML .= "<div id=\"innercontent\"><ul>\n";

		// Alert the user we are now pulling thread IDs
		$strPageHTML .= '	<li>Pulling thread IDs from database...';

		// Pull the threads
		if($threadQuery = $SQL->query('SELECT * FROM `'.$databaseTables['threads'].'` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
		{

			// If all went well alert the user and tell them whats next
			$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
			$strPageHTML .= '	<li>Sorting thread IDs...';

			// sort out the info we got from the database and store it for later
			while($threadResult = $threadQuery->fetch_assoc())
			{
				$thread[] = $threadResult;
			}

			// Initialise the variable for use later
			$thread_ids = '';

			// Build a list of ids for use in the next query and
			foreach($thread as $k => $v)
			{

				$thread_ids .= ($k > 0) ? '\',\'' : '';
				$thread_ids .= $v['thread_id'];

			}

			// Keep the user up to date
			$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
			$strPageHTML .= '	<li>Pulling posts from database...';

			// Next query, lets get all the posts contained in the affected threads
			if($postQuery = $SQL->query('SELECT * FROM `'.$databaseTables['posts'].'` WHERE `thread_id` IN (\''.$thread_ids.'\')'))
			{

				// More updates for the user
				$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
				$strPageHTML .= '	<li>Getting Image Names...';

				// Store the posts for later
				while($postResult = $postQuery->fetch_assoc())
				{
					$post[] = $postResult;
				}

				// get the image names from the affected posts
				foreach($post as $v) {

					if($v['image'] != '')
					{
						$pimg[] = $v['image'];
					}

				}

				// more updates
				$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
				$strPageHTML .= '	<li>Attempting to delete images...<ul>';

				// Lets begin shall we, starting from the lowest part of the chain and moving up...
				// Attempt to remove all the images that go with our posts
				foreach($pimg as $v) {

					// The original image
					$strPageHTML .= '		<li>'.$VAR['updir'].$v.'...';
					if(unlink($VAR['updir'].$v))
					{
						$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
					}
					else
					{
						ERROR::report('could not delete: '.$VAR['updir'].$v);
						$strPageHTML .= '<span class="error">FAILED</span></li>'."\n";
					}

					// The Thumbnail image
					$strPageHTML .= '		<li>'.$VAR['thdir'].$v.'...';
					if(unlink($VAR['thdir'].$v))
					{
						$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
					}
					else
					{
						ERROR::report('could not delete: '.$VAR['thdir'].$v);
						$strPageHTML .= '<span class="error">FAILED</span></li>'."\n";
					}
				}
				$strPageHTML .= '</ul></li>';

				$strPageHTML .= '	<li>Attempting to delete posts...';

				// Now lets hit the posts
				if($SQL->query('DELETE FROM `'.$databaseTables['posts'].'` WHERE `thread_id` IN (\''.$thread_ids.'\')'))
				{
					$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
					$strPageHTML .= '	<li>Attempting to delete threads...';

					// Followed by the threads
					if($SQL->query('DELETE FROM `'.$databaseTables['threads'].'` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
					{
						$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
						$strPageHTML .= '	<li>Attempting to delete board...';

						// And now the board itself
						if($SQL->query('DELETE FROM `'.$databaseTables['boards'].'` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
						{
							$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
							$strPageHTML .= '	<li>Deleting Link...';

							// And the last thing to remove is the link
							if($SQL->query('DELETE FROM `ste_navbar` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
							{
								$strPageHTML .= '<span class="green">OK.</span></li>'."\n";
								$strPageHTML .= '	<li>Verifying...';

								// Now verify all traces were removed...

							}
							else
							{
								$strPageHTML .= '<span class="error">FAILED</span></li>';
							}
						}
						else
						{
							$strPageHTML .= '<span class="error">FAILED</span></li>';
						}
					}
					else
					{
						$strPageHTML .= '<span class="error">FAILED</span></li>';
					}
				}
				else
				{
					$strPageHTML .= '<span class="error">FAILED</span></li>';
				}
			}
			else
			{
				$strPageHTML .= '<span class="error">FAILED</span></li>';
			}
		}
		else
		{
			$strPageHTML .= '<span class="error">FAILED</span></li>';
		}
		$strPageHTML .= '</ul></div>';
	}
}
function editBoard(){}
function clearBoard(){}
function banUser(){}
function editUser(){}
function promoteUser(){}

?>