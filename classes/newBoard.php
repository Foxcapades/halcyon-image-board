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

/**
 * @author Lucent
 *
 */
class Board {

	public
		$id,
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

	protected
		$threads = array();

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
	public function __construct
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


	/**
	 * Create Thread
	 *
	 * 	This function will create a 'child' instance of the Thread class, store
	 * it and return a reference to the created Thread instance for further
	 * manipulation.
	 *
	 * @param int $id
	 * @param string $title
	 * @param int $author
	 * @param int $date
	 * @return Thread
	 */
	public function create_thread
	(
		$id = 0,
		$title = '',
		$author = 0,
		$date = 0
	)
	{
		$threads =& $this->threads;
		$threads[$id] = new Thread($id,$this->id,$title,$author,$date);
		return $threads[$id];
	}


	public function show_threads($start,$num){}


	public function sort_threads(){}

}

class Thread {

	public
		$id,
		$parent_id,
		$user_id,
		$key,
		$title,
		$time;

	protected
		$posts = array();

	public function __construct
	(
		$id = 0,
		$parent = 0,
		$title = '',
		$author = 0,
		$date = 0
	)
	{
		$this->id = $id;
		$this->parent_id = $parent;
		$this->user_id = $author;
		$this->time = $date;
		$this->title = $title;
	}


	public function create_post
	(
		$id = 0,
		$title = '',
		$author = 0,
		$date = 0
	)
	{
		$post =& $this->posts;
		$post[$id] = new Post($id,$this->id,$title,$author,$date);
		return $post[$id];
	}

	public function show_posts($start,$num){}
	public function sort_posts(){}
}

class Post {

	public
		$id,
		$parent,
		$user = array(),
		$title,
		$number,
		$time,
		$image = array(),
		$text;

	public function __construct
	(
		$id = '',
		$parent = '',
		$title = '',
		$author = '',
		$date = ''
	)
	{
		$this->id = $id;
		$this->parent_id = $parent;
		$this->user_id = $author;
		$this->time = $date;
		$this->title = $title;
	}

	public function load_template($html)
	{
		$template_vars = array
		(
		);
	}

	public function render()
	{}
}

?>