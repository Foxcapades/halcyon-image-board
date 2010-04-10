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

class manage_board
{

Private $SQL;
public $errors = array();

public function __construct(&$SQL)
{$this->SQL =& $SQL;}

public function delete_post()
{

}

public function delete_thread()
{
	if($SQL->query(

'DELETE FROM `'.DB_TABLE_THREAD_LIST.'`
WHERE `board_id` IN (\''.$_SESSION['boards'].'\')'

	))
	{

		return TRUE;

	}
	else
	{

		return FALSE;

	}
}

public function delete_board($board_id)
{

	if(is_array($board_id))
	{

		$where = 'IN (\''.implode('\', \'',$board_id).'\')';

	}
	else
	{

		$where = ' = \''.$board_id.'\'';

	}

	if($SQL->query(

'DELETE FROM `'.DB_TABLE_BOARD_LIST.'`
WHERE `board_id` '.$where

	))
	{

		if($this->delete_link($board_id))
		{

			return TRUE;

		}
		else
		{

			return FALSE;

		}

	}
	else
	{

		return FALSE;

	}

}

private function delete_image()
{}

private function delete_link($board_id)
{

	if(is_array($board_id))
	{

		$where = 'IN (\''.implode('\', \'',$board_id).'\')';

	}
	else
	{

		$where = ' = \''.$board_id.'\'';

	}

	if($this->SQL->query(

'DELETE FROM `'.DB_TABLE_NAVIGATION.'`
WHERE `board_id` '.$where

	))
	{

		return TRUE;

	}
	else
	{

		return FALSE;

	}

}

}
?>