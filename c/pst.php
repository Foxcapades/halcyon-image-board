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

class POST {

public	$vars = array();
public	$avdir,$updir,$thdir;
public	$html;

/**
 * Post Construction
 *
 * @param int $uid User ID
 * @param string $unm Username
 * @param string $uav Avatar
 * @param int $pid Post Id
 * @param string $ptm Post Timestamp
 * @param string $ptx Post Text
 * @param string $pim Post Image Name
 * @param int $tid Thread ID
 * @param string $avdir Avatar Path
 * @param string $updir Image Path
 */
function __construct(	$uid,
						$unm,
						$uav,
						$pid,
						$ptm,
						$ptx,
						$pim,
						$tid,
						$avdir = 'i/av/',
						$updir = 'i/up/images/',
						$thdir = 'i/up/thumbs/'
	) {

	// Path to user avatars
	$this->avdir = $avdir;

	// Path to uploaded images
	$this->updir = $updir;

	// Path to thumbnails
	$this->thdir = $thdir;

	// User ID
	$this->vars['id'] = $uid;

	// User Name
	$this->vars['name'] = $unm;

	// User Avatar
	$this->vars['avatar'] = $uav;

	// Post ID
	$this->vars['pid'] = $pid;

	// Post Time
	$this->vars['post_time'] = $ptm;

	// Post Text
	$this->vars['text'] = $ptx;

	// Post Image
	$this->vars['image'] = $pim;

	// Thread ID
	$this->vars['tid'] = $tid;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Post Time Format

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

public function post_time() {
	$time_dif = time() - strtotime($this->vars['post_time']);

	if($time_dif == 1) {
		return 'Posted 1 second ago.';
	} elseif($time_dif < 60) {
		return 'Posted '.$time_dif.' seconds ago.';
	} elseif($time_dif < 120) {
		return 'Posted 1 minute ago.';
	} elseif($time_dif < 3600) {
		return 'Posted '.floor($time_dif / 60).' minutes ago.';
	} elseif($time_dif < 7200) {
		return 'Posted 1 hour ago.';
	} elseif($time_dif < 86400) {
		return 'Posted '.floor($time_dif / 3600).' hours ago.';
	} elseif($time_dif < 172800) {
		return 'Posted 1 day ago.';// at '.date('g:ia',strtotime($this->vars['post_time'])).'.';
	} elseif($time_dif < 604800) {
		return 'Posted '.floor($time_dif / 86400).' days ago.';
	} elseif($time_dif < 1209600) {
		return 'Posted 1 week ago.';
	} else {
		return 'Posted '.floor($time_dif / 604800).' weeks ago.';
	}
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Post Box

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

public function postbox($class = '') {

	$imagename = array_shift(explode('.',$this->vars['image']));

	$html  =
'
	<div id="i'.$this->vars['pid'].'" class="post'.(($class == '') ? '">'."\n\n" : ' '.$class.'">'."\n\n");

	$html .= (($this->vars['image'] != '' && $this->vars['image'] !=	NULL) ?
'
		<div class="imgbox">

			<a href="javascript:yeOldeSwitcheroo(\'t'.$imagename.'\',\''.$this->vars['image'].'\')">
				<img id="t'.$imagename.'" src="'.$this->thdir.$this->vars['image'].'" alt="image" />
			</a>

		</div>' : '').'
		<div class="charbox">

			<img src="'.$this->avdir.$this->vars['avatar'].'" alt="Avatar" />
			<div class="charinfo">

				<ul>

					<li class="name"><a href="u.php?view='.$this->vars['id'].'" title="View User Page">'.$this->vars['name'].'</a></li>
					<li style="font-size:.8em; line-height:1.2em;">'.$this->post_time().'</li>

				</ul>

			</div>

		</div>

		<div class="optbox">
';
		if($this->vars['id'] > 1 && $_SESSION['uid'] > 1) {

		$html .= (($this->vars['id'] == $_SESSION['uid']) ?

'			<a href="#" class="delbut" title="Delete Post">Delete</a>
':
'			<a href="#" class="repbut" title="Report Post">Report</a>
').
'			<a href="javascript:post_quote(\'i'.$this->vars['pid'].'\')" class="addbut" title="Reply to this post">Reply</a>
'.(($this->vars['id'] == $_SESSION['uid']) ?
'			<a href="#" class="edtbut" title="Edit Post">Edit Post</a>
':
'			<a href="#" class="mesbut" title="Message this posts author">Message</a>
');

	} elseif($this->vars['id'] == 1 && $_SESSION['uid'] > 1) {

		$html .=
'			<a href="#" class="repbut" title="Report Post">Report</a>
			<a href="#" class="addbut" title="Reply to this post">Reply</a>
';

	} else {

		$html .=
'			<a href="#" class="addbut" title="Reply to this post">Reply</a>
';
	}
	$html .=
'		</div>
';
	$text = preg_replace('/\r\n|\n\r|\n|\r/is','<br />',$this->vars['text']);
	$html .=
'		<div class="text">
			'.$text.'
		</div>
	</div>
';
$this->html = $html;
return $html;
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Thread View

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

// need to make this more 'generic' as well
public function threadview($arr1,$arr2) {
	$image_count = ($arr1['image_count'] > 1) ? $arr1['image_count'] - 1 : $arr1['image_count'];
	$images = ($image_count > 1) ? ' images.' : ' image.';
	$html = '<div class="thread"><div class="header">
	<a href="t.php?tid='.$arr1['tid'].'" title="'.$arr1['title'].'">'.$arr1['title'].'</a>'.$arr1['count'].' posts and '.$image_count.$images/*Last Post:'.date('l M jS, Y - g:i a',strtotime($arr2['post_time']))*/.'
</div>'."\n\n";
	$this->vars = $arr1;
	$html .= $this->postbox();
	/*if($arr2['pid'] != '' && $arr2['pid'] != NULL) {
		$this->vars = $arr2;
		$html .= $this->postbox('bottom');
	}*/
	$html .= /*"</div>*/"</div>\n\n";
	$this->html = $html;
	return $html;
}

//EOC
}
?>