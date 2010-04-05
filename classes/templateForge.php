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

/**
 * Page Construction Class
 *
 * 	This class handles the template files, puts them into an array of usable
 * variables, and can render the finished page.
 *
 */

class templateForge
{

	public $vars = array();
	public $html;
	private $conf = array();


	function loadtovar($varname, $filename)
	{

		if ($html = $this->load($filename))
		{

			$this->set($varname, $html);

			return $html;

		} else {

			return false;

		}

	}

	function formtovar($varname,$filename,$form,$array = array())
	{

		extract($array);

		ob_start();

		include('t/' . $filename);

		$this->vars[$varname] = ob_get_clean();

		return $this->vars[$varname];

	}

	function set($key,$value = '') {

		if(!is_array($key)) {

			$this->vars[$key] = $value;

		} else {

			foreach($key as $k=>$v) {

				$this->vars[$k] = $v;

			}

		}

	}

	function load($filename)
	{


		extract($this->vars);
		ob_start();

		include($filename);

		$this->html = ob_get_clean();

		return $this->html;
	}

	function render($return = false)
	{

		$this->vars['pagebody'] = $this->html;

//		if ($this->layout) {

//			ob_start();
//			echo $this->load($this->layout);
//			$html = ob_get_clean();

//		} else {

			$html = $this->html;

//		}

		if (!$return) {

			// gzip enabled by config?

			if (isset($this->config['gzip']) && $this->config['gzip'] == true) {

				ob_start();
				ob_start('ob_gzhandler');

				echo $html;

				ob_end_flush();

				header('Content-Length: '.ob_get_length());

				ob_end_flush();

			} else {

				echo $html;

			}

		} else {

			return $html;

		}

	}

}
?>