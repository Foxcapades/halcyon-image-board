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
 * Attempt to import the configuration file.
**/
if(file_exists('conf/index.php'))
{
	require_once 'conf/index.php';
}
else
{
	die('dude, wtf');
}

$threadID = $SQL->real_escape_string($_GET['thread_id']);

if(!is_numeric($threadID))
{
	ERROR::report($threadID.' given for thread ID by '.$_SERVER['REMOTE_ADDR']);
	index();
}

$r = $SQL->query('SELECT * FROM `pst_threads` INNER JOIN `ste_boards` USING (`board_id`) WHERE `thread_id` = \''.$threadID.'\' LIMIT 0,1');

$BINFO = $r->fetch_assoc();

$P->set('title', $VAR['site_title'].' / '.$BINFO['title']);
$P->set('h1', $BINFO['title']);
//$P->set('mes', '&gt;&gt;<a href="b.php?board='.$BINFO['dir'].'" title="Return to '.$BINFO['dir'].'">Return to '.$BINFO['dir'].'</a>');

$P->set('navbar', navbuild($SQL));



/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

* New Post Handler

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
if(count($_POST) && $_GET['post'] == 'new' && $USR['level'] >= $BINFO['reply_min_lvl'] && (time() - $_SESSION['postcooldown']) > 1) {

	$continue = TRUE;
	$reasons = array();

	// Make sure users are allowed to upload files at the size you set
	ini_set('upload_max_filesize', $VAR['maxFileSize']);
	$post	= &$_POST;
	$file	= &$_FILES;

	$imageTypes = array('image/gif', 'image/jpeg', 'image/png');

	$text = $FORM->scrub_text($post['text']);
	if(strlen($text) < 3) {
 $continue = FALSE; $reasons[] = "Posts must be at least 3 characters.";}


	/**
	 * 	This trys to figure out and report errors.  Important, errors that were not
	 * caused by normal user error are reported to the error log.
	 */

	// Check For File Upload Errors:
	$errmes='';
	$FORM->file_check($file['img1'], $imageTypes, $errmes);
	$reasons[] = $errmes;

	// Generate a 24 random digit file name
	$fname = ($file['img1']['error'] != UPLOAD_ERR_NO_FILE) ? rand_str().'.'.array_pop(explode('.', $file['img1']['name'])) : NULL;

	// Target upload directory + Generated file name
	$newfile = ($file['img1']['error'] != UPLOAD_ERR_NO_FILE) ? $VAR['updir'] .= $fname : NULL;

	// Stop if the file is too big
	if($file['img1']['size'] > $VAR['maxFileSize']) {

		$reasons[] = 'File too big, try resizing it or uploading something else.';
		$continue = FALSE;

	}

	// Check if we should continue or not
	if($continue && $file['img1']['error'] != UPLOAD_ERR_NO_FILE) {

		// Try and move the uploaded file
		if(!move_uploaded_file($file['img1']['tmp_name'], $newfile)) {
			$continue = FALSE;
			$reasons[] = 'Could not relocate uploaded file. Error reported, please try again later.';
			ERROR::report('Failed to move an uploaded file');

		} else {

			if(!makethumb($newfile, $VAR['thdir'].$fname)) {
				echo '<div class="error">Thumbnail image failed. Generic image used.</div>';
				ERROR::report('Failed to create thumb for image '.$newfile);
			}
		}
	}

	if($continue && $_GET['post'] == 'new') {

		if(!$SQL->query('INSERT INTO `pst_posts` (`thread_id`,`user_id`,`image`,`text`) VALUES (\''.$threadID.'\', \''.$USR['user_id'].'\', \''.$fname.'\', \''.$text.'\')')) {
			$continue = FALSE;
			$reasons[] = 'Database error, could not create post. Please try again later.';
			ERROR::report('Could not create post, attempting to remove traces. DB said: '.$SQL->error);

			if(!unlink($newfile)) {
				ERROR::report('Could not delete uploaded file after post failed. File: '.$newfile);
			}
		} else {
			if(!$SQL->query('UPDATE `pst_threads` SET `posted` = DEFAULT WHERE `thread_id` = \''.$BINFO['thread_id'].'\'')) {
				ERROR::report('Could not update thread posted time on '.$BINFO['thread_id']);
			}
			$refreshq = $SQL->query('SELECT `post_id` FROM `pst_posts` WHERE `user_id` = \''.$USR['user_id'].'\' AND `thread_id` = \''.$threadID.'\' ORDER BY `post_time` DESC LIMIT 0,1');
			$refresha = $refreshq->fetch_assoc();
			$_SESSION['postcooldown'] = time();
			$P->set('headstuff', '<meta http-equiv="refresh" content="0;url='.$VAR['base_url'].'/t.php?thread_id='.$threadID.'#i'.$refresha['post_id'].'" />');
		}
	}

	/**
	 * 	If they made it this far with no errors, I guess the post was made
	 * successfully :P
	 */

	// if it errored out, there are error messages to display
	if(!$continue) {
		$errorBoxHtml = '<div class="error"><ul>'."\n";
		foreach($reasons as $reason) {
			$errorBoxHtml .= '<li>'.$reason.'</li>'."\n";
		}
		$errorBoxHtml .= '<ul></div>'."\n";
	}

}
ini_restore('upload_max_filesize');

