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

/**
 * 	Form Templates for use throughout the site.
 */

// Prevent any 'unauthorized' access to the forms.

if($_SERVER['PHP_SELF'] == '/forms.php'){die();}

switch ($form) {

case 'newboard':

	?>
<form id="NewBoard" method="post" action="<?=$action; ?>">
	<fieldset><legend>New Board</legend>
		<div>
			<label for="bnm">Board Name</label>
			<input type="text" name="bnm" id="bnm" value="<?=$bnm; ?>" /></div>
		<div>
			<label for="bttl">Board Header</label>
			<input type="text" name="bttl" id="bttl" value="<?=$bttl; ?>" /></div>
		<div>
			<label for="bms">Header Message</label>
			<input type="text" name="bms" id="bms" value="<?=$bms; ?>" /></div>
		<div>
			<label for="blvl">Access Level</label>
			<select name="blvl" id="blvl">
				<option value="1">Anonymous</option>
				<option value="2">Registered</option>
				<option value="10">Mods Only</option>
				<option value="90">Admins Only</option>
				<option value="0">Banned Users</option>
			</select>
		</div>
		<div class="half">
			<label for="lkd">Locked</label> <input type="checkbox"
	value="1" name="lkd" id="lkd" /></div>
		<div class="half">
			<label for="hdn">Hidden</label> <input type="checkbox"
	value="1" name="hdn" id="hdn" /></div>
		<div>
			<label for="bplvl">Posting Level</label> <select name="plvl"
	id="plvl">

				<option value="1">Anonymous</option>
				<option value="2">Registered</option>
				<option value="10">Mods Only</option>
				<option value="90">Admins Only</option>
				<option value="0">Banned Users</option>

			</select></div>

<input type="submit" value="Create Board" /></fieldset>

</form>
	<?php

	break;
case 'sitesettings':

	?>
<form method="post" action="<?=$action; ?>">

<fieldset><legend>General Board Settings</legend>

<div class="full"><label for="sttle">Site Title</label> <input
	type="text" name="sttle" id="sttle" value="<?=$sttle; ?>" /></div>
<div class="full"><label for="shdr">Site Header</label> <input
	type="text" name="shdr" id="shdr" value="<?=$shdr; ?>" /></div>
<div class="full"><label for="bmes">Base Header Message</label> <input
	type="text" name="bmes" id="bmes" value="<?=$bmes; ?>" /></div>
<div class="full"><label for="benab">Site Header</label> <input
	type="checkbox" name="benab" id="benab" value="1" <?=$shdr; ?> /></div>

</fieldset>

</form>

	<?php

	break;
default:

	?>
<form method="post" action="<?=$ninjas; ?>"><?=$contents; ?></form>
	<?php

	break;
}

?>
