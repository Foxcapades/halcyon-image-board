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
 * LIST OF VARS USED:
 *
 * $POST_ID
		Unique ID for each post, used in a number of situations where the
		individual post is referenced.

 * $POST_NUMBER
		The post number represents this post's position in the thread

 * $POST_ICON_URL
		URL to the icon to show in the post's 'title bar'

 * $POST_IMAGE_NAME
		The name assigned to the image and thumbnail for this post

 * $POST_IMAGE_FILE_NAME
		The full file name including the extension

 * $POST_IMAGE_FILE_PATH
		The full path to the image relative to the script's root directory

 * $POST_TEXT
		The post content

 * $POST_MAIN_CLASS
		The class for the main post container, used to style posts differently
		based on their content, thread position, or author

 * $POST_INCLUDE_HEADER
		Boolean TRUE or FALSE meant to determine whether or not to show the
		'title bar'.

 * $POST_INCLUDE_IMAGE
		Boolean TRUE or FALSE meant to determine whether or not to show the
		image box.

 * $POST_INCLUDE_EDIT
		Boolean TRUE or FALSE meant to determine whether or not to show the edit
		post link.

 * $POST_INCLUDE_DELETE
		Boolean TRUE or FALSE meant to determine whether or not to show the
		delete post link.

 * $POST_INCLUDE_QUOTE
		Boolean TRUE or FALSE meant to determine whether or not to show the
		quote/reply to post link.

 * $POST_INCLUDE_REPORT
		Boolean TRUE or FALSE meant to determine whether or not to show the
		report post link.

 * $POST_USER_LEVEL_CLASS

 * $POST_USER_NAME

 * $POST_USER_PAGE_HREF

 * $POST_USER_NAME

 * $POST_USER_RANK

 * $POST_USER_LEVEL

 * $POST_TIME_STAMP

 */
?>
<div id="i<?=$POST_ID; ?>" class="post<?=$POST_MAIN_CLASS?>">
<?php
if($POST_INCLUDE_HEADER) {	// START HEADER

?>
	<div class="post_header">
		<span class="post_title"><img src="<?=$POST_ICON_URL; ?>" alt="IMG" title="Image Post" /> <?=$POST_TITLE; ?></span>
		<span class="post_number">#<?=$POST_NUMBER; ?></span>
	</div>
<?php
}	// END HEADER
if($POST_INCLUDE_IMAGE) { // START IMAGE
?>
	<div class="imgbox">

		<a href="javascript:yeOldeSwitcheroo('t<?=$POST_IMAGE_NAME; ?>','<?=$POST_IMAGE_FILE_NAME; ?>')">
			<img id="t<?=$POST_IMAGE_NAME; ?>" src="<?=$POST_IMAGE_FILE_PATH; ?>" alt="image" />
		</a>

	</div>
<?php
}	// END IMAGE
?>
	<div class="charbox<?=$POST_CHARBOX_CLASS; ?>">

		<img src="<?=$POST_USER_AVATAR; ?>" alt="Avatar" />
		<div class="charinfo">

			<ul>

				<li class="name <?=$POST_USER_LEVEL_CLASS; ?>"><a href="<?=$POST_USER_PAGE_HREF; ?>" title="View User Page"><?=$POST_USER_NAME; ?></a></li>
				<li style="font-size:.8em; line-height:1.2em;"><?=$POST_TIME_STAMP; ?></li>

			</ul>
			<div class="optbox">
<?php
if($POST_INCLUDE_DELETE) {	// BEGIN DELETE LINK
?>
				<a href="#" class="delbut" title="Delete Post">Delete</a>
<?php
}	// END DELETE LINK
if($POST_INCLUDE_REPORT) {	// BEGIN REPORT LINK
?>
'				<a href="#" class="repbut" title="Report Post">Report</a>
<?php
}	// END REPORT LINK
if($POST_INCLUDE_QUOTE) {	// BEGIN QUOTE LINK
?>
				<a href="javascript:post_quote('t_<?=$POST_ID; ?>')" class="addbut" title="Reply to this post">Reply</a>
<?php
}	// END QUOTE LINK
if($POST_INCLUDE_EDIT) {	// BEGIN EDIT LINK
?>
				<a href="#" class="edtbut" title="Edit Post">Edit Post</a>
<?php
}	// END EDIT LINK
?>
			</div>
		</div>

	</div>
	<div id="t_<?=$POST_ID?>" class="text"><?=$POST_TEXT?></div>
</div>

