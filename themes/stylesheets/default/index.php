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

header('Content-Type: text/css');

$LONG_CSS = '';
$LONG_CSS .= file_get_contents('admin.css');
$LONG_CSS .= file_get_contents('base.css');
$LONG_CSS .= file_get_contents('bbcd.css');
$LONG_CSS .= file_get_contents('html.css');

echo $LONG_CSS;
?>