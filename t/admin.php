<?php
/**
 *
 *	Halcyon Image Board
 *  Copyright (C) 2010  Steven Utiger
 *
 *  This program is free software: you can redistribute it and/or modify it
 *  under the terms of the GNU General Public License as published by the Free
 *  Software Foundation, either version 3 of the License, or any later version.
 *
 *  This program is distributed in the hope that it will be useful, but WITHOUT
 *  ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 *  FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 *  more details.
 *
 *  You should have received a copy of the GNU General Public License along with
 *  this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * 	This page is going to be a mess of nested switches.  When i finish
 * everything, I will come back to sort this out...  Sorry.
 *
 */


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$title; ?></title>
	<link href="s/base.css" rel="stylesheet" type="text/css" />
	<link href="s/bbcd.css" rel="stylesheet" type="text/css" />
	<link href="s/html.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="c/bbsrc.js"></script>
	<?=$headstuff; ?>
</head>

<body>

	<div id="admheader">

		<h1><?=$h1; ?></h1>
		<span><?=$mes; ?></span>

	</div>
	<div class="navbar inchead">

		<ul>

<?=$navbar; ?>

		</ul>

	</div>
	<div class="admbody">
		<div class="admnav">

<?=$adnav; ?>

		</div>
		<div class="admcon">
<?=$body; ?>
		</div>
	</div>
	<div id="footer">&copy; 2010 Steven Utiger, Rev-rocom</div>

</body>

</html>