/**
 * If the thread ID given is not a number, then somethings up, so instead we'll
 * just show the index, and report the issue.
 */

$postQuery = $SQL->query('
SELECT *
FROM `pst_posts` as `p`
LEFT JOIN (
	`user_accounts` as `u`
	LEFT JOIN `user_online` AS `o`
	USING (`user_id`)
) USING (`user_id`)
WHERE `p`.`thread_id` = \''.$threadID.'\'
ORDER BY `post_id` ASC');

$strPageHTML = $errorBoxHtml;


$strPageHTML .= '<div id="thread">'."\n";
$now = 1;
$onlineUsers = array_flip($onlineUsers);
while($postParam = $postQuery->fetch_assoc())
{
	if($postParam['name'] == NULL || $postParam['name'] == '')
	{
		$postParam['user_id'] = '0000000001';
		$postParam['name'] = 'Anonymous';
		$postParam['level'] = 1;
	}
	$postParam['text'] = $BBC->parse($postParam['text']);
	if(in_array($postParam['user_id'], $onlineUsers))
	{
		 $online = TRUE;
	}
	else
	{
		 $online = FALSE;
	}
	$activePost = new POST(
		$postParam['user_id'],
		$postParam['name'],
		$postParam['avatar'],
		$postParam['level'],
		$postParam['last_ping'],
		$postParam['email'],
		$postParam['post_id'],
		$postParam['post_time'],
		$postParam['text'],
		$postParam['image'],
		$postParam['thread_id']
	);
	if($now === 1)
	{
		$strPageHTML .= $activePost->postbox('firstPost');
	}else
	{
		$strPageHTML .= $activePost->postbox();
	}
	if($USR['level'] >= $BINFO['reply_min_lvl']&& $now == 1)
	{
		$postForm = new newForm($_SERVER['REQUEST_URI'].'&amp;post=new', 'post', 'multipart/form-data');
		$postForm->fieldStart('Reply');
		$postForm->inputTextarea('text', 'Text', FALSE, 30, 3, FALSE, 'fullwidth');
		$postForm->inputHidden('MAX_FILE_SIZE', '2621440');
		$postForm->inputFile('img1', 'Image', FALSE, 'halfwidth');
		$postForm->inputSubmit('Post Reply', FALSE, FALSE, FALSE, 'halfwidth');
		$strPageHTML .= $postForm->formReturn();
		$now --;
	}
}

$strPageHTML .= '</div>'."\n";

$P->set('body', $strPageHTML);
$P->load('base.php');
$P->render();
?>