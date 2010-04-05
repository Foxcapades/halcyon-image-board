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
$strBoardID = (isset($_GET['board']) && is_numeric($_GET['board'])) ? $_GET['board'] : '0';
$strBoardID = $SQL->real_escape_string($_GET['board']);
$objBoardInfoResult = $SQL->query(
'SELECT *
FROM `'.$databaseTables['boards'].'`
WHERE `board_id` = \''.$strBoardID.'\'
LIMIT 0,1');

if($objBoardInfoResult->num_rows == 0) {
	$string_HTML_Return = '<p>Could not find a board with the ID specified.';
	return;
}
elseif(!count($_POST))
{
	$arrBoardInfo = $objBoardInfoResult->fetch_assoc();
	$form = new newForm('?'.$_SERVER['QUERY_STRING']);
	$form->inputHTML('<div class="long_form">');
	$form->inputText('bnm','Board Name',$arrBoardInfo['dir'],'','',10);
	$form->inputHTML('<div class="form_explain">The board \'directory\' name, appears on the nav bar.</div></div><div class="long_form">');
	$form->inputText('bttl','Full Board Title',$arrBoardInfo['name']);
	$form->inputHTML('<div class="form_explain">The full board title that shows at the top of the page.</div></div><div class="long_form">');
	$form->inputText('bms','Header Message',$arrBoardInfo['mes']);
	$form->inputHTML('<div class="form_explain">The message under the board title.</div></div><div class="long_form">');
	$form->inputSelect('blvl','Viewing Level');
	foreach($VAR['userLevelList'] as $k => $v)
	{
		if($arrBoardInfo['view_min_lvl'] == $v)
		{
			$form->addOption($k,$v,TRUE);
		}
		else
		{
			$form->addOption($k,$v);
		}
	}
	$form->endSelect();
	$form->inputHTML('<div class="form_explain">Level at which users can see the board.</div></div><div class="long_form">');
	$form->inputSelect('plvl','Posting Level');
	foreach($VAR['userLevelList'] as $k => $v)
	{
		if($arrBoardInfo['view_min_lvl'] == $v)
		{
			$form->addOption($k,$v,TRUE);
		}
		else
		{
			$form->addOption($k,$v);
		}
	}
	$form->endSelect();
	$form->inputHTML('<div class="form_explain">Level at which users can create new threads on the board.</div></div><div class="long_form">');
	$form->inputSelect('rlvl','Reply Level');
	foreach($VAR['userLevelList'] as $k => $v)
	{
		if($arrBoardInfo['view_min_lvl'] == $v)
		{
			$form->addOption($k,$v,TRUE);
		}
		else
		{
			$form->addOption($k,$v);
		}
	}
	$form->endSelect();
	$form->inputHTML('<div class="form_explain">Level at which users can reply to already present threads on the board.</div></div><div class="long_form">');
	if($arrBoardInfo['disabled'] == 1)
	{
		$form->inputCheckbox('lkd',1,'Lock Board?','','',TRUE);
	}
	else
	{
		$form->inputCheckbox('lkd',1,'Lock Board?');
	}
	$form->inputHTML('<div class="form_explain">Is this board locked?</div></div><div class="long_form">');
	if($arrBoardInfo['hidden'] == 1)
	{
		$form->inputCheckbox('hdn',1,'Hide Board?','','',TRUE);
	}
	else
	{
		$form->inputCheckbox('hdn',1,'Hide Board?');
	}
	$form->inputHTML('<div class="form_explain">Is this board hidden?</div></div>');
	$form->inputSubmit('Submit Changes');
	$string_HTML_Return = $form->formReturn();
	}
else
{
	$cleanArray = array();
	$_POST['lkd'] = (isset($_POST['lkd']) && $_POST['lkd'] == 1) ? 1 : 0;
	$_POST['hdn'] = (isset($_POST['hdn']) && $_POST['hdn'] == 1) ? 1 : 0;

	foreach($_POST as $k => $v)
	{
		$cleanArray[$k] = $FORM->scrub_text($v);
	}

	$neededKeys = array('bnm'=>'','bttl'=>'','bms'=>'','blvl'=>'','plvl'=>'','rlvl'=>'','hdn'=>'','lkd'=>'');
	if(count($neededKeys)==count(array_intersect_key($neededKeys,$cleanArray)))
	{
		$string_HTML_Return = '<div>Updating Tables:</div>';
		if($SQL->query(

'UPDATE `'.$databaseTables['boards'].'`
SET
	`dir` = \''.$cleanArray['bnm'].'\',
	`name` = \''.$cleanArray['bttl'].'\',
	`mes` = \''.$cleanArray['bms'].'\',
	`hidden` = \''.$cleanArray['hdn'].'\',
	`disabled` = \''.$cleanArray['lkd'].'\',
	`post_min_lvl` = \''.$cleanArray['plvl'].'\',
	`view_min_lvl` = \''.$cleanArray['blvl'].'\',
	`reply_min_lvl` = \''.$cleanArray['rlvl'].'\'
WHERE `board_id` = \''.$strBoardID.'\''

		))
		{
			$string_HTML_Return .= '<div class="green">Board Table Updated</div>';
			if($SQL->query(

'UPDATE `'.$databaseTables['nav_bar'].'`
SET
	`href` = \'b.php?board='.$cleanArray['bnm'].'\',
	`title` = \''.$cleanArray['bttl'].'\',
	`text` = \''.$cleanArray['bnm'].'\',
	`usr_thresh` = \''.$cleanArray['blvl'].'\',
	`usr_max` = \'0\'
WHERE `board_id` = \''.$strBoardID.'\''

			))
			{
				$string_HTML_Return .= '<div class="green">Link Table Updated</div>';
			}
			else
			{
				$string_HTML_Return .= '<div class="error">Could not update link table.  MySQLi says: '.$SQL->error.'</div>';
				ERROR::report('Could not update link table on admin panel edit board.');
			}
		}
		else
		{
			$string_HTML_Return .= '<div class="error">Could not update board table.  MySQLi says: '.$SQL->error.'</div>';
			ERROR::report('Could not update board table on admin panel edit board.');
		}
	}
	else
	{
		$string_HTML_Return .= '<div class="error">Invalid form field count.</div>';
	}

}

?>