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
$array_VarsCopy = $_POST;
if(count($_POST))
{
	$continue = TRUE;
	$ERRORMES = '';
	if(!$FORM->validate_text($_POST['site_title'],4,255))
	{
		$continue = FALSE;
		$ERRORMES .= '<div class="error">Invalid Site Title</div>';
	}
	if(!$FORM->validate_text($_POST['base_mes'],4,255))
	{
		$continue = FALSE;
		$ERRORMES .= '<div class="error">Invalid Header Message</div>';
	}
	if(!$FORM->validate_text($_POST['base_header'],4,255))
	{
		$continue = FALSE;
		$ERRORMES .= '<div class="error">Invalid Site Header</div>';
	}
	if(!$FORM->validate_url($_POST['base_url'],4,255))
	{
		$continue = FALSE;
		$ERRORMES .= '<div class="error">Invalid URL</div>';
	}
	if($continue)
	{
		$string_HTML_Return = '<div class="box">Attempting to insert values...</div>';
		if($SQL->query(
'UPDATE `'.$databaseTables['global_vars'].'` SET
`value` =
CASE `key`
	WHEN \'site_title\' THEN \''.$_POST['site_title'].'\'
	WHEN \'base_header\' THEN \''.$_POST['base_header'].'\'
	WHEN \'base_mes\' THEN \''.$_POST['base_mes'].'\'
	WHEN \'base_url\' THEN \''.$_POST['base_url'].'\'
ELSE `value` END'
		))
		{
			$string_HTML_Return .= '<div class="green">Update Completed</div>';
		}
		else
		{
			$string_HTML_Return .= '<div class="green">Could not update, database said: '.$SQL->error.'</div>';
		}
	}

}

?>