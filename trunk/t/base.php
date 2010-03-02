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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$title; ?></title>
	<link href="s/base.css" rel="stylesheet" type="text/css" />
	<link href="s/bbcd.css" rel="stylesheet" type="text/css" />
	<link href="s/html.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="c/bbsrc.js"></script>
	<script type="text/javascript">
		function yeOldeSwitcheroo(id,imagename) {
			var thumbdir
			thumbdir = "<?='http://'.$_SERVER['HTTP_HOST'].'/'.$thumbdir; ?>"+imagename;
			var maindir
			maindir = "<?='http://'.$_SERVER['HTTP_HOST'].'/'.$imagedir; ?>"+imagename;
			var img = document.getElementById(id);
			if(img.src == thumbdir) {
				img.src = maindir;
			} else {
				img.src = thumbdir;
			}
		}
	</script>
<?=$headstuff; ?>
</head>

<body>
	<div id="header">

		<h1><?=$h1; ?></h1>
		<span><?=$mes; ?></span>

	</div>
	<div class="navbar">


<?=$navbar; ?>


	</div>
<?=$body; ?>
	<div class="navbar">


			<?=$navbar; ?>


	</div>
	<div id="footer">&copy; 2009-2010 Lucent, Rev-rocom</div>

</body>

</html>