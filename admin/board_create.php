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
if(!count($_POST)) {
	$form = new newForm('?section=boards&mode=newBoard');
	$form->inputHTML('<div class="long_form">');
	$form->inputText('bnm','Board Name','','','',10);
	$form->inputHTML('<div class="form_explain">The board \'directory\' name, appears on the nav bar.</div></div><div class="long_form">');
	$form->inputText('bttl','Full Board Title');
	$form->inputHTML('<div class="form_explain">The full board title that shows at the top of the page.</div></div><div class="long_form">');
	$form->inputText('bms','Header Message');
	$form->inputHTML('<div class="form_explain">The message under the board title.</div></div><div class="long_form">');
	$form->inputSelect('blvl','Viewing Level');
	$form->addOption('Anonymous',1);
	$form->addOption('Registered',2);
	$form->addOption('Moderator',10);
	$form->addOption('Administrator',90);
	$form->addOption('Banned Users',0);
	$form->endSelect();
	$form->inputHTML('<div class="form_explain">Level at which users can see the board.</div></div><div class="long_form">');
	$form->inputSelect('plvl','Posting Level');
	$form->addOption('Anonymous',1);
	$form->addOption('Registered',2);
	$form->addOption('Moderator',10);
	$form->addOption('Administrator',90);
	$form->addOption('Banned Users',0);
	$form->endSelect();
	$form->inputHTML('<div class="form_explain">Level at which users can create new threads on the board.</div></div><div class="long_form">');
	$form->inputSelect('rlvl','Reply Level');
	$form->addOption('Anonymous',1);
	$form->addOption('Registered',2);
	$form->addOption('Moderator',10);
	$form->addOption('Administrator',90);
	$form->addOption('Banned Users',0);
	$form->endSelect();
	$form->inputHTML('<div class="form_explain">Level at which users can reply to already present threads on the board.</div></div><div class="long_form">');
	$form->inputCheckbox('lkd',1,'Lock the board?');
	$form->inputHTML('<div class="form_explain">Is this board locked?</div></div><div class="long_form">');
	$form->inputCheckbox('hdn',1,'Hide the board?');
	$form->inputHTML('<div class="form_explain">Is this board hidden?</div></div>');
	$form->inputSubmit('Create Board');
	$string_HTML_Return = $form->formReturn();
} else {
	/**
	 * bnm = board name
	 * bttl = board title
	 * bms = board message
	 * blvl = visibility level
	 * plvl = level required to post
	 * lkd = locked
	 * hdn = hidden
	 */
	$ern = 0;
	$ern += ($FORM->validate_text($_POST['bnm'],1,10)) ? 0 : 1;
	$ern += ($FORM->validate_text($_POST['bttl'],3,48)) ? 0 : 2;
	$ern += ($FORM->validate_text($_POST['bms'],3,128)) ? 0 : 4;
	$ern += (is_numeric($_POST['blvl']) && $_POST['blvl'] < 100  && $_POST['blvl'] >= 0) ? 0 : 8;
	$ern += (is_numeric($_POST['plvl']) && $_POST['plvl'] < 100  && $_POST['plvl'] >= 0) ? 0 : 16;
	$ern += (is_numeric($_POST['plvl']) && $_POST['rlvl'] < 100  && $_POST['plvl'] >= 0) ? 0 : 32;
	$_POST['lkd'] += ($_POST['lkd'] != 1) ? 0 : 1;
	$_POST['hdn'] += ($_POST['hdn'] != 1) ? 0 : 1;

	$bnm = $SQL->real_escape_string($_POST['bnm']);
	$bttl = $SQL->real_escape_string($_POST['bttl']);
	$bms = $SQL->real_escape_string($_POST['bms']);
	if($ern == 0) {

		$string_HTML_Return= '<div>Attempting to create board...<br />';
		if($SQL->query(

'INSERT INTO `'.DB_TABLE_BOARD_LIST.'`
VALUES (
	NULL,
	\'image\',
	\''.$bnm.'\',
	\''.$bttl.'\',
	\''.$bms.'\',
	\''.$_POST['hdn'].'\',
	\''.$_POST['lkd'].'\',
	\''.$_POST['plvl'].'\',
	\''.$_POST['blvl'].'\',
	\''.$_POST['rlvl'].'\'
)'
		)) {

			if($posarr = $SQL->query('SELECT `position` FROM `'.DB_TABLE_NAVIGATION.'` WHERE `position` < 250 ORDER BY `position` DESC LIMIT 0,1')) {

				if($chud = $SQL->query('SELECT `board_id` FROM `'.DB_TABLE_BOARD_LIST.'` WHERE `dir` = \''.$_POST['bnm'].'\' ORDER BY `board_id` DESC LIMIT 0,1')) {

					$nurm = $posarr->fetch_assoc();
					$hurp = $chud->fetch_assoc();
					$nurm['position'] += 1;

					// TODO: Allow setting the class and max user level.
					// TODO: find a way to do this with fewer queries
					// TODO: auto attempt cleanup on failure

					if($SQL->query(

'INSERT INTO `'.DB_TABLE_NAVIGATION.'`
VALUES (
	NULL,
	\''.$nurm['position'].'\',
	\'b.php?board='.$_POST['bnm'].'\',
	\''.$_POST['bttl'].'\',
	\''.$_POST['bnm'].'\',
	\'\',
	\''.$_POST['blvl'].'\',
	\'0\',
	\''.$hurp['board_id'].'\',
	\'boards\',
	\'image\'
)'

					)) {

						// TODO: verify this
						$string_HTML_Return .= '<span class="green">Board Created</span>';
					}else{
						$string_HTML_Return .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED</span>';
					}
				}
			} else {
				$string_HTML_Return .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED</span>';
			}
		} else {
			$string_HTML_Return .= '<span class="error">COULD NOT CREATE BOARD, SQL FAILED<br />Database Said:<br />'.$SQL->error.'</span>';
		}
	} else {echo $ern;}
}

?>