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
session_start();
/**
 * Attempt to import the configuration file.
 */
if(file_exists('config/config.php'))
{
	require_once 'config/config.php';
}
else
{
	die('dude, wtf');
}
/**
 *
 * Strip and verify URL given board ID
 *
 */
$directory = $SQL->real_escape_string($_GET['board']);

// If $directory is not a valid board name then show the index.
if(strlen($directory) > 10 || strlen($directory) == 0)
{
	index();
}


/**
 *
 * Attempt to pull the board information from the database.
 *
 */

$q = $SQL->query(

'SELECT *
FROM `'.DB_TABLE_BOARD_LIST.'`
WHERE `dir` = \''.$directory.'\''

);


// If the number of rows found less than 1 then show the index.
if($q->num_rows < 1)
{
	index();
}


// If the number of rows found was greater than 1, report the error and show the index.
elseif($q->num_rows > 1)
{
	ERROR::report('More than one board was found with the ID: '.$directory);
	index();
}


// Pass off the pulled info to an easier to manage array.
$BINFO = $q->fetch_assoc();

$q->close();

if($BINFO['view_min_lvl'] > $USR['level'])
{
	index();
}



// Page construction Variables
$P->set('title', $VAR['site_title'].' / '.$BINFO['dir']);
$P->set('h1', $BINFO['name']);
$P->set('mes', $BINFO['mes']);
$P->set('navbar', navbuild($SQL));


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

* New Thread Handler

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

