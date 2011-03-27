<?php

App::import('Helper', 'Html');

class UserfilesHelper extends HtmlHelper {
	function image($item, $options = array()) {
		$item = reset($item);
		return parent::image('../'. $item['dir'].'/'.$item['filename'], $options);
	}
}
?>