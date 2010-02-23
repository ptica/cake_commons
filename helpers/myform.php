<?php

App::import('Helper', 'Form');

class MyformHelper extends FormHelper {
	function __construct() {
	    parent::__construct();
	}

	function submit($caption = null, $options = array()) {
		$or = '';
		if (isset($options['or'])) {
			$orOptions = $options['or'];
			unset($options['or']);
		}
		if (isset($orOptions) && is_array($orOptions)) {
			$or = '<span class="button_or">nebo ' . $this->Html->link($orOptions['title'], $orOptions['url']) . '</span>';
			if (isset($options['after'])) {
				$options['after'] =  "$or";
			} else {
				$options['after'] =  $or;
			}
		}
		
		$out = parent::submit($caption, $options);
		return $out;
	}
	
	function hidden($fieldName, $options = array()) {
		$out = parent::hidden($fieldName, $options);
		return "<div>$out</div>\n";
	}
	
	/* if sanitized string is used in a form, than every amp is recursively growing (&quote; -> &amp;quote; -> ... */
	function unescape_sanitized_html($string) {
		$replacements = array("&", "%", "<", ">", '"', "'", "(", ")", "+", "-");
		$patterns = array("/\&amp;/", "/\&#37;/", "/\&lt;/", "/\&gt;/", "/\&quot;/", "/\&#39;/", "/\&#40;/", "/\&#41;/", "/\&#43;/", "/\&#45;/");
		$string = preg_replace($patterns, $replacements, $string);
		return $string;
	}
}
?>