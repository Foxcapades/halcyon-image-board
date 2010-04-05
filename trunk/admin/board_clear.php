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

$string_H2 = 'Board Clear Verification';
$strBoardID = (isset($_GET['board']) && is_numeric($_GET['board'])) ? $_GET['board'] : '0';
$strBoardID = $SQL->real_escape_string($_GET['board']);
$objBoardInfoResult = $SQL->query(

'SELECT *
FROM `'.$databaseTables['boards'].'`
WHERE `board_id` = \''.$strBoardID.'\'
LIMIT 0,1'

);

if($objBoardInfoResult->num_rows == 0) {
	$string_HTML_Return = '<p>Could not find a board with the ID specified.';
	return;
}
else
{
	$arrBoardInfo = $objBoardInfoResult->fetch_assoc();
	if(!isset($_GET['continue'])) {

		$string_HTML_Return = '<div><span class="error">WARNING!</span><br />This action will remove all the threads, posts, and images currently attached to the "'.$arrBoardInfo['name'].'" board.  Any data removed will not be recoverable.  Are you sure you want to continue?<br /><br /><a href="?'.$_SERVER['QUERY_STRING'].'&continue" title="Continue to clear boards...">Continue</a></div>';
	}
	else
	{
		$string_H2 = 'Clearing Board';
		// This is going be dumped straight into the $string_HTML_Return var
		// Start the output
		$string_HTML_Return = "<div id=\"innercontent\"><ul>\n";

		// Alert the user we are now pulling thread IDs
		$string_HTML_Return .= '	<li>Pulling thread IDs from database...';

		// Pull the threads
		if($threadQuery = $SQL->query(

'SELECT *
FROM `'.$databaseTables['threads'].'`
WHERE `board_id` = \''.$strBoardID.'\''

		))
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
			if($postQuery = $SQL->query(

'SELECT *
FROM `'.$databaseTables['posts'].'`
WHERE `thread_id` IN (\''.$thread_ids.'\')'

			))
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
}
?>