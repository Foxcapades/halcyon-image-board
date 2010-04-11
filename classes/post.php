<?php
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

class POST {

	public	$vars = array();
	public	$avdir,$updir,$thdir;
	public	$html;

	/**
	 * Post Construction
	 *
	 * @param int $user_id User ID
	 * @param string $unm Username
	 * @param string $uav Avatar
	 * @param int $post_id Post Id
	 * @param string $ptm Post Timestamp
	 * @param string $ptx Post Text
	 * @param string $pim Post Image Name
	 * @param int $thread_id Thread ID
	 * @param string $avdir Avatar Path
	 * @param string $updir Image Path
	 */
	function __construct(
	$user_id,
	$unm,
	$uav,
	$ulv,
	$uon,
	$mil,
	$post_id,
	$post_title,
	$ptm,
	$ptx,
	$pim,
	$thread_id,
	$avdir = 'images/av/',
	$updir = 'images/up/images/',
	$thdir = 'images/up/thumbs/'
	) {

		// Path to user avatars
		$this->avdir = $avdir;

		// Path to uploaded images
		$this->updir = $updir;

		// Path to thumbnails
		$this->thdir = $thdir;

		// User ID
		$this->vars['user_id'] = $user_id;

		// User Name
		$this->vars['name'] = $unm;

		// User Avatar
		$this->vars['avatar'] = $uav;

		// User Level
		$this->vars['level'] = $ulv;

		// Last User Activity
		$this->vars['last_ping'] = $uon;

		// User Email Address
		$this->vars['email'] = $mil;

		// Post ID
		$this->vars['post_id'] = $post_id;

		// Post Time
		$this->vars['post_time'] = $ptm;

		// Post Title
		$this->vars['post_title'] = $post_title;

		// Post Text
		$this->vars['text'] = $ptx;

		// Post Image
		$this->vars['image'] = $pim;

		// Thread ID
		$this->vars['thread_id'] = $thread_id;

	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

	* Post Time Format

	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	public function post_time() {
		$time_dif = time() - strtotime($this->vars['post_time']);

		if($time_dif == 1) {
			return '1 second ago';
		} elseif($time_dif < 60) {
			return $time_dif.' seconds ago';
		} elseif($time_dif < 120) {
			return '1 minute ago';
		} elseif($time_dif < 3600) {
			return floor($time_dif / 60).' minutes ago';
		} elseif($time_dif < 7200) {
			return '1 hour ago';
		} elseif($time_dif < 86400) {
			return floor($time_dif / 3600).' hours ago';
		} elseif($time_dif < 172800) {
			return '1 day ago';// at '.date('g:ia',strtotime($this->vars['post_time'])).'.';
		} elseif($time_dif < 604800) {
			return floor($time_dif / 86400).' days ago';
		} elseif($time_dif < 1209600) {
			return '1 week ago';
		} else {
			return floor($time_dif / 604800).' weeks ago';
		}
	}


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

	* Post Box

	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	public function postbox($class = '',$noheader = FALSE) {

		$onlineClass = ((time()-300) > $this->vars['last_ping']) ? '' : ' online';

		$this->vars['grav'] = 'http://www.gravatar.com/avatar/'.md5($this->vars['email']).'.jpg?s=50&d=identicon';

		$imagename = array_shift(explode('.',$this->vars['image']));

		$html  =
'
	<div id="i'.$this->vars['post_id'].'" class="post'.(($class == '') ? '">'."\n\n" : ' '.$class.'">'."\n\n");

		$html .= (!$noheader) ?
'		<div class="post_header">
			<span class="post_title"><img src="'.THEME_ICONSET_DIR.(($this->vars['image'] != '' && $this->vars['image'] !=	NULL)?'image.png':'document-horizontal-text.png').'" alt="IMG" title="Image Post" /> '.$this->vars['post_title'].'</span>
			<span class="post_number"></span>
		</div>' : '';

		$html .= (($this->vars['image'] != '' && $this->vars['image'] !=	NULL) ?
'
		<div class="imgbox">

			<a href="javascript:yeOldeSwitcheroo(\'t'.$imagename.'\',\''.$this->vars['image'].'\')">
				<img id="t'.$imagename.'" src="'.$this->thdir.$this->vars['image'].'" alt="image" />
			</a>

		</div>' : '').'
		<div class="charbox'.$onlineClass.'">

			<img src="'.$this->vars['grav'].'" alt="Avatar" />
			<div class="charinfo">

				<ul>

					<li class="name ulv'.$this->vars['level'].'"><a href="u.php?view='.$this->vars['user_id'].'" title="View User Page">'.$this->vars['name'].'</a></li>
					<li style="font-size:.8em; line-height:1.2em;">'.$this->post_time().'</li>

				</ul>
				<div class="optbox">
';
		if($this->vars['user_id'] > 1 && $_SESSION['user_id'] > 1) {

			$html .= (($this->vars['user_id'] == $_SESSION['user_id']) ?

'			<a href="#" class="delbut" title="Delete Post">Delete</a>
':
'			<a href="#" class="repbut" title="Report Post">Report</a>
').
'			<a href="javascript:post_quote(\'t_'.$this->vars['post_id'].'\')" class="addbut" title="Reply to this post">Reply</a>
'.(($this->vars['user_id'] == $_SESSION['user_id']) ?
'			<a href="#" class="edtbut" title="Edit Post">Edit Post</a>
':
'			<a href="#" class="mesbut" title="Message this posts author">Message</a>
');

		} elseif($this->vars['user_id'] == 1 && $_SESSION['user_id'] > 1) {

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
'				</div>
			</div>

		</div>
';
		$text = preg_replace('/\r\n|\n\r|\n|\r/is','<br />',$this->vars['text']);
		$html .=
'		<div id="t_'.$this->vars['post_id'].'" class="text">'.$text.'</div>
	</div>
';
		$this->html = $html;
		return $html;
	}


	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

	* Thread View

	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	// need to make this more 'generic' as well
	public function threadview($arr1) {
		$image_count = ($arr1['image_count'] > 1) ? $arr1['image_count'] - 1 : $arr1['image_count'];
		$images = ($image_count > 1) ? ' images' : ' image';
		$posts = ($arr1['count'] > 1) ? ' posts' : ' post';
		$html = '<div class="thread"><div class="header">
	<a href="t.php?thread_id='.$arr1['thread_id'].'" title="'.$arr1['title'].'">'.$arr1['title'].'</a>'.$arr1['count'].$posts.' with '.$image_count.$images.'
</div>'."\n\n";
		$this->vars = $arr1;
		$html .= $this->postbox('',TRUE);
		$html .= "</div>\n\n";
		$this->html = $html;
		return $html;
	}

	//EOC
}
?>