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
 * Random string generator
 *
 * 	Used in generating keys for posts, names for files etc...
 *
 * @param int $length Length of the return string
 * @param string $chars Character pool to pull from
 * @return string
 */
function rand_str($length = 24, $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890_-')
{

	$poolcount = strlen($pool) - 1;

	$string = $pool{rand(0, $poolcount)};

	for ($i = 1; $i < $length; $i = strlen($string))
	{

		$r = $pool{rand(0, $poolcount)};
		if ($r != $string{$i - 1}) $string .=  $r;

	}

	return $string;

} // - END rand_str()
// FIXME unsecure mimetype discovery
function mime_type($filename)
{

	$mime_types = array(
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'png' => 'image/png',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'tif' => 'image/tiff',
		'tiff' => 'image/tiff'
	);

	$ext = strtolower(array_pop(explode('.',$filename)));
	if (array_key_exists($ext, $mime_types))
	{
		return $mime_types[$ext];
	}
	else
	{
		return FALSE;
	}
}
/**
 * Image Thumbnail generator
 *
 * 	Takes the user uploaded image and attempts to create a thumbnail for it.
 *
 * TODO: Use a generic thumbnail on error
 * TODO: Simplify function, 'cause i know there are some extra steps
 *
 * @param string $image Source Image Path - 'i/up/images/name.file'
 * @param string $thumb Target Thumbnail Path - 'i/up/thumbs/name.file'
 * @return boolean
 */
function makethumb($image,$thumb)
{

	$memin = getimagesize($image);
	$size = (memory_get_usage()+ceil($memin[0]*$memin[1]*$memin['bits']))*1.5;

	ini_set('memory_limit', $size);

	$mType = mime_type($image);

	if($memin[0] != $memin[1])
	{
		if($memin[0] > $memin[1])
		{

			$newx = 150;
			$tarp = round(150/$memin[0],4);
			$newy = round($memin[1]*$tarp);

		}
		else
{
			$newy = 150;
			$tarp = round(150/$memin[1],4);
			$newx = round($memin[0]*$tarp);

		}

	}
	else
{
		$newx = 150;
		$newy = 150;

	}

	if($mType == 'image/jpeg')
	{
	$gump = imagecreatefromjpeg($image);}
	elseif ($mType == 'image/gif')
	{
	$gump = imagecreatefromgif($image);}
	elseif ($mType == 'image/png')
	{
	$gump = imagecreatefrompng($image);}
	else
	{
	return FALSE;}

	$gimp = imagecreatetruecolor($newx,$newy);
	imagecopyresampled($gimp,$gump,0,0,0,0,$newx,$newy,$memin[0],$memin[1]);

	imagedestroy($gump);

	if($mType == 'image/jpeg')
	{
	imagejpeg($gimp,$thumb);}
	elseif ($mType == 'image/gif')
	{
	imagegif($gimp,$thumb);}
	elseif ($mType == 'image/png')
	{
	imagepng($gimp,$thumb);}
	else
	{
	return FALSE;}

	imagedestroy($gimp);
	ini_restore('memory_limit');
	return TRUE;

} // - END makethumb()function index()
function index()
{
	global $P,$VAR;
	$P->set('title',$VAR['site_title']);
	$P->set('h1',$VAR['base_header']);
	$P->set('mes',$VAR['base_mes']);
	$strPageHTML = "<div class=\"boards\">\n";
	$strPageHTML .= navbuild($SQL);
	$strPageHTML .= "	\n</div>";
	$P->set('body',$strPageHTML);
	$P->load('base.php');
	$P->render();
	die();
}
function navbuild(&$sql)
{
	Global $SQL,$USR,$BINFO;
	$cheese = $SQL->query('SELECT * FROM `ste_navbar` ORDER BY `position` ASC');
	$out="<ul>\n";
	while($nim = $cheese->fetch_assoc())
	{
		$nim['usr_max'] = ($nim['usr_max'] == 0) ? 9001 : $nim['usr_max'];
		if($USR['level'] >= $nim['usr_thresh'] && $USR['level'] <= $nim['usr_max'])
		{

			$out .= '<li><a href="'.$nim['href'].'" title="'.$nim['title'].'"';

			$out .= ($nim['class']!='' && $nim['text'] == $BINFO['dir']) ? ' class="'.$nim['class'].' here"' : '';
			$out .= ($nim['class']!='' && $nim['text'] =! $BINFO['dir']) ? ' class="'.$nim['class'].'"' : '';
			$out .= ($nim['class']=='' && $nim['text'] == $BINFO['dir']) ? ' class="here"' : '';

			$out .= '>'.$nim['text'].'</a></li>';
		}
	}
	return $out."</ul>\n";
}
function pingUser($forced = FALSE)
{
	global $SQL,$USR;
	$timeNow = time();
	$time5Ago = $timeNow - 300;

	$SQL->query('DELETE FROM `user_online` WHERE `last_ping` <= \''.($time5Ago-300).'\'');

	$countVerify = $SQL->query('SELECT * FROM `user_online` WHERE `current_ip` = \''.$_SERVER['REMOTE_ADDR'].'\' AND `user_id` = \''.$_SESSION['user_id'].'\'');
	if($countVerify->num_rows == '0')
	{

		$SQL->query('INSERT INTO `user_online` VALUES (\''.$_SESSION['user_id'].'\',\''.$timeNow.'\',\''.$_SERVER['REMOTE_ADDR'].'\')');

	}
	else
	{

		$SQL->query('UPDATE `user_online` SET `last_ping` = \''.$timeNow.'\' WHERE `user_id` = \''.$_SESSION['user_id'].'\' AND `current_ip` = \''.$_SERVER['REMOTE_ADDR'].'\'');

	}

}


?>