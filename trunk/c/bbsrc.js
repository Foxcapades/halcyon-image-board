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
function post_quote(id) {

	var textarea = document.getElementById("text");
	var text = document.getElementById(id).innerHTML;

	text = text.replace(/&lt\;/ig,'<');
	
	
	text = text.replace(/<blockquote class="quote">/gi, '[quote]');
	text = text.replace(/<\/blockquote>/gi, '[/quote]')

	text = text.replace(/<div class="quote"><span><a href="(.+?)" title=(.+?)>(.+?)<\/a><\/span>(.+?)<\/div>/gi,"[quote=[url=$1]$2[/url]]$4[/quote]");
	text = text.replace(/<div class="quote"><span>(.+?)<\/span>(.+?)<\/div>/gi,"[quote=$1]$2[/quote]");
	text = text.replace(/<div class="quote">/gi,"[quote]$1[/quote]");
	text = text.replace(/<a href="(.+?)" title="(.+?)">(.+?)<\/a>/ig,"[url=$1]$3[/url]");
	text = text.replace(/\n|\r/,'');
	text = text.replace(/&gt\;/ig,'>');
	text = text.replace(/&amp\;/ig,'&');
	text = text.replace(/&npsp\;/ig,' ');
	text = text.replace(/&quot\;/ig,'"');
	var quote = "[quote]" + text.replace(/<br(\s?)(\/?)>/gim,"\n") + "[/quote]";
	
	textarea.value = textarea.value + quote;


}