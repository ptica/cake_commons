<?php

class CzechHelper extends AppHelper {

	var $nouns = array(
		'příspěvek' => array('příspěvek', 'příspěvky', 'příspěvků'),
		'téma' => array('téma', 'témata', 'témat')
	);
	
	function pluralize($lemma, $number) {
		if ($number == 0) {
			$number = 2;
		} elseif ($number == 1) {
			$number = 0;
		} elseif ($number < 5) {
			$number = 1;
		} else {
			$number = 2;
		}
		return $this->nouns[$lemma][$number];
	}
	
	function short_preps_nbsp($text) {
		// negative lookbehind to avoid matching in tags like <a href=
		return preg_replace('/(?!<[^>]*)(\W[ksvzKSVZOoUuIiAa]) (\w)/', '$1&nbsp;$2', $text);
	}
	function sp_to_nbsp($text) {
		return preg_replace('/(\w) (\w)/', '$1&nbsp;$2', $text);
	}
	
	function slashed_nice_numbers($text) {
		//$parts = explode('/', $text);
		$parts = preg_split('/\/(?!\w)/', $text); # prevent split before and tags
		$res = array();
		foreach ($parts as $part) $res[] = $this->nice_numbers($part);
		return join(' / ', $res);
	}
	
	function nice_numbers($num, $bold=false) {
		if (!preg_match('/\d/', $num)) return '<span class="no-numbers">'.$num.'</span>';
		
		// just if something lower will match
		if (preg_match('/\d\d00\b/', $num)) $num = preg_replace('/\b(\d+)\b/', '<span class="anchor">\1</span>', $num);
		
		if (preg_match('/\d000000\b/', $num)) {
			$num = preg_replace('/000000\b/', '<span class="magnitude">milion</span>', $num);
		}
		if (preg_match('/\d\d00000\b/', $num)) {
			$num = preg_replace('/(\d)00000\b/', '.\1<span class="magnitude">milion</span>', $num);
		}
		if (preg_match('/\d000\b/', $num)) {
			$num = preg_replace('/000\b/', '<span class="magnitude">tisíc</span>', $num);
		} else {
			if (preg_match('/\d\d00\b/', $num)) {
				$num = preg_replace('/(\d)00\b/', '.\1<span class="magnitude">tisíc</span>', $num);
			}
		}
		
		if ($bold) $num = preg_replace('/\b(\d+)\b/', '<b>\1</b>', $num);
		
		return $num;
	}
}
?>