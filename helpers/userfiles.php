<?php

class UserfilesHelper extends AppHelper {
	var $helpers = array('Html');
	
	/*  
	 * return html code for uploaded userfiles images
	 */
	function image($item, $options = array()) {
		$item = reset($item);
		return $this->Html->image('../'. $item['dir'].'/'.$item['filename'], $options);
	}
}
