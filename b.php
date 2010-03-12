<?php
/**
 *
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
 */

session_start();

/**
 *
 * Attempt to import the configuration file.
 *
 */

if(file_exists('n/cnf.php')){require_once 'n/cnf.php';} else {die('dude, wtf');}


/**
 *
 * Attempt to find the post class
 *
 */

if(file_exists('c/pst.php')){require_once 'c/pst.php';} else
{ERROR::dead('Could not find post creation class.');}


/**
 *
 * Strip and verify URL given board ID
 *
 */

$directory = $SQL->real_escape_string($_GET['board']);

// If $directory is not a valid board name then show the index.
if(strlen($directory) > 10 || strlen($directory) == 0){index();}


/**
 *
 * Attempt to pull the board information from the database.
 *
 */

$q = $SQL->query('SELECT * FROM `ste_boards` WHERE `dir` = \''.$directory.'\'');


// If the number of rows found less than 1 then show the index.
if($q->num_rows < 1){index();}


// If the number of rows found was greater than 1, report the error and show the index.
elseif($q->num_rows > 1){
	ERROR::report('More than one board was found with the ID: '.$directory);
	index();
}


// Pass off the pulled info to an easier to manage array.
$BINFO = $q->fetch_assoc();

if($BINFO['thresh'] > $USR['level']) {index();}



// Page construction Variables
$P->set('title',$VAR['site_title'].' / '.$BINFO['dir']);
$P->set('h1',$BINFO['name']);
$P->set('mes',$BINFO['mes']);
$P->set('navbar',navbuild($SQL));


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

* New Thread Handler

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

if(count($_POST) && $_GET['post'] == 'new' && $USR['level'] >= $BINFO['allowed'] && (time() - $_SESSION['postcooldown']) > 1) {

	// Simple error tracker to help shoot down the script when needed.
	$continue = TRUE;

	// An array of errors to show to the user to explain what happened.
	$reasons = array();

	// Set the php file size limit at the level you need (it will be restored)
	ini_set('upload_max_filesize',$VAR['maxFileSize']);

	// Allowed Image types
	$imageTypes = array(
	'image/gif',
	'image/jpeg',
	'image/png'
	);
	$file = &$_FILES;

	// Clean and check the length of the user given thread title
	$title = $FORM->scrub_text($_POST['ttle']);
	$title = (strlen($title) < 1) ? 'Untitled Thread' : $title;

	// Clean and check the length of the user given post text
	$text = $FORM->scrub_text($_POST['text']);
	if(strlen($text) < 3) {$continue = FALSE; $reasons[] = "Posts must be at least 3 characters.";}

	// Generate random 10 digit string for the thread key
	$threadkey = rand_str(10);

	$errmes='';
	$continue = ($FORM->file_check($file['img1'],$imageTypes,$errmes)) ? $continue : FALSE;
	$reasons[] = $errmes;

	// Generate a 24 random digit file name
	$fname = rand_str().'.'.array_pop(explode('.',$_FILES['img1']['name']));

	// Target upload directory + Generated file name
	$newfile = $VAR['updir'] .= $fname;

	// Stop if the file is too big
	if($_FILES['img1']['size'] > $VAR['maxFileSize']) {
		$reasons[] = 'File too big, try resizing it or uploading something else.';
		$continue = FALSE;
	}

	// Check if we should continue or not
	if($continue) {

		// Try and move the uploaded file
		if(!move_uploaded_file($_FILES['img1']['tmp_name'],$newfile)) {
			$continue = FALSE;
			$reasons[] = 'Could not relocate uploaded file. Error reported, please try again later.';
			ERROR::report('Failed to move an uploaded file');
		} else {
			if(!makethumb($newfile,$VAR['thdir'].$fname)) {
				echo '<div class="error">Thumbnail image failed. Generic image used.</div>';
				ERROR::report('Failed to create thumb for image '.$newfile);
			}
		}
	}

	if($continue) {

		// Try and create the thread
		if(!$SQL->query('INSERT INTO `pst_threads` (`board`,`key`,`title`,`user`) VALUES(\''.$BINFO['id'].'\',\''.$threadkey.'\',\''.$title.'\',\''.$USR['id'].'\')')) {

			// If the thread could not be created, stop and delete the uploaded file
			$continue = FALSE;
			$reasons[] = 'Database error, Could not create thread. Please try again later.';
			ERROR::report('Could not create thread, database said: '.$SQL->error);

			if(!unlink($newfile)) {
				ERROR::report('Could not delete uploaded file after thread insert failed. File: '.$newfile);
			}
		}
	}

	if($continue) {

		// Select newly created thread to verify it was actually posted and to obtain the DB set thread ID and DATE
		$nudes = $SQL->query('SELECT `tid`,`posted` FROM `pst_threads` WHERE `key` = \''.$threadkey.'\' AND `user` = \''.$USR['id'].'\' ORDER BY `tid` DESC LIMIT 0,1');

		if(!is_object($nudes)) {
			$continue = FALSE;
			$reasons[] = 'Database Error, please try again later.';
			ERROR::report('Error selecting created thread. Threadkey: \''.$threadkey.'\' DB said: '.$SQL->error);

			if(!unlink($newfile)) {
				ERROR::report('Could not delete uploaded file after threadselect failed. File: '.$newfile);
			}

		} else {

			// Get the pulled thread ID and DATE to attach to the post
			$nudez = $nudes->fetch_assoc();

			// Insert the post into the database
			if(!$SQL->query('INSERT INTO `pst_posts` (`thread`,`poster`,`post_time`,`image`,`text`) VALUES (\''.$nudez['tid'].'\',\''.$USR['id'].'\',\''.$nudez['posted'].'\',\''.$fname.'\',\''.$text.'\')')) {

				$continue = FALSE;
				$reasons[] = 'Database error, could not create post. Please try again later.';
				ERROR::report('Could not create post, attempting to remove traces. DB said: '.$SQL->error);

				if(!$SQL->query('DELETE FROM `pst_threads` WHERE `pst_threads`.`key` = \''.$threadkey.'\' AND `pst_threads`.`title` = \''.$title.'\' LIMIT 1')) {
					ERROR::report('could not remove thread after post failed. Threadkey: \''.$threadkey.'\' DB said: '.$SQL->error);
				}

				if(!unlink($newfile)) {
					ERROR::report('Could not delete uploaded file after post failed. File: '.$newfile);
				}
			} else {
				$refreshq = $SQL->query('SELECT `pid` FROM `pst_posts` WHERE `poster` = \''.$USR['id'].'\' AND `thread` = \''.$nudez['tid'].'\' ORDER BY `post_time` DESC LIMIT 0,1');
				$refresha = $refreshq->fetch_assoc();
				$_SESSION['postcooldown'] = time();
				$P->set('headstuff','<meta http-equiv="refresh" content="0;url='.$VAR['base_url'].'/t.php?tid='.$nudez['tid'].'#i'.$refresha['pid'].'" />');
			}
		}
	}
	/**
	 * 	If they made it this far with no errors, I guess the post was made
	 * successfully :P
	 */

	// if it errored out, there should be error messages to display
	if($continue == FALSE) {
		$errorBoxHtml = '<div class="error"><ul>'."\n";
		foreach($reasons as $reason) {
			$errorBoxHtml .= '<li>'.$reason.'</li>'."\n";
		}
		$errorBoxHtml .= '<ul></div>'."\n";
	}

}
ini_restore('upload_max_filesize');


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