if(
	count($_POST) &&
	$_GET['post'] == 'new' &&
	$USR['level'] >= $BINFO['post_min_lvl'] &&
	(time() - $_SESSION['postcooldown']) > 1
)
{

	// Simple error tracker to help shoot down the script when needed.
	$continue = TRUE;

	// An array of errors to show to the user to explain what happened.
	$reasons = array();

	// Set the php file size limit at the level you need (it will be restored)
	ini_set('upload_max_filesize', $VAR['maxFileSize']);

	// Allowed Image types
	$imageTypes = array(
	'image/gif',
	'image/jpeg',
	'image/png'
	);
	$file = &$_FILES;

	// Clean and check the length of the user given thread title
	$title = (strlen($FORM->scrub_text($_POST['ttle'])) < 1) ? 'Untitled Thread' : $FORM->scrub_text($_POST['ttle']);
	$title = (strlen($title) < 1) ? 'Untitled Thread' : $title;

	// Clean and check the length of the user given post text
	$text = $FORM->scrub_text($_POST['text']);
	if(strlen($text) < 3)
	{
		$continue = FALSE;
		$reasons[] = "Posts must be at least 3 characters.";
	}

	// Generate random 10 digit string for the thread key
	$threadkey = rand_str(10);

	$errmes='';
	$continue = ($FORM->file_check($file['img1'], $imageTypes, $errmes)) ? $continue : FALSE;
	$reasons[] = $errmes;

	// Generate a 24 random digit file name
	$fname = rand_str().'.'.array_pop(explode('.', $_FILES['img1']['name']));

	// Target upload directory + Generated file name
	$newfile = $VAR['updir'] .= $fname;

	// Stop if the file is too big
	if($_FILES['img1']['size'] > $VAR['maxFileSize'])
	{
		$reasons[] = 'File too big, try resizing it or uploading something else.';
		$continue = FALSE;
	}

	// Check if we should continue or not
	if($continue)
	{

		// Try and move the uploaded file
		if(!move_uploaded_file($_FILES['img1']['tmp_name'], $newfile))
		{
			$continue = FALSE;
			$reasons[] = 'Could not relocate uploaded file. Error reported, please try again later.';
			ERROR::report('Failed to move an uploaded file');
		}
		else
		{
			if(!makethumb($newfile, $VAR['thdir'].$fname))
			{
				echo '<div class="error">Thumbnail image failed. Generic image used.</div>';
				ERROR::report('Failed to create thumb for image '.$newfile);
			}
		}
	}

	if($continue)
	{

		// Try and create the thread
		if(!$SQL->query(

'INSERT INTO `'.DB_TABLE_THREAD_LIST.'` (
	`board_id`,
	`key`,
	`title`,
	`user`
) VALUES (
	\''.$BINFO['board_id'].'\',
	\''.$threadkey.'\',
	\''.$title.'\',
	\''.$USR['user_id'].'\'
)'

))
		{

			// If the thread could not be created, stop and delete the uploaded file
			$continue = FALSE;
			$reasons[] = 'Database error, Could not create thread. Please try again later.';
			ERROR::report('Could not create thread, database said: '.
				$SQL->error);

			if(!unlink($newfile))
			{
				ERROR::report('Could not delete uploaded file after thread insert failed. File: '.$newfile);
			}
		}
	}

	if($continue)
	{

		// Select newly created thread to verify it was actually posted and to obtain the DB set thread ID and DATE
		$objThreadVerify = $SQL->query(

'SELECT `thread_id`,`posted`
FROM `'.DB_TABLE_THREAD_LIST.'`
WHERE `key` = \''.$threadkey.'\'
AND `user` = \''.$USR['user_id'].'\'
ORDER BY `thread_id` DESC
LIMIT 0,1'

		);

		if(!is_object($objThreadVerify))
		{
			$continue = FALSE;
			$reasons[] = 'Database Error, please try again later.';
			ERROR::report('Error selecting created thread. Threadkey: \''.
				$threadkey.'\' DB said: '.$SQL->error);

			if(!unlink($newfile)) {
				ERROR::report('Could not delete uploaded file after threadselect failed. File: '.$newfile);
			}

		} else {

			// Get the pulled thread ID and DATE to attach to the post
			$nudez = $objThreadVerify->fetch_assoc();
			$objThreadVerify->close();

			// Insert the post into the database
			if(!$SQL->query(

'INSERT INTO `'.DB_TABLE_POST_LIST.'` (
	`thread_id`,
	`user_id`,
	`post_time`,
	`title`,
	`image`,
	`text`
) VALUES (
	\''.$nudez['thread_id'].'\',
	\''.$USR['user_id'].'\',
	\''.$nudez['posted'].'\',
	\''.$title.'\',
	\''.$fname.'\',
	\''.$text.'\'
)'

			))
			{

				$continue = FALSE;
				$reasons[] = 'Database error, could not create post. Please try again later.';
				ERROR::report('Could not create post, attempting to remove traces. DB said: '.$SQL->error);

				if(!$SQL->query(

'DELETE FROM `'.DB_TABLE_THREAD_LIST.'`
WHERE `key` = \''.$threadkey.'\'
AND `title` = \''.$title.'\''

				))
				{
					ERROR::report('could not remove thread after post failed. Threadkey: \''.$threadkey.'\' DB said: '.$SQL->error);
				}

				if(!unlink($newfile))
				{
					ERROR::report('Could not delete uploaded file after post failed. File: '.$newfile);
				}
			}
			else
			{
				$refreshq = $SQL->query(

'SELECT `post_id`
FROM `'.DB_TABLE_POST_LIST.'`
WHERE `user_id` = \''.$USR['user_id'].'\'
AND `thread_id` = \''.$nudez['thread_id'].'\'
ORDER BY `post_time` DESC
LIMIT 0,1'

				);
				$refresha = $refreshq->fetch_assoc();
				$refreshq->close();
				$_SESSION['postcooldown'] = time();
				$P->set('headstuff', '<meta http-equiv="refresh" content="0;url='
					.$VAR['base_url'].'/t.php?thread_id='.$nudez['thread_id'].
					'#i'.$refresha['post_id'].'" />');
			}
		}
	}
	/**
	 * 	If they made it this far with no errors, I guess the post was made
	 * successfully :P
	 */

	// if it errored out, there should be error messages to display
	if($continue == FALSE)
	{
		$errorBoxHtml = '<div class="error"><ul>'."\n";
		foreach($reasons as $reason)
		{
			$errorBoxHtml .= '<li>'.$reason.'</li>'."\n";
		}
		$errorBoxHtml .= '<ul></div>'."\n";
	}

}
ini_restore('upload_max_filesize');


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

