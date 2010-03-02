<?php
/**
 *
 *	Taco Image Board
 *  Copyright (C) 2009-2010  Steven K. Utiger
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
 *  Site Index
 *
 * 	At the moment, nothing more than a list of boards.  May change later...
 */
session_start();



if(file_exists('n/cnf.php')) {
	require_once 'n/cnf.php';
} else {
	die('dude, wtf');
}

index();
?>