* Page Body Construction

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// Start the $body variable, if there was an error earlier in the script, this
//  will set the error box, otherwise the $body variable is set to null.
$body = $errorBoxHtml;

// Begin the thread list
$cherp = '<div id="thread">'."\n";

// Retrieve the thread list from the database
$dumo = $SQL->query('

(SELECT `a`.*, COUNT(*) AS `count`,COUNT(DISTINCT `image`) AS `image_count` FROM (

SELECT * FROM `pst_threads` as `t`
INNER JOIN (

	`pst_posts` AS `p`
	INNER JOIN `usr_accounts` AS `u`
	ON `p`.`poster` = `u`.`id`

) ON `p`.`thread` = `t`.`tid`
WHERE `t`.`board` = \''.$BINFO['id'].'\'
ORDER BY `p`.`pid` ASC

) AS `a` GROUP BY `a`.`tid`)
UNION
(SELECT `b`.*, COUNT(*) AS `count`,COUNT(DISTINCT `image`) AS `image_count` FROM (

SELECT * FROM `pst_threads` as `t`
INNER JOIN (

	`pst_posts` AS `p`
	INNER JOIN `usr_accounts` AS `u`
	ON `p`.`poster` = `u`.`id`

) ON `p`.`thread` = `t`.`tid`
WHERE `t`.`board` = \''.$BINFO['id'].'\'
ORDER BY `p`.`pid` DESC

) AS `b` GROUP BY `b`.`tid`)
ORDER BY `posted` DESC, `pid` ASC');

// Sift through the results and enter them into an array
while($mrd = $dumo->fetch_assoc()) {
	$durr[$mrd['tid']][] = $mrd;
}

// Close the open $SQL connection
$SQL->close();


// Create a blank instance of POST for the following loop
$POST = new POST('','','','','','','','','','');

if(is_array($durr)) {
	foreach($durr as $v) {

		$v[0]['text'] = $BBC->parse($v[0]['text']);
		$v[1]['text'] = $BBC->parse($v[1]['text']);
		$cherp .= $POST->threadview($v[0],$v[1]);

	}
}

// End the thread list
$cherp .= "</div>\n";

if($USR['level'] >= $BINFO['allowed']) {
	$postForm = new newForm($_SERVER['REQUEST_URI'].'&amp;post=new','post','multipart/form-data');
	$postForm->fieldStart('New Thread');
	$postForm->inputText('ttle','Title',FALSE,FALSE,'halfwidth');
	$postForm->inputText('unme','Username',$USR['name'],FALSE,'halfwidth');
	$postForm->inputTextarea('text','Text',FALSE,30,4,FALSE,'fullwidth');
	$postForm->inputHidden('MAX_FILE_SIZE','2621440');
	$postForm->inputFile('img1','Image',FALSE,'halfwidth');
	$postForm->inputSubmit('Create Thread',FALSE,FALSE,FALSE,'halfwidth');
	$body .= $postForm->formReturn();
}

// Add the thread list to the $body var
$body .= $cherp;

// Render the page
$P->set('body',$body);
$P->load('base.php');
$P->render();


?>