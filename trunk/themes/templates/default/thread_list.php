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
		<h3 class="hidden">User Info</h3>
		<ul>
			<li><a href="usr.php?mode=uac" title="Edit Account Settings"><?=$current_user_name; ?></a></li>
			<li>
				<div id="usr_avatar">
					<img src="<?=$current_user_avatar; ?>" alt="Avatar" title="<?=$current_user_name; ?>" />
				</div>
			</li>
			<li>
				<dl>
					<dt>Rank:</dt>
						<dd><?=$current_user_rank; ?></dd>
					<dt>Unread Posts:</dt>
						<dd><?=$current_user_unread_posts; ?></dd>
					<dt>Messages:</dt>
						<dd><?=$current_user_unread_messages; ?> unread</dd>
						<dd><?=$current_user_total_messages; ?> total</dd>
				</dl>
			</li>
			<li><a href="usr.php?mode=logout" title="Logout">Logout</a></li>
		</ul>
	</div>
	<div id="side_nav_bar">
		<h3>Boards:</h3>
		<?=$side_nav; ?>
	</div>
</div>