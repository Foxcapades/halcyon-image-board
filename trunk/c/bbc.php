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
	if(count($found[0])) {$topn++;}
	preg_match_all($bot,$this->string,$fund);
	if(count($fund[0])) {$botn++;}


	$match = array(
		$top,
		$bot
	);

	$top = (isset($found[2][0])) ? '<span>'.$found[2][0]."</span>\n" : '';

	$replace = array(
		'<div class="quote">'.$top,
		"</div>\n"
	);

	$string ='';

	$dif = ($topn-$botn);

	for(; $dif < 0; $dif++) {
		$string .= '[quote]';
	}

	$string .= $this->string;

	for(; $dif > 0; $dif--) {
		$string .= '[/quote]';
	}

	$this->string = preg_replace($match,$replace,$string);

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