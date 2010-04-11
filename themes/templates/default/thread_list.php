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
?>
<div id="thread_box">
<?=$thread_list; ?>
</div>
<div id="side_box">
	<h2>Side Bar</h2>
	<div id="user_info_box">
		<?=$user_box_content; ?>
		<h3 class="hidden">User Info</h3>
		<h4><a href="usr.php?mode=uac" title="Edit Account Settings"><?=$current_user_name; ?></a></h4>
		<div id="usr_avatar">
			<img src="<?=$current_user_avatar; ?>" alt="Avatar" title="<?=$current_user_name; ?>" />
		</div>
		<ul>
			<li>Rank: <?=$current_user_rank; ?></li>
			<li><?=$current_user_unread_posts; ?> Unread Posts</li>
			<li><?=$current_user_unread_messages; ?> Unread PMs</li>
			<li><a href="usr.php?mode=logout" title="Logout">Logout</a></li>
		</ul>
	</div>
	<div id="side_nav_bar">
		<h3>Boards:</h3>
		<?=$side_nav; ?>
	</div>
</div>