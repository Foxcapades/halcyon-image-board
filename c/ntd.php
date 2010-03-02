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
if(count($_POST)) {

	ini_set('upload_max_filesize',$VAR['maxFileSize']);
	$post	= &$_POST;
	$file	= &$_FILES;

	$imageTypes = array('image/gif','image/jpeg','image/png');

	$title = $FORM->scrub_text($post['ttle']);
	$uname = $FORM->scrub_text($post['unme']);
	$text = $FORM->scrub_text($post['text']);

	$continue = TRUE;
	$reasons = array();

	// Generate random 10 digit string for the thread key
	$threadkey = rand_str(10);


	/**
	 * 	This trys to figure out and report errors.  Important, errors that were not
	 * caused by normal user error are reported to the error log.
	 */

	if($file['img1']['error'] != UPLOAD_ERR_OK) {
		switch($file['img1']['error']) {

			// File is too large
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$reasons[] = 'File too big, try resizing it or uploading something else.';
				break;

				// Partial file upload
			case UPLOAD_ERR_PARTIAL:
				$reasons[] = 'File errored while uploading, please try again.';
				break;

				// No file was specified
			case UPLOAD_ERR_NO_FILE:
				$reasons[] = 'Could not find file specified, please try again.';
				break;

				// Missing a temporary folder
			case UPLOAD_ERR_NO_TMP_DIR:
				$reasons[] = 'Server side upload error. Error reported to admins, please check back later.';
				ERROR::report('User upload failed, server said NO TEMP DIR.');
				break;

				// Failed to write file to disk
			case UPLOAD_ERR_CANT_WRITE:
				$reasons[] = 'Server side upload error. Error reported to admins, please check back later.';
				ERROR::report('User upload failed, server said Failed to write to disk.');
				break;

				// File upload stopped by extension
			case UPLOAD_ERR_EXTENSION:
				$reasons[] = 'Server does not permit uploading files of the specified type. Error reported to admins, please choose another file or check back later.';
				ERROR::report('User:'.$_SERVER['REMOTE_ADDR'].' tried to upload a restricted file type ('.$file['img1']['type'].').');
				break;

			default:
				break;
		}
		$continue = FALSE;
	}

	// Generate a 24 random digit file name
	$fname = rand_str().'.'.array_pop(explode('.',$file['img1']['name']));

	// Target upload directory + Generated file name
	$newfile = $VAR['updir'] .= $fname;

	// Stop if the file is not an allowed type
	if(!in_array($file['img1']['type'],$imageTypes)) {

		$reasons[] = 'Invalid File Type, please choose another file and try again.';
		$continue = FALSE;

	}

	// Stop if the file is too big
	if($file['img1']['size'] > $VAR['maxFileSize']) {

		$reasons[] = 'File too big, try resizing it or uploading something else.';
		$continue = FALSE;

	}

	// Check if we should continue or not
	if($continue) {

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

	if($continue) {

		// Try and create the thread
		if(!$SQL->query('INSERT INTO `pst_threads` (`board`,`key`,`title`,`user`) VALUES(\''.$parts['id'].'\',\''.$threadkey.'\',\''.$title.'\',\''.$USR['id'].'\')')) {

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

		$nudes = $SQL->query('SELECT `tid`,`posted` FROM `pst_threads` WHERE `key` = \''.$threadkey.'\' AND `user` = \''.$USR['id'].'\' ORDER BY `tid` DESC LIMIT 0,1');

		if(!is_object($nudes)) {
			$continue = FALSE;
			$reasons[] = 'Database Error, please try again later.';
			ERROR::report('Error selecting created thread. Threadkey: \''.$threadkey.'\' DB said: '.$SQL->error);

			if(!unlink($newfile)) {
				ERROR::report('Could not delete uploaded file after threadselect failed. File: '.$newfile);
			}

		} else {

			$nudez = $nudes->fetch_assoc();

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
			}
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

	// Now lets clean up our vars
	unset($reason,$reasons,$nudes,$nudez,$newfile,$post,$file,$uname,$title,$text,$threadkey,$imageTypes);

}

?>