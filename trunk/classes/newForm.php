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
 * Form Generation Class
 *
 *	The form generation class "newForm" provides a means to dynamically create
 * HTML forms on pages who's content is not 'set in stone'.  This removes the
 * need for templates, and allows the site to add and remove fields as needed to
 * keep the forms specific and on target.
 *
 */
class newForm {
/**
 * newForm::parts is a public array containing the individual HTML elements
 * contained in the form.
 *
 * @var array
 */
public	$parts=array();
/**
 * newForm::html is a private string containing the unfinished form, all added
 * elements are appended to the string until it is returned to the calling
 * script via $newForm::returnForm();.  This variable is kept private as the
 * string is not finished and may contain several open tags that the class keeps
 * track of and will close when the form's HTML is returned.
 *
 * @var string
 */
private $html;
/**
 * newForm::fieldOpen is a private variable containing a simple boolean TRUE or
 * FALSE.  This tells the class if there is an open fieldset tag that needs to
 * be closed before the HTML is returned.
 *
 * @var boolean
 */
private $fieldOpen = FALSE;
/**
 * newForm::selectOpen is a private variable containing a simple boolean TRUE or
 * FALSE.  This thells the class if there is an open select tag that needs to be
 * clised before the HTML is returned, or another element is added.
 *
 * @var boolean
 */
private $selectOpen = FALSE;
/**
 * newForm::__construct() starts out the newForm class by building the form
 * tag and starting out the HTML for the form.  The parameters follow the basic
 * attributes that are common for the form tag.
 *
 * @param string $action
 * @param string $method
 * @param string $enctype
 * @param string $name
 * @param string $id
 * @param string $class
 * @param string $accept
 * @param string $charset
 * @return unknown_type
 */
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
	$this->parts[] = $html;
}
/**
 * newForm::inputButton() adds a button to the form (input type="button").
 * The parameters follow the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $value
 * @param string $label
 * @param string_type $id
 * @param string_type $class
 * @param boolean $checked
 * @param boolean $disabled
 * @param string $extra
 * @param boolean $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label	!== FALSE)	? '<label for="'.$id.'">'.$label.'</label>' : '';

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
/**
 * newForm::inputCheckbox() adds a checkbox to the form
 * (input type="checkbox"). The parameters follow the common attributes for
 * the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $value
 * @param string $label
 * @param string $id
 * @param string $class
 * @param boolean $checked
 * @param boolean $disabled
 * @param string $extra
 * @param boolean $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label	!== FALSE)	? '<label for="'.$id.'">'.$label.'</label>' : '';

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
/**
 * newForm::inputFile() adds a file input to the form (input type="file"). The
 * parameters follow the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $label
 * @param string $id
 * @param string $class
 * @param string $accept
 * @param boolean $disabled
 * @param string $size
 * @param string $extra
 * @param boolean $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label !== FALSE) ? '<label for="'.$id.'">'.$label.'</label>' : '';

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
/**
 * newForm::inputHidden() adds a hidden input to the form (input type="hidden").
 * The parameters follow the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $value
 * @param string $id
 * @param string $class
 * @param string $extra
 * @param boolean $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html = '<input type="hidden" name="'.$name.'" value="'.htmlentities($value).'"';
	$html .= ($id			!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class		!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($extra		!== FALSE)	? ' '.$extra : '';
	$html .= " />\n\n";

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}
/**
 * newForm::inputPassword() adds a password input to the form
 * (input type="password"). The parameters follow the common attributes for the
 * equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $label
 * @param string $value
 * @param string $id
 * @param string $class
 * @param string $maxlength
 * @param boolean $disabled
 * @param boolean $readonly
 * @param string $size
 * @param string $extra
 * @param boolean $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label != FALSE) ? '<label for="'.$id.'">'.$label.'</label>' : '';

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
/**
 * newForm::inputRadio() adds a radio button to the form (input type="radio").
 * The parameters follow the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $value
 * @param string $label
 * @param string $id
 * @param string $class
 * @param boolean $checked
 * @param boolean $disabled
 * @param string $extra
 * @param boolean $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label 	!= FALSE)	? '<label for="'.$id.'">'.$label.'</label>' : '';

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
/**
 * newForm::inputSelect() starts a select box to the form. The parameters follow
 * the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $label
 * @param boolean $multiple
 * @param string $id
 * @param string $class
 * @param boolean $disabled
 * @param string $size
 * @param string $extra
 * @param boolean $return
 * @return string
 */
public function inputSelect
(
	$name,
	$label		= '',
	$multiple	= FALSE,
	$size		= '',
	$id			= '',
	$class		= '',
	$disabled	= FALSE,
	$extra		= '',
	$return		= FALSE
)
{
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}

	$id = ($id === '') ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== '')		? ' class="'.$class.'"' : '';
	$html .= ($id		!== '')		? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label	!== '')		? '<label for="'.$id.'">'.$label.'</label>' : '';

	$html .= '<select name="'.$name.'"';
	$html .= ($multiple	!== FALSE)	? ' multiple="multiple"' : '';
	$html .= ($id		!== '')		? ' id="'.$id.'"' : '';
	$html .= ($class 	!== '')		? ' class="'.$class.'"' : '';
	$html .= ($disabled === TRUE)	? ' disabled="disabled"' : '';
	$html .= ($size 	!== '')		? ' size="'.$size.'"' : '';
	$html .= ($extra	!== FALSE)	? ' '.$extra : '';
	$html .= ">\n";

	$this->selectOpen = TRUE;
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}

}
/**
 * newForm::addOption() adds an option to an open select element in the form .
 * The parameters follow the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $text
 * @param string $value
 * @param string $selected
 * @param string $label
 * @param string $id
 * @param string $class
 * @param string $extra
 * @param string $return
 * @return string|string
 */
public function addOption
(
	$text,
	$value,
	$selected	= FALSE,
	$label		= '',
	$id			= '',
	$class		= '',
	$extra		= '',
	$return		= FALSE
)
{
	if($this->selectOpen !== TRUE) {return FALSE;}
	$html .= '<option value="'.$value.'"';
	$html .= ($selected	!== FALSE)	? ' selected="selected"' : '';
	$html .= ($label	!== '')		? ' label="'.$label.'"' : '';
	$html .= ($id		!== '')		? ' id="'.$id.'"' : '';
	$html .= ($class 	!== '')		? ' class="'.$class.'"' : '';
	$html .= ($extra	!== '')	? ' '.$extra : '';
	$html .= '>'.$text."</option>\n";

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}
/**
 * newForm::endSelect() closes an open select box in the form. The parameters
 * follow the common attributes for the equivalent HTML tag.
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param boolean $return
 * @return string
 */
public function endSelect($return=FALSE)
{
	if($this->selectOpen !== TRUE) {return FALSE;}
	$html = "</select></div>\n\n";
	$this->html .= $html;
	$this->parts[] = $html;
	$this->selectOpen = FALSE;
}
/**
 * newForm::inputSubmit() adds a submit button to the form. The parameters
 * follow the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $value
 * @param string $name
 * @param string $label
 * @param string $id
 * @param string $class
 * @param boolean $disabled
 * @param string $extra
 * @param string $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label	!== FALSE) ? '<label for="'.$id.'">'.$label.'</label>' : '';

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
/**
 * newForm::inputText() adds a text input to the form. The parameters follow the
 * common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $label
 * @param string $value
 * @param string $id
 * @param string $class
 * @param string $maxlength
 * @param boolean $disabled
 * @param boolean $readonly
 * @param string $size
 * @param string $extra
 * @param boolean $return
 * @return string
 */
public function inputText
(
	$name,
	$label		= '',
	$value		= '',
	$id			= '',
	$class		= '',
	$maxlength	= '',
	$disabled	= FALSE,
	$readonly	= FALSE,
	$size		= '',
	$extra		= '',
	$return		= FALSE
)
{
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === '') ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== '')	? ' class="'.$class.'"' : '';
	$html .= ($id		!== '')	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label !== '') ? '<label for="'.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="text" name="'.$name.'"';

	$html .= ($value		!== '')	? ' value="'.htmlentities($value).'"' : '';
	$html .= ($id			!== '')	? ' id="'.$id.'"' : '';
	$html .= ($class		!== '')	? ' class="'.$class.'"' : '';
	$html .= ($maxlength	!== '')	? ' maxlength="'.$maxlength.'"' : '';
	$html .= ($disabled		=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($readonly 	=== TRUE)	? ' readonly="readonly"' : '';
	$html .= ($size 		!== '')	? ' size="'.$size.'"' : '';
	$html .= ($extra		!== '')	? ' '.$extra : '';
	$html .= " /></div>\n\n";

	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}
/**
 * newForm::inputTextarea() adds a text box to the form. The parameters follow
 * the common attributes for the equivalent HTML tag.
 *
 * The $extra parameter allows scripts or extra attributes to be added into the
 * input as well.  Attributes set in the $extra parameter should be set exactly
 * how you want them to be in the tag (i.e. $extra = 'onclick="dothis()"')
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $name
 * @param string $label
 * @param string $value
 * @param string $cols
 * @param string $rows
 * @param string $id
 * @param string $class
 * @param boolean $disabled
 * @param boolean $readonly
 * @param string $extra
 * @param boolean $return
 * @return string
 */
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
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$id = ($id === FALSE) ? $name : $id;

	$html  = '<div';
	$html .= ($class 	!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($id		!== FALSE)	? ' id="div_'.$id.'"' : '';
	$html  .= '>';

	$html .= ($label !== FALSE) ? '<label for="'.$id.'">'.$label.'</label>' : '';

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
/**
 * newForm::inputHTML() adds HTML directly to the form.  Can be used to inject
 * any HTML, be cautious with how this is used.
 *
 * @param string $html
 * @return string
 */
public function inputHTML($html) {$this->html .= $html; $parts[] = $html;}
/**
 * newForm::labelStart() adds an open label tag to the form. The parameters
 * follow the common attributes for the equivalent HTML tag. Text can be added
 * here and/or with the closing tag.
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $text
 * @param string $for
 * @param string $id
 * @param string $class
 * @param boolean $return
 * @return Ambigous <string, unknown>
 */
public function labelStart
(
	$text	= FALSE,
	$for	= FALSE,
	$id		= FALSE,
	$class	= FALSE,
	$return	= FALSE
)
{
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
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
/**
 * newForm::labelEnd() ends an open label tag. Text can be added here and/or
 * with the opening tag.
 *
 * @param string $text
 * @param boolean $return
 * @return string
 */
public function labelEnd
(
	$text=FALSE,
	$return=FALSE
)
{
	$html .= ($text != FALSE) ? $text : '';
	$html = '</label>';
}
/**
 * newForm::fieldStart() adds an open fieldset tag to the form with an optional
 * legend tag. The parameters follow the common attributes for the equivalent
 * HTML tag.
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param string $legend
 * @param string $id
 * @param string $class
 * @param boolean $return
 * @return string
 */
public function fieldStart
(
	$legend	= FALSE,
	$id		= FALSE,
	$class	= FALSE,
	$return	= FALSE
)
{
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$html = ($this->fieldOpen) ? "</fieldset>\n\n" : '';
	$html .= '<fieldset>';
	$html .= ($legend != FALSE) ? '<legend>'.$legend.'</legend>'."\n" : '';

	$this->html .= $html;
	$parts[] = $html;
	$this->fieldOpen = TRUE;
	if($return) {return $html;}
}
/**
 * newForm::fieldEnd() closes an open fieldset in the form. The parameters
 * follow the common attributes for the equivalent HTML tag.
 *
 * The $return parameter is a simple TRUE or FALSE on whether or not you want
 * the function to return the created HTML to the calling script.
 *
 * @param boolean $return
 * @return string
 */
public function fieldEnd($return=FALSE)
{
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$html = "</fieldset>\n\n";
	$this->html .= $html;
	$this->fieldOpen = FALSE;
}
/**
 * newForm::formReturn() closes the open tags and returns the assembled HTML for
 * form.
 *
 * @return string
 */
public function formReturn()
{
	if($this->selectOpen !== FALSE) {$this->endSelect($return);}
	$html = ($this->fieldOpen) ? '</fieldset></form>' : '</form>';
	return $this->html.$html;
}
//EOC
}
?>