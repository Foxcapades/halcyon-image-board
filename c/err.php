<?php
/**
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
 * Fatal Error Handler
 *
 *
 */
class ERROR {
	public function report($message = 'unknown') {
		if(file_exists('error.txt')) {
			file_put_contents('error.txt',"\n".date('h:i:s a - m:d:y').'	========	'.$message,FILE_APPEND);
		} else {
			file_put_contents('error.txt',date('h:i:s a - m:d:y').'	========	'.$message);
		}
	}

	public function dead($message = 'unknown') {

		$out = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Fatal Error</title>
	<style media="all" type="text/css">
		* {padding:0px;margin:0px;color:#111;font-family:Arial, Helvetica, sans-serif;}
		body {background-color:#222;}
		div {background-color:#dde;width:500px;margin:100px auto 0px auto;border:2px dotted #fff;text-align:center;padding:1em 0px;}
		ul {list-style:none;}
		li {color:#f56;font-style:italic;}
	</style>
</head>

<body>
	<div>
		<h1>A Fatal Error Occured</h1>
		<p>Please try again later.<br /><br />
		This Error has been reported to the administrators.</p>
	</div>
</body>
</html>';

		$this->report($message);
		die($out);
	}
}
?>