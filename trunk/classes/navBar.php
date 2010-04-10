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
 * Base Navigation Constructor
 *
 * 	Basically, this is the base for the nav bar functions,
 * @author Lucent
 *
 */
class navBar
{
	private $array_Links = array();

	public function addLink($url, $title, $desc ='', $class ='', $id ='')
	{
		$array_LinkArray = array('type'=>'link', 'href'=> $url, 'text'=>$title);
		if($desc != '') $array_LinkArray['tip'] = $desc;
		if($class != '') $array_LinkArray['class'] = $class;
		if($id != '') $array_LinkArray['id'] = $id;
		$this->array_Links[] = $array_LinkArray;
	}
	public function addGroup($title, $class = '', $id = '')
	{
		$array_LinkArray = array('type'=>'group', 'text'=>$title);
		if($class != '') $array_LinkArray['class'] = $class;
		if($id != '') $array_LinkArray['id'] = $id;
		$this->array_Links[] = $array_LinkArray;
	}
	public function assemble($class='',$id='')
	{

		$string_HTML = '<ul'.(($class != '')?' class="'.$class.'"':'').
			(($id != '')?' id="'.$id.'"':'').'>';
		foreach($this->array_Links as $linkArray)
		{
			if($linkArray['type'] == 'group')
			{
				$string_HTML .= '<li class="group"><h4'.((isset(
					$linkArray['class']))?' class="'.$linkArray['class'].'"':'')
					.((isset($linkArray['id']))?' id="'.$linkArray['id'].'"':'')
					.'>'.$linkArray['text'].'</h4></li>';
			}
			elseif($linkArray['type'] == 'link')
			{
				$string_HTML .= '<li class="link"><a href="'.$linkArray['href'].
					'" title="'.((isset($linkArray['tip'])) ? $linkArray['tip']:
					$linkArray['title']).'" '.((isset($linkArray['id']))?' id="'
					.$linkArray['id'].'"' : '').((isset($linkArray['class'])) ?
					' class="'.$linkArray['class'].'"':'').'>'.
					$linkArray['text'].'</a></li>';
			}
		}
		$string_HTML .= '</ul>';
		return $string_HTML;
	}
}

class navBar_mysqli extends navBar
{
	public $id,$level;
	private $SQL;

	public function __construct($id,&$link,$level,$preload=FALSE)
	{
		$this->id = $id;
		$this->SQL =& $link;
		$this->level = $level;
		if($preload) {$this->pre_load();}
	}
	public function pre_load()
	{
		$SQL =& $this->SQL;
		$result = $SQL->query(

'SELECT *
FROM `'.DB_TABLE_NAVIGATION.'`
WHERE `group` = \''.$this->id.'\' AND `usr_thresh` <= \''.$this->level.'\'
ORDER BY `position` ASC'

		);
		$array_rows = array();
		while($array = $result->fetch_assoc())
		{
			$array_rows[$array['type']][$array['id']] = $array;
		}
		foreach($array_rows as $k=>$v)
		{
			if($k == 'image')
			{
				if(count($array_rows) > 1)
				{
					$this->addGroup('Image Boards');
				}
			}
			elseif($k == 'text')
			{
				if(count($array_rows) > 1)
				{
					$this->addGroup('Text Boards');
				}
			}
			elseif($k == 'other')
			{
				if(count($array_rows) > 1)
				{
					$this->addGroup('Other');
				}
			}
			foreach($v as $s)
			{
				$this->addLink($s['href'],$s['text'],$s['title'],(($s['class'] != '' && $s['class'] != NULL)?$s['class']:''));
			}


		}

	}
}
?>