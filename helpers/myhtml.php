<?php

class MyhtmlHelper extends HtmlHelper {
	var $helpers = array('Number');
	
	var $Number = null;
	
	function __construct() {
	    parent::__construct();
	}
	
	function avatar_for($user_id) {
		return '';	
	}
	function tagErrorMsg($field, $text=null) {
		$error = 1;
		$this->setEntity($field);
		// PTICA works not well with 1.2 validation
		// PTICA div -> span 
		//if ($error == $this->tagIsInvalid()) {
		// very dull here:
		// tagIsInvalid returns 1 or the message assigned during invalidate call
		if ($error = $this->tagIsInvalid()) {
			return sprintf('<span class="error-message">%s</span>', is_array($text) ? (empty($text[$error - 1]) ? 'Error in field' : $text[$error - 1]) : $text ? $text : $error);
		} else {
			return null;
		}
	}
	
	function downloadLink($item) {
		$linkname = '';
		if (isset($item['displayname'])) $linkname = $item['displayname'];
		if (isset($item['inscription'])) $linkname = $item['inscription']; 
		if (isset($item['name'])) $linkname = $item['name'];
		
		$res = '';
		
		if ($item['extension'] == 'PDF') {
			//$res .= $this->link($linkname , 'http://pdfmenot.com/view/'.'http://www.roztoky.cz'.'/'.$item['dir'].'/'.$item['filename']);
			//$res .= $this->link($linkname , 'http://docs.google.com/viewer?embedded=true&url='.'http://www.roztoky.cz'.'/'.$item['dir'].'/'.$item['filename']);
			
			//$res .= $this->link($linkname , "/viewer/".$item['dir'].'/'.$item['filename']);
			$res .= $this->link($linkname , '/'.$item['dir'].'/'.$item['filename']);
		} else {
			$res .= $this->link($linkname , '/'.$item['dir'].'/'.$item['filename']);
		}
		$res .= ' <span class="notice">';
		$res .= '(';
		$res .= $item['extension'].' '.$this->Number->toReadableSize($item['filesize']);
		if ($item['extension'] == 'PDF') {
			//$res .= $this->link($this->image('download.gif', array('title'=>'uložit')), '/'.$item['dir'].'/'.$item['filename'], array('escape' => false));
		}
		$res .= ")</span><br />\n";
		return $res; 
	}
}
?>