* Page Body Construction

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// Start the $strPageHTML variable, if there was an error earlier in the script, this
//  will set the error box, otherwise the $strPageHTML variable is set to null.
$strPageHTML = (isset($errorBoxHtml)) ? $errorBoxHtml : '';

// Begin the thread list
$cherp = '<div id="thread">'."\n";

// Retrieve the thread list from the database
$dumo = $SQL->query('

SELECT `a`.*, COUNT(*) AS `count`,COUNT(DISTINCT `image`) AS `image_count`
FROM (

	SELECT
		`u`.`user_id`, `u`.`name`, `u`.`level`, `u`.`email`, `u`.`avatar`,
		`o`.`last_ping`,
		`p`.`post_id`, `p`.`title` AS `post_title`, `p`.`post_time`, `p`.`text`, `p`.`image`,
		`t`.`thread_id`, `t`.`posted`, `t`.`title`
	FROM `'.DB_TABLE_THREAD_LIST.'` AS `t`
	INNER JOIN (

		`'.DB_TABLE_POST_LIST.'` AS `p`
		LEFT JOIN (

			`'.DB_TABLE_USER_LIST.'` AS `u`
			LEFT JOIN `user_online` AS `o`
			USING (`user_id`)

		) USING (`user_id`)

	) USING (`thread_id`)
	WHERE `t`.`board_id` = \''.$BINFO['board_id'].'\'
	ORDER BY `p`.`post_id` ASC

) AS `a`
GROUP BY `a`.`thread_id`
ORDER BY `a`.`posted` DESC');
// Sift through the results and enter them into an array
$durr = array();

while($mrd = $dumo->fetch_assoc())
{
	if($mrd['name'] == NULL || $mrd['name'] == '')
	{
		$mrd['user_id'] = '0000000001';
		$mrd['name'] = 'Anonymous';
		$mrd['level'] = 1;
	}
	$durr[] = $mrd;
}

// Close the open $SQL connection
$dumo->close();


// Create a blank instance of POST for the following loop
$POST = new POST('', '', '', '', '', '', '', '', '', '', '');

if(count($durr))
{
	foreach($durr as $v)
	{

		$v['text'] = $BBC->parse($v['text']);
		$cherp .= $POST->threadview($v);

	}
}

// End the thread list
$cherp .= "</div>\n";

if($USR['level'] >= $BINFO['post_min_lvl'])
{
	$postForm = new newForm($_SERVER['REQUEST_URI'].'&amp;post=new', 'post',
		'multipart/form-data');
	$postForm->fieldStart('New Thread');
	$postForm->inputText('ttle', 'Title', '', '', 'fullwidth');
	$postForm->inputTextarea('text', 'Text', FALSE, 30, 4, FALSE, 'fullwidth');
	$postForm->inputHidden('MAX_FILE_SIZE', '2621440');
	$postForm->inputFile('img1', 'Image', FALSE, 'halfwidth');
	$postForm->inputSubmit('Create Thread', FALSE, FALSE, FALSE, 'halfwidth');
	$strPageHTML .= $postForm->formReturn();
}

// Add the thread list to the $strPageHTML var
$strPageHTML .= $cherp;

// Render the page
$userInfoArray = array(

'current_user_name' => $USR['name'],
'current_user_avatar' => $VAR['avdir'].$USR['avatar'],
'current_user_rank' => 0,
'current_user_unread_posts' => 0,
'current_user_unread_messages' => 0,
'current_user_total_messages' => 0

);
$side_nav = new navBar_mysqli('boards',$SQL,$USR['level'],TRUE);
$P->set('side_nav',$side_nav->assemble());
$P->set('thread_list', $strPageHTML);
$P->set($userInfoArray);
$P->loadtovar('body','themes/templates/'.$VAR['template_dir'].'thread_list.php');
$P->load('themes/templates/'.$VAR['template_dir'].'base.php');
$P->render();


?>