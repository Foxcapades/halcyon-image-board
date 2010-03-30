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

// To delete the board we will need to remove all posts and files associated with the board, so it may be slow

/**
 * TODO: Break apart the delete parts into individual functions (delete_post, delete_thread, delete_board)
 */

// Double check to make sure this is what the user wants to do as
//  there is no going back after this...
if($_GET['yesdb'] != 'yes')
{

	// Alert the user they cannot undo this
	$pageHTML = '<p>You are about to delete the following board:<br /><br /><ul>';

	// Make sure all the given IDs are valid
//	foreach($_POST['bdel'] as $bdel)
//	{
		if(strlen($_GET['board']) == 8 && $_GET['board'] != 0)
		{
			$boards[] = $_GET['board'];
		}
//	}

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
	$pageHTML .= '<a href="?section=boards&mode=delBoard&yesdb=yes" title="Continue">Continue</a>';

	// Send the built HTML to the body
	$string_HTML_Return .= $pageHTML;

}
else
{

	//ok, were going to delete the board now, lets start by getting a list of whats contained on this board

	// This is going be dumped straight into the $string_HTML_Return var
	// Start the output
	$string_HTML_Return .= "<div id=\"innercontent\"><ul>\n";

	// Alert the user we are now pulling thread IDs
	$string_HTML_Return .= '	<li>Pulling thread IDs from database...';

	// Pull the threads
	if($threadQuery = $SQL->query('SELECT * FROM `'.$databaseTables['threads'].'` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
	{

		// If all went well alert the user and tell them whats next
		$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
		$string_HTML_Return .= '	<li>Sorting thread IDs...';

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
		$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
		$string_HTML_Return .= '	<li>Pulling posts from database...';

		// Next query, lets get all the posts contained in the affected threads
		if($postQuery = $SQL->query('SELECT * FROM `'.$databaseTables['posts'].'` WHERE `thread_id` IN (\''.$thread_ids.'\')'))
		{

			// More updates for the user
			$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
			$string_HTML_Return .= '	<li>Getting Image Names...';

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
			$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
			$string_HTML_Return .= '	<li>Attempting to delete images...<ul>';

			// Lets begin shall we, starting from the lowest part of the chain and moving up...
			// Attempt to remove all the images that go with our posts
			foreach($pimg as $v) {

				// The original image
				$string_HTML_Return .= '		<li>'.$VAR['updir'].$v.'...';
				if(unlink($VAR['updir'].$v))
				{
					$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
				}
				else
				{
					ERROR::report('could not delete: '.$VAR['updir'].$v);
					$string_HTML_Return .= '<span class="error">FAILED</span></li>'."\n";
				}

				// The Thumbnail image
				$string_HTML_Return .= '		<li>'.$VAR['thdir'].$v.'...';
				if(unlink($VAR['thdir'].$v))
				{
					$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
				}
				else
				{
					ERROR::report('could not delete: '.$VAR['thdir'].$v);
					$string_HTML_Return .= '<span class="error">FAILED</span></li>'."\n";
				}
			}
			$string_HTML_Return .= '</ul></li>';

			$string_HTML_Return .= '	<li>Attempting to delete posts...';

			// Now lets hit the posts
			if($SQL->query('DELETE FROM `'.$databaseTables['posts'].'` WHERE `thread_id` IN (\''.$thread_ids.'\')'))
			{
				$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
				$string_HTML_Return .= '	<li>Attempting to delete threads...';

				// Followed by the threads
				if($SQL->query('DELETE FROM `'.$databaseTables['threads'].'` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
				{
					$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
					$string_HTML_Return .= '	<li>Attempting to delete board...';

					// And now the board itself
					if($SQL->query('DELETE FROM `'.$databaseTables['boards'].'` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
					{
						$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
						$string_HTML_Return .= '	<li>Deleting Link...';

						// And the last thing to remove is the link
						if($SQL->query('DELETE FROM `ste_navbar` WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'))
						{
							$string_HTML_Return .= '<span class="green">OK.</span></li>'."\n";
							$string_HTML_Return .= '	<li>Verifying...';

							// Now verify all traces were removed...

						}
						else
						{
							$string_HTML_Return .= '<span class="error">FAILED</span></li>';
						}
					}
					else
					{
						$string_HTML_Return .= '<span class="error">FAILED</span></li>';
					}
				}
				else
				{
					$string_HTML_Return .= '<span class="error">FAILED</span></li>';
				}
			}
			else
			{
				$string_HTML_Return .= '<span class="error">FAILED</span></li>';
			}
		}
		else
		{
			$string_HTML_Return .= '<span class="error">FAILED</span></li>';
		}
	}
	else
	{
		$string_HTML_Return .= '<span class="error">FAILED</span></li>';
	}
	$string_HTML_Return .= '</ul></div>';
}
?>