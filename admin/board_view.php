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
 * Result class for the query that should pull all info for all the boards
 * in the database.
 *
 * @var object
 */
$object_boardResult = $SQL->query(
'SELECT *
FROM `ste_boards`
ORDER BY `board_id` ASC'
);

/**
 * Array that will contain a set of sub arrays that represent the boards on
 * the site.
 *
 * @var array
 */
$array_boardList = array();

/**
 * A string that will hold the HTML for the list of boards on the home page
 * for the boards section.
 *
 * @var string
 */
$string_HTML_Return  = '<table id="admin_board_list">'."\n";
$string_HTML_Return .= '	<col class="bdLstCol0" />'."\n";
$string_HTML_Return .= '	<col class="bdLstCol1" />'."\n";
$string_HTML_Return .= '	<col class="bdLstCol2" />'."\n";
$string_HTML_Return .= "	<tbody>\n";

/**
 * Put together a table of the boards with links to various options to
 * manipulate them
 */
while($temp = $object_boardResult->fetch_assoc())
{
	$array_boardList[] = $temp;
	$string_HTML_Return .= '		<tr id="btd_'.$temp['board_id'].'">'."\n";
	$string_HTML_Return .= '			<td class="bdLstTitle">'.$temp['dir']."</td>\n";
	$string_HTML_Return .= '			<td class="bdLstTitle">'.$temp['name']."</td>\n";
	$string_HTML_Return .= "			<td class=\"bdLstOps\">\n";
	$string_HTML_Return .= "				<ul>\n";
	$string_HTML_Return .= '					<li><a href="'.$VAR['base_url'].'/admin/index.php?section=boards&mode=delBoard&board='.$temp['board_id'].'" title="Delete this board?"><img src="../themes/iconsets/'.$VAR['iconset_dir'].'cross.png" alt="Delete" /></a></li>'."\n";
	$string_HTML_Return .= '					<li><a href="'.$VAR['base_url'].'/admin/index.php?section=boards&mode=editBoard&board='.$temp['board_id'].'" title="Edit this board?"><img src="../themes/iconsets/'.$VAR['iconset_dir'].'/gear.png" alt="Edit" /></a></li>'."\n";
	$string_HTML_Return .= '					<li><a href="'.$VAR['base_url'].'/admin/index.php?section=boards&mode=clearBoard&board='.$temp['board_id'].'" title="Clear this board?"><img src="../themes/iconsets/'.$VAR['iconset_dir'].'/bin.png" alt="Clear" /></a></li>'."\n";
	$string_HTML_Return .= "				</ul>\n";
	$string_HTML_Return .= "			</td>\n";
	$string_HTML_Return .= "		</tr>\n";
}
$string_HTML_Return .= "	</tbody>\n";
$string_HTML_Return .= "</table>\n";

?>