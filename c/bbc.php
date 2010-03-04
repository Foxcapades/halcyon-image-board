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

class BBCode {

private $string;
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Main Function

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

public function parse($string)
{
	$this->string = $string;
	// Take care of the simple tags like [b] and [i]
	$this->format();

	// Deal with the [quote] tags
	$this->quote();

	// Handle the [url] tags
	$this->url();

	return $this->string;

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Code Box

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function code()
{

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * General Format

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function format()
{
	$find = array(
		'/\[b\](.+?)\[\/b\]/is',//Bold
		'/\[i\](.+?)\[\/i\]/is',//Italic
		'/\[u\](.+?)\[\/u\]/is',//Underline
		'/\[s\](.+?)\[\/s\]/is',//Strikethrough
		'/\[cd\](.+?)\[\/cd\]/is'//Inline Code
	);
	$replace = array(
		'<span class="bold">\1</span>',//Bold
		'<span class="italic">\1</span>',//Italic
		'<span class="underline">\1</span>',//Underline
		'<span class="strike">\1</span>',//Strikeththrough
		'<span class="cd">\1</span>'//Inline Code
	);

	$this->string = preg_replace($find,$replace,$this->string);

	return TRUE;
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Embedded images

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function image() {

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Ordered List

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function orderedList() {

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Quote Box

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function quote()
{

	$top = '/\[quote((?:={1})(?:&quot;|"|&#039;|\')?(.+?)(?:&quot;|"|&#039;|\')?)?\]/is';
	$bot = '/\[\/quote\]/is';
	$topn = $botn = 0;
	preg_match_all($top,$this->string,$found);
	if(count($found[0])) {$topn=count($found[0]);}
	preg_match_all($bot,$this->string,$fund);
	if(count($fund[0])) {$botn=count($fund[0]);}


	$match = array(
		$top,
		$bot
	);

	$top = (isset($found[2][0]) && $found[2][0] != '') ? '<span>'.$found[2][0]."</span>\n" : '';

	$replace = array(
		'<blockquote class="quote">'.$top,
		"</blockquote>"
	);

	$string ='';

	$dif = ($topn-$botn);

//	if(abs($dif) == 1 && ($topn + $botn) == 1) {return TRUE;}

	$limit = ($topn > $botn) ? $botn : '-1';
	$limit = ($botn > $topn) ? $topn : $limit;
/*	for(; $dif < 0; $dif++) {
		$string .= '[quote]';
	}

	*/$string .= $this->string;/*

	for(; $dif > 0; $dif--) {
		$string .= '[/quote]';
	}*/

	$this->string = preg_replace($match,$replace,$string,$limit);

	return TRUE;
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Unordered List

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function unorderedList () {

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Url

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function url() {

	$match = '/\[url((?:={1})(?:&quot;|"|\'|&#039;)?(.+?)(?:&quot;|"|\'|&#039;)?)?\](.+?)\[\/url\]/is';
	preg_match_all($match,$this->string,$matches);
	foreach($matches[0] as $k => $a) {
		if($a == '' || strlen($a) <= 8) {
			unset($matches[0][$k],$matches[1][$k],$matches[2][$k],$matches[3][$k]);
		}
	}
	$htp = array('http://','https://','ftp://','irc://');
	foreach($matches[2] as $t => $v) {
		if($v=='' || strlen($v) < 5) {
			if($matches[3][$t] == '' || strlen($matches[3][$t]) < 5) {
				//what?
				unset($matches[0][$t],$matches[1][$t],$matches[2][$t],$matches[3][$t]);
			} else {
				$matches[1][$t]= '<a href="'.$matches[3][$t].'" class="link">'.$matches[3][$t].'</a>';
			}
		} else {
			$matches[1][$t]= '<a href="'.$matches[2][$t].'" class="link">'.$matches[3][$t].'</a>';
		}
	}

	$this->string = str_replace($matches[0],$matches[1],$this->string);

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

 * Background functions

* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

private function quoteHeader($str) {
	if(strlen($str) > 0) {
		return '<span>'.$str.'</span>';
	}
	return '';
}
//EOC
}
$BBC = new BBCode();
?>