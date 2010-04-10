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

class newBoard {
	public	$id,
			$name,
			$title,
			$message,
			$hidden,
			$disabled,
			$post_level,
			$view_level,
			$reply_level,
			$last_error = FALSE,
			$errors = array();

	/**
	 * Constructor
	 *
	 * 	Presets all the info for the newBoard instance.  When calling, some or
	 * all of the parameters may be left blank, and can be set later.
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $title
	 * @param string $message
	 * @param int $hidden
	 * @param int $disabled
	 * @param int $post
	 * @param int $view
	 * @param int $reply
	 * @return bool
	 */
	public function newBoard
	(
		$id	= 0,
		$name	= '',
		$title	= '',
		$message	= '',
		$hidden	= 0,
		$disabled	= 0,
		$post	= 2,
		$view	= 1,
		$reply	= 2
	)
	{
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->message = $message;
		$this->hidden = $hidden;
		$this->disabled = $disabled;
		$this->post_level = $post;
		$this->view_level = $view;
		$this->reply_level = $reply;
		return TRUE;
	}
	/**
	 * Information
	 *
	 * 	Takes all the board info puts them in an array and returns them to the
	 * caller.  Useful for quick dumps into other functions or queries.
	 *
	 * @return array
	 */
	public function info()
	{
		$info['id'] = &$this->id;
		$info['name'] = &$this->name;
		$info['title'] = &$this->title;
		$info['message'] = &$this->message;
		$info['hidden'] = &$this->hidden;
		$info['disabled'] = &$this->disabled;
		$info['post_level'] = &$this->post_level;
		$info['view_level'] = &$this->view_level;
		$info['reply_level'] = &$this->reply_level;
		return $info;
	}
	public function create_thread() {}
}

class newThread {
	public	$id,
			$parent_id,
			$user_id,
			$title;
	public function create_post(){}
}

class newPost {
	public	$id,
			$parent_id,
			$user_id,
			$image,
			$text;

}
?>