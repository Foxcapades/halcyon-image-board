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
/**
 * An array to hold the info pulled from the database for the table
 *
 * @var array
 */
$array_SiteStats = array();
$object_StatsResult = $SQL->query('SELECT * FROM `'.$databaseTables['statistics'].'`');
while($temp = $object_StatsResult->fetch_assoc())
{
	$array_SiteStats[$temp['stat']] = $temp['value'];
}
$string_HTML_Table = '
	<table id="admin_stat_table">
		<thead>
			<tr>
				<th>Description</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Installed</td>
				<td>'.date('l F d, Y',$array_SiteStats['installed']).'</td>
			</tr>
			<tr>
				<td>Board Version</td>
				<td>'.$VAR['version'].'</td>
			</tr>
			<tr>
				<td>Posts</td>
				<td>'.$array_SiteStats['posts'].'</td>
			<tr>
			<tr>
				<td>Images</td>
				<td>'.$array_SiteStats['image_posts'].'</td>
			<tr>
						<tr>
				<td>Threads</td>
				<td>'.$array_SiteStats['threads'].'</td>
			<tr>
			<tr>
				<td>Registered Users</td>
				<td>'.$array_SiteStats['reg_users'].'</td>
			<tr>
		</tbody>
	</table>
';
$string_HTML_Body .= '<div id="admin_body_content"><h2>Site Info</h2>'.$string_HTML_Table.'</div>';
?>