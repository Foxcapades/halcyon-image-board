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
class newForm {

public	$parts=array();
private
	$html,
	$fieldOpen = FALSE;

public function __construct
(
	$action		= '#',
	$method		= 'post',
	$enctype	= FALSE,
	$name		= FALSE,
	$id			= FALSE,
	$class		= FALSE,
	$accept		= FALSE,
	$charset	= FALSE
)
{
	$html  = '<form action="'.$action.'" method="'.$method.'"';
	$html .= ($enctype	!== FALSE) ? ' enctype="'.$enctype.'"' : '';
	$html .= ($name		!== FALSE) ? ' name="'.$name.'"' : '';
	$html .= ($id		!== FALSE) ? ' id="'.$id.'"' : '';
	$html .= ($class	!== FALSE) ? ' class="'.$class.'"' : '';
	$html .= ($accept	!== FALSE) ? ' accept="'.$accept.'"' : '';
	$html .= ($charset	!== FALSE) ? ' accept-charset="'.$charset.'"' : '';
	$html .= ">\n\n";

	$this->html = $html;
}

public function divLast
(
	$id		= FALSE,
	$class	= FALSE,
	$return	= FALSE
)
{
	$html = '<div';
	$html .= ($id != FALSE) ? ' id="'.$id.'"' : '';
	$html .= ($class != FALSE) ? ' class="'.$class.'"' : '';
	$html .= '>';

	$this->html .= $html.end($parts[]).'</div>';
	$parts[] = $html.end($parts[]).'</div>';
	if($return) {return $html.end($parts[]).'</div>';}
}

public function inputButton
(
	$name,
	$value,
	$label		=FALSE,
	$id			=FALSE,
	$class		=FALSE,
	$checked	=FALSE,
	$disabled	=FALSE,
	$extra		=FALSE,
	$return		=FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label	!== FALSE)	? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="checkbox" name="'.$name.'"';
	$html .= ' value="'.htmlentities($value).'"';
	$html .= ($id		!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($checked	=== TRUE)	? ' checked="checked"' : '';
	$html .= ($disabled	=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($extra	!== FALSE)	? ' '.$extra : '';
	$html .= " /></div>\n\n";
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputCheckbox
(
	$name,
	$value,
	$label		= FALSE,
	$id			= FALSE,
	$class		= FALSE,
	$checked	= FALSE,
	$disabled	= FALSE,
	$extra		= FALSE,
	$return		= FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label	!== FALSE)	? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="checkbox" name="'.$name.'"';
	$html .= ' value="'.htmlentities($value).'"';
	$html .= ($id		!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($checked	=== TRUE)	? ' checked="checked"' : '';
	$html .= ($disabled === TRUE)	? ' disabled="disabled"' : '';
	$html .= ($extra	!== FALSE)	? ' '.$extra : '';
	$html .= " /></div>\n\n";
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputFile
(
	$name,
	$label		= FALSE,
	$id			= FALSE,
	$class		= FALSE,
	$accept		= FALSE,
	$disabled	= FALSE,
	$size		= FALSE,
	$extra		= FALSE,
	$return		= FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label !== FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="file" name="'.$name.'"';

	$html .= ($id		!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($accept	!== FALSE)	? ' accept="'.$accept.'"' : '';
	$html .= ($disabled	=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($size 	!== FALSE)	? ' size="'.$size.'"' : '';
	$html .= ($extra	!== FALSE)	? ' '.$extra : '';
	$html .= " /></div>\n\n";

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputHidden
(
	$name,
	$value,
	$id			= FALSE,
	$class		= FALSE,
	$extra		= FALSE,
	$return		= FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html .= '<input type="hidden" name="'.$name.'" value="'.htmlentities($value).'"';
	$html .= ($id			!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class		!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($extra		!== FALSE)	? ' '.$extra : '';
	$html .= " />\n\n";

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputPassword
(
	$name,
	$label		= FALSE,
	$value		= FALSE,
	$id			= FALSE,
	$class		= FALSE,
	$maxlength	= FALSE,
	$disabled	= FALSE,
	$readonly	= FALSE,
	$size		= FALSE,
	$extra		= FALSE,
	$return		= FALSE)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label != FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="password" name="'.$name.'"';
	$html .= ($value		!== FALSE)	? ' value="'.htmlentities($value).'"' : '';
	$html .= ($id			!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class		!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($maxlength	!== FALSE)	? ' maxlength="'.$maxlength.'"' : '';
	$html .= ($disabled		=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($readonly 	=== TRUE)	? ' readonly="readonly"' : '';
	$html .= ($size 		!== FALSE)	? ' size="'.$size.'"' : '';
	$html .= ($extra		!== FALSE)	? ' '.$extra : '';
	$html .= " /></div>\n\n";
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputRadio
(
	$name,
	$value,
	$label		= FALSE,
	$id			= FALSE,
	$class		= FALSE,
	$checked	= FALSE,
	$disabled	= FALSE,
	$extra		= FALSE,
	$return		= FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label 	!= FALSE)	? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="radio" name="'.$name.'"';
	$html .= ' value="'.htmlentities($value).'"';
	$html .= ($id		!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($checked	=== TRUE)	? ' checked="checked"' : '';
	$html .= ($disabled === TRUE)	? ' disabled="disabled"' : '';
	$html .= ($extra	!== FALSE)	? ' '.$extra : '';
	$html .= " /></div>\n\n";
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputSubmit
(
	$value		='Submit',
	$name		= FALSE,
	$label		= FALSE,
	$id			= FALSE,
	$class		= FALSE,
	$disabled	= FALSE,
	$extra		= FALSE,
	$return		= FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label	!== FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="submit" value="'.$value.'"';
	$html .= ($name		!== FALSE) ? ' name="'.$name.'"' : '';
	$html .= ($id		!== FALSE) ? ' id="'.$id.'"' : '';
	$html .= ($class	!== FALSE) ? ' class="'.$class.'"' : '';
	$html .= ($disabled === TRUE) ? ' disabled="disabled"' : '';
	$html .= ($extra	!== FALSE)	? ' '.$extra : '';
	$html .= " /></div>\n\n";
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputText
(
	$name,
	$label		= FALSE,
	$value		= FALSE,
	$id			= FALSE,
	$class		= FALSE,
	$maxlength	= FALSE,
	$disabled	= FALSE,
	$readonly	= FALSE,
	$size		= FALSE,
	$extra		= FALSE,
	$return		= FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label !== FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="text" name="'.$name.'"';

	$html .= ($value		!== FALSE)	? ' value="'.htmlentities($value).'"' : '';
	$html .= ($id			!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class		!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($maxlength	!== FALSE)	? ' maxlength="'.$maxlength.'"' : '';
	$html .= ($disabled		=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($readonly 	=== TRUE)	? ' readonly="readonly"' : '';
	$html .= ($size 		!== FALSE)	? ' size="'.$size.'"' : '';
	$html .= ($extra		!== FALSE)	? ' '.$extra : '';
	$html .= " /></div>\n\n";

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputTextarea
(
	$name,
	$label		= FALSE,
	$value		= FALSE,
	$cols		= 20,
	$rows		= 4,
	$id			= FALSE,
	$class		= FALSE,
	$disabled	= FALSE,
	$readonly	= FALSE,
	$extra		= FALSE,
	$return		= FALSE
)
{
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label !== FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<textarea name="'.$name.'"';
	$html .= ' cols="'.$cols.'"';
	$html .= ' rows="'.$rows.'"';

	$html .= ($id		!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($disabled	=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($readonly	=== TRUE)	? ' readonly="readonly"' : '';
	$html .= ($extra	!== FALSE)	? ' '.$extra : '';
	$html .= '>';
	$html .= ($value	!== FALSE)	? htmlentities($value) : '';
	$html .= "</textarea></div>\n\n";

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function inputHTML($html) {$this->html .= $html; $parts[] = $html;}

public function labelBegin
(
	$text	= FALSE,
	$for	= FALSE,
	$id		= FALSE,
	$class	= FALSE,
	$return	= FALSE
)
{
	$html = '<label';
	$html .= ($for != FALSE) ? ' for="'.$for.'"' : '';
	$html .= ($id != FALSE) ? ' id="'.$id.'"' : '';
	$html .= ($class != FALSE) ? ' class="'.$class.'"' : '';
	$html .= '>';
	$html .= ($text != FALSE) ? $text : '';

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}

public function labelEnd
(
	$text=FALSE,
	$return=FALSE
)
{
	$html .= ($text != FALSE) ? $text : '';
	$html = '</label>';
}

public function fieldStart
(
	$legend	= FALSE,
	$id		= FALSE,
	$class	= FALSE,
	$return	= FALSE
)
{
	$html = ($this->fieldOpen) ? "</fieldset>\n\n" : '';
	$html .= '<fieldset>';
	$html .= ($legend != FALSE) ? '<legend>'.$legend.'</legend>'."\n" : '';

	$this->html .= $html;
	$parts[] = $html;
	$this->fieldOpen = TRUE;
	if($return) {return $html;}
}

public function fieldEnd($return=FALSE)
{
	$html = "</fieldset>\n\n";
	$this->fieldOpen = FALSE;
}

public function formReturn()
{
	$html = ($this->fieldOpen) ? '</fieldset></form>' : '</form>';
	return $this->html.$html;
}public function returnForm()
{
	$html = ($this->fieldOpen) ? '</fieldset></form>' : '</form>';
	return $this->html.$html;
}

//EOC
}
?>