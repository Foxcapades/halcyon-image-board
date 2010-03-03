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
 */
if(file_exists('n/cnf.php')){require_once 'n/cnf.php';} else {die('dude, wtf');}

/**
 * Attempt to find the post class
 */
if(file_exists('c/pst.php')){require_once 'c/pst.php';} else {
	ERROR::dead('Could not find post creation class.');}

	$tid = $SQL->real_escape_string($_GET['tid']);

	if(!is_numeric($tid)) {
		ERROR::report($tid.' given for thread ID by '.$_SERVER['REMOTE_ADDR']);
		index();
	}

	$r = $SQL->query('SELECT * FROM `pst_threads` `p` INNER JOIN `ste_boards` `s` ON `p`.`board` = `s`.`id` WHERE `tid` = \''.$tid.'\' LIMIT 0,1');

	$threadInfo = $r->fetch_assoc();

	$P->set('title',$VAR['site_title'].' / '.$threadInfo['title']);
	$P->set('h1',$threadInfo['title']);

	$P->set('navbar',navbuild($SQL));

	$formVars = array(
	'action'	=> $_SERVER['REQUEST_URI'].'&amp;post=new',
	'ttle'		=> $threadInfo['title'],
	'unme'		=> $USR['name']
	);

	/*
	 *
	 * NEW POST HANDLING
	 *
	 */
	if(count($_POST) && $_GET['post'] == 'new' && $USR['level'] >= $threadInfo['reply'] && (time() - $_SESSION['postcooldown']) > 1) {

		$continue = TRUE;
		$reasons = array();

		// Make sure users are allowed to upload files at the size you set
		ini_set('upload_max_filesize',$VAR['maxFileSize']);
		$post	= &$_POST;
		$file	= &$_FILES;

		$imageTypes = array('image/gif','image/jpeg','image/png');

		$text = $FORM->scrub_text($post['text']);
		if(strlen($text) < 3) {$continue = FALSE; $reasons[] = "Posts must be at least 3 characters.";}


		/**
		 * 	This trys to figure out and report errors.  Important, errors that were not
		 * caused by normal user error are reported to the error log.
		 */

		// Check For File Upload Errors:
		$errmes='';
		$FORM->file_check($file['img1'],$imageTypes,$errmes);
		$reasons[] = $errmes;

		// Generate a 24 random digit file name
		$fname = ($file['img1']['error'] != UPLOAD_ERR_NO_FILE) ? rand_str().'.'.array_pop(explode('.',$file['img1']['name'])) : NULL;

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
			if(!move_uploaded_file($file['img1']['tmp_name'],$newfile)) {
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

		if($continue && $_GET['post'] == 'new') {

			if(!$SQL->query('INSERT INTO `pst_posts` (`thread`,`poster`,`image`,`text`) VALUES (\''.$tid.'\',\''.$USR['id'].'\',\''.$fname.'\',\''.$text.'\')')) {
				$continue = FALSE;
				$reasons[] = 'Database error, could not create post. Please try again later.';
				ERROR::report('Could not create post, attempting to remove traces. DB said: '.$SQL->error);

				if(!unlink($newfile)) {
					ERROR::report('Could not delete uploaded file after post failed. File: '.$newfile);
				}
			} else {
				if(!$SQL->query('UPDATE `pst_threads` SET `posted` = DEFAULT WHERE `tid` = \''.$threadInfo['tid'].'\'')) {
					ERROR::report('Could not update thread posted time on '.$threadInfo['tid']);
				}
				$refreshq = $SQL->query('SELECT `pid` FROM `pst_posts` WHERE `poster` = \''.$USR['id'].'\' AND `thread` = \''.$tid.'\' ORDER BY `post_time` DESC LIMIT 0,1');
				$refresha = $refreshq->fetch_assoc();
				$_SESSION['postcooldown'] = time();
				$P->set('headstuff','<meta http-equiv="refresh" content="0;url='.$VAR['base_url'].'/t.php?tid='.$tid.'#i'.$refresha['pid'].'" />');
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

	$q = $SQL->query('SELECT `p`.*, `u`.`id`, `u`.`name`, `u`.`avatar` FROM `pst_posts` as `p` INNER JOIN `usr_accounts` as `u` ON `p`.`poster` = `u`.`id` WHERE `p`.`thread` = \''.$tid.'\' ORDER BY `pid` ASC');

	$body = $errorBoxHtml;

	if($USR['level'] >= $threadInfo['reply']) {
		$body .= $P->formtovar('nerds','forms.php','newpost',$formVars);
	}

	$body .= '<div id="thread">'."\n";

	while($ch = $q->fetch_assoc()) {
		$ch['text'] = $BBC->parse($ch['text']);
		$derp = new POST($ch['id'], $ch['name'], $ch['avatar'], $ch['pid'], $ch['post_time'], $ch['text'], $ch['image'], $ch['tid']);
		$body .= $derp->postbox();
	}

	$body .= '</div>'."\n";

	$P->set('body',$body);
	$P->load('base.php');
	$P->render();
	?>