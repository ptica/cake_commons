<?php

class CzechHelper extends Helper {

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
		return preg_replace('/(\W[ksvzKSVZOoUuIiA]) (\w)/', '$1&nbsp;$2', $text);
	}
}
?>