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
 * Form Validation Class
 *
 * 	This class will be used to validate user input throughout the site.  It
 * 'should' simplify things later on.
 *
 * 	It should be noted that this validator is tailored specifically for this
 * project, and is not likely to be usefull anywhere else.
 *
 */
class FORM {

	private $string;
	private $altered;
	public $error = FALSE;
	public $invalid = FALSE;
	public $reasons = array();
	public $legal_chars = '/[^a-z0-9\v\-?`:;&~_!\-=+\\@#$%\^ *.	,<>\/()[\]]/i';

	function __construct() {}

	/**
	 * Text Entry Validation
	 *
	 * 	Takes the given string, and verifies there are no 'Illegal' or problem
	 * causing characters.
	 *
	 * @param string $input
	 * @param int $minln
	 * @param int $maxln
	 * @return bool
	 */

	public function length($string,$min=4,$max=32) {
		$string = strlen($string);
		if($string > $max || $string < $min) {

			$this->reasons[] = 'Invalid length, must be between '
			.$min.' and '.$max.' characters.';
			return FALSE;

		} else {
			return TRUE;
		}

	}
	public function validate_text($input,$minln=4,$maxln=32) {

		$res = preg_match_all($this->legal_chars,$input,$matches);

		if($res == FALSE) {

			$this->error = preg_last_error().' Preg_Match error.';

		} elseif($res != 0) {

			$invalid = TRUE;
			$this->reasons[] = 'Illegal character.';

		}

		$invalid = (!$this->length($input,$minln,$maxln)) ? TRUE : FALSE;


		if($invalid === TRUE) {

			$this->invalid = TRUE;
			return FALSE;

		} else {

			return TRUE;

		}

	}

	/**
	 * Input Scrubber
	 *
	 * 	'Cleans' the input to make it safe for storing and displaying.
	 *
	 * @param string $input
	 * @return string
	 */
	public function scrub_text($input) {

		$input = htmlentities($input, ENT_QUOTES);

		$input = preg_replace($this->legal_chars,'',$input);

		return $input;
	}
	public function validate_name(){}
	public function validate_email($email){

		if(!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {

			return false;

		}

		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);

		for($i = 0; $i < sizeof($local_array); $i++) {

			if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",$local_array[$i])) {

				return false;

			}
		}

		if(!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {

			$domain_array = explode(".", $email_array[1]);

			if(sizeof($domain_array) < 2) {

				return false;

			}

			for($i = 0; $i < sizeof($domain_array); $i++) {

				if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])| ([A-Za-z0-9]+))$",$domain_array[$i])) {

					return false;

				}
			}
		}

		return true;
	}
	// TODO: Make this more 'all purpose'
	public function file_check(&$upload,$allowedTypes = FALSE,&$errmes = ''){

		$output = TRUE;

		if($upload['error'] == UPLOAD_ERR_OK) {
			if($allowedTypes == TRUE && !in_array($upload['type'],$allowedTypes)) {

				$errmes = 'Invalid File Type, please choose another file and try again.';
				return FALSE;

			} else {

				return TRUE;

			}

		} elseif($upload['error'] == UPLOAD_ERR_NO_FILE){
			return TRUE;
		}

		switch($upload['error']) {

			// File is too large
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$errmes = 'File too big, try resizing it or uploading something else.';
				break;

				// Partial file upload
			case UPLOAD_ERR_PARTIAL:
				$errmes = 'File errored while uploading, please try again.';
				break;

				// Missing a temporary folder
			case UPLOAD_ERR_NO_TMP_DIR:
				$errmes = 'Server side upload error. Error reported to admins, please check back later.';
				ERROR::report('User upload failed, server said NO TEMP DIR.');
				break;

				// Failed to write file to disk
			case UPLOAD_ERR_CANT_WRITE:
				$errmes = 'Server side upload error. Error reported to admins, please check back later.';
				ERROR::report('User upload failed, server said Failed to write to disk.');
				break;

				// File upload stopped by extension
			case UPLOAD_ERR_EXTENSION:
				$errmes = 'Server does not permit uploading files of the specified type. Error reported to admins, please choose another file or check back later.';
				ERROR::report('User:'.$_SERVER['REMOTE_ADDR'].' tried to upload a restricted file type ('.$upload['type'].').');
				break;

			default:
				break;
		}

		return FALSE;
	}

}

class newForm {

	public	$parts=array();
	private $html,$fieldOpen = FALSE;

public function __construct($action='#',$method='post',$enctype=FALSE,$name=FALSE,$id=FALSE,$class=FALSE,$accept=FALSE,$charset=FALSE)
{
	$html  = '<form action="'.$action.'" method="'.$method.'"';
	$html .= ($name != FALSE) ? ' name="'.$name.'"' : '';
	$html .= ($id != FALSE) ? ' id="'.$id.'"' : '';
	$html .= ($class != FALSE) ? ' class="'.$class.'"' : '';
	$html .= ($accept != FALSE) ? ' accept="'.$accept.'"' : '';
	$html .= ($charset != FALSE) ? ' accept-charset="'.$charset.'"' : '';
	$html .= ">\n\n";
	$this->html = $html;
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
	$return		= FALSE
)
{
	$html  = '<div>';

	$id = ($id === FALSE) ? $name : $id;

	$html .= ($label !== FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';

	$html .= '<input type="text" name="'.$name.'"';

	$html .= ($value		!== FALSE)	? ' value="'.htmlentities($value).'"' : '';
	$html .= ($id			!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class		!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($maxlength	!== FALSE)	? ' maxlength="'.$maxlength.'"' : '';
	$html .= ($disabled		=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($readonly 	=== TRUE)	? ' readonly="readonly"' : '';
	$html .= ($size 		!== FALSE)	? ' size="'.$size.'"' : '';
	$html .= ' /></div>';

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
	$return		= FALSE)
{
	$html  = '<div>';
	$id = ($id === FALSE) ? $name : $id;
	$html .= ($label != FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';
	$html .= '<input type="password" name="'.$name.'"';
	$html .= ($value		!== FALSE)	? ' value="'.htmlentities($value).'"' : '';
	$html .= ($id			!== FALSE)	? ' id="'.$id.'"' : '';
	$html .= ($class		!== FALSE)	? ' class="'.$class.'"' : '';
	$html .= ($maxlength	!== FALSE)	? ' maxlength="'.$maxlength.'"' : '';
	$html .= ($disabled		=== TRUE)	? ' disabled="disabled"' : '';
	$html .= ($readonly 	=== TRUE)	? ' readonly="readonly"' : '';
	$html .= ($size 		!== FALSE)	? ' size="'.$size.'"' : '';
	$html .= ' /></div>';
		$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}
public function inputCheckbox($name,$value,$label=FALSE,$id=FALSE,$class=FALSE,$checked=FALSE,$disabled=FALSE,$return=FALSE)
{
	$html  = '<div>';
	$id = ($id === FALSE) ? $name : $id;
	$html .= ($label != FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';
	$html .= '<input type="checkbox" name="'.$name.'"';
	$html .= ($id != FALSE) ? ' id="'.$id.'"' : '';
	$html .= ' value="'.htmlentities($value).'"';
	$html .= ($class != FALSE) ? ' class="'.$class.'"' : '';
	$html .= ($checked === TRUE) ? ' checked="'.$checked.'"' : '';
	$html .= ($disabled === TRUE) ? ' disabled="disabled"' : '';
	$html .= ' /></div>';
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}
public function inputRadio($name,$value,$label=FALSE,$id=FALSE,$class=FALSE,$checked=FALSE,$disabled=FALSE,$return=FALSE)
{
	$html  = '<div>';
	$id = ($id === FALSE) ? $name : $id;
	$html .= ($label != FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';
	$html .= '<input type="radio" name="'.$name.'"';
	$html .= ($id != FALSE) ? ' id="'.$id.'"' : '';
	$html .= ' value="'.htmlentities($value).'"';
	$html .= ($class != FALSE) ? ' class="'.$class.'"' : '';
	$html .= ($checked == FALSE) ? ' checked="'.$checked.'"' : '';
	$html .= ($disabled === TRUE) ? ' disabled="disabled"' : '';
	$html .= ' /></div>';
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}
public function inputSubmit($value='Submit',$name=FALSE,$label=FALSE,$id=FALSE,$class=FALSE,$disabled=FALSE,$return=FALSE)
{
	$html  = '<div>';
	$id = ($id === FALSE) ? $name : $id;
	$html .= ($label != FALSE) ? '<label for='.$id.'">'.$label.'</label>' : '';
	$html .= '<input type="submit" value="'.$value.'"';
	$html .= ($name != FALSE) ? ' name="'.$name.'"' : '';
	$html .= ($id != FALSE) ? ' id="'.$id.'"' : '';
	$html .= ($class != FALSE) ? ' class="'.$class.'"' : '';
	$html .= ($disabled === TRUE) ? ' disabled="disabled"' : '';
	$html .= ' /></div>';
	$this->html .= $html;
	$parts[] = $html;
	if($return) {return $html;}
}
public function inputHTML($html) {
	$this->html .= $html;
	$parts[] = $html;
}
public function divLast($id=FALSE,$class=FALSE,$return=FALSE)
{
	$html = '<div';
	$html .= ($id != FALSE) ? ' id="'.$id.'"' : '';
	$html .= ($class != FALSE) ? ' class="'.$class.'"' : '';
	$html .= '>';

	$this->html .= $html.end($parts[]).'</div>';
	$parts[] = $html.end($parts[]).'</div>';
	if($return) {return $html.end($parts[]).'</div>';}
}
public function labelBegin($text=FALSE,$for=FALSE,$id=FALSE,$class=FALSE,$return=FALSE)
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
public function labelEnd($text=FALSE,$return=FALSE)
{
	$html .= ($text != FALSE) ? $text : '';
	$html = '</label>';
}
public function fieldStart($legend=FALSE,$id=FALSE,$class=FALSE,$return=FALSE)
{
	$html = ($this->fieldOpen) ? '</fieldset>' : '';
	$html .= '<fieldset>';
	$html .= ($legend != FALSE) ? '<legend>'.$legend.'</legend>' : '';

	$this->html .= $html;
	$parts[] = $html;
	$this->fieldOpen = TRUE;
	if($return) {return $html;}
}
public function fieldEnd($return=FALSE)
{
	$html = '</fieldset>';
	$this->fieldOpen = FALSE;
}
public function formReturn() {
	$html = ($this->fieldOpen) ? '</fieldset></form>' : '</form>';
	return $this->html.$html;
}
//EOC
}

?>