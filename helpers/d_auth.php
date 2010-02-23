<?php
/*
 * PHP versions 4 and 5
 *
 * dAuth: A secure authentication system for the cakePHP framework.
 * Copyright (c)	2006, Dieter Plaetinck
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author			Dieter Plaetinck
 * @copyright		Copyright (c) 2006, Dieter Plaetinck
 * @version			0.3
 * @modifiedby		Dieter@be
 * @lastmodified	$Date: 2007/12/28 21:16:27 $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class dAuthHelper extends Helper
{
	var $helpers = array('html','form');
	var $noClearTextErrorId = 'impossible_login_error';
	var $noClearTextErrorMessage = ' impossible.  For security reasons, you should enable javascript.';
	var $noClearTextFormId = 'not_working_form';

	function loadJs() {
		return $this->output($this->html->script('sha1').$this->html->script('d_auth'));
	}
	
	function formHeader($action,$formAction,$cleartext) {
		$output ='';
		if($action && $formAction) {
			if ($cleartext) {
				$output = "<form action='$formAction' method='post' id='login_form'>";
			} else {
				$output = "<p class='error_message' id='$this->noClearTextErrorId'>$action $this->noClearTextErrorMessage</p>";
				$output .= "<form id='$this->noClearTextFormId' style='display:none'>";
				$output .= $this->html->scriptBlock("removeError('$this->noClearTextErrorId');fixForm('$this->noClearTextFormId','$formAction');");
			}
		}
		return $this->output($output);
	}

	function errorMsg($action,$error) {
		$output = '';
		if(!$action) {
			$action = 'Action';
		}
		if ($error) {
			$output = "<p class='error_message'>$error</p>";
		}
		return $this->output($output);
	}

	function emptyField($id = null) {
		$output ='';
		if ($id) {
		    $output = $this->html->scriptBlock("emptyField('$id');");
		}
		return $this->output($output);
	}

	function formInput($name,$type) {
		$output ='';
		if($name && $type)
		{
			$output = "<label for='".low($name)."' class='label'>jméno:</label><br/>";
			$output .= $this->form->input($type, array('style'=>'width:148px', 'div'=> false, 'label'=>false, 'size'=>20, 'class'=>'TextField', 'id'=>low($name)));
			$output .= $this->form->error($type, 'Vyplňte vaše uživatelské jméno.').'<br/>';
		}
		return $this->output($output);
	}

	function formPassword($name,$type)
	{
		$output ='';
		if($name && $type)
		{
			$output = "<label for='".low($name)."' class='label'>heslo:</label><br/>";
			$output .= $this->form->password($type, array('style'=>'width:148px', 'size' => 20, 'class' => 'TextField', 'id'=>low($name)));
			$output .= $this->form->error($type, 'Please enter your '.low($name)).'<br/>';
		}
		return $this->output($output);
	}

	function hiddenField($name,$type,$value)
	{
		$output ='';
		if($name && $type)
		{
			$output = $this->form->input($type, array('type' => 'hidden', 'id'=>low($name), 'value' => $value)).'<br/>';
		}
		return $this->output($output);
	}
	function submit($name = null,$stage2 = true)
	{
		if(!$name) {
			$name = 'Submit';
		}
		$onclick ='';
		if($stage2) {
			$onClick = 'Javascript:return doStage2();';
		} else {
			$onClick = 'Javascript:return doStage1();';
		}
		//PTICA: I am willingly disabling the hashing, its causing problems
		$onClick = ''; 

		$output = $this->form->submit($name, array('class'=>'Button', 'onclick'=>$onClick));

		return $this->output($output);
	}
}
?>