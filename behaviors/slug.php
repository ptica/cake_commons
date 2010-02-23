<?php
/**
 * Slug Behavior class file.
 *
 * Model Behavior to support slugs.
 *
 * @filesource
 * @package	app
 * @subpackage	models.behaviors
 */

/**
 * Add slug behavior to a model.
 *
 * @author	Mariano Iglesias
 * @package	app
 * @subpackage	models.behaviors
 */
class SlugBehavior extends ModelBehavior {
	/**
	 * Initiate behaviour for the model using specified settings.
	 *
	 * @param object $model	Model using the behaviour
	 * @param array $settings	Settings to override for model.
	 *
	 * @access public
	 */
	function setup(&$model, $settings = array()) {
		$default = array( 'label' => array('title'), 'slug' => 'slug', 'separator' => '-', 'length' => 100, 'overwrite' => false );

		if (!isset($this->settings[$model->name])) {
			$this->settings[$model->name] = $default;
		}

		$this->settings[$model->name] = array_merge($this->settings[$model->name], ife(is_array($settings), $settings, array()));
	}

	/**
	 * Run before a model is saved, used to set up slug for model.
	 *
	 * @param object $model	Model about to be saved.
	 *
	 * @access public
	 * @since 1.0
	 */
	function beforeSave(&$model) {
		if (!is_array($this->settings[$model->name]['label'])) {
			$this->settings[$model->name]['label'] = array( $this->settings[$model->name]['label'] );
		}

		foreach($this->settings[$model->name]['label'] as $field) {
			if (!$model->hasField($field)) {
				return;
			}
		}

		if ($model->hasField($this->settings[$model->name]['slug']) && ($this->settings[$model->name]['overwrite'] || empty($model->{$model->primaryKey}))) {
			$label = '';
			
			foreach($this->settings[$model->name]['label'] as $field) {
				$label .= ife(!empty($label), ' ', '');
				$label .= $model->data[$model->name][$field];
			}
				
			if (empty($label)) {
				$label = 'slug';
			}
				
			$slug = $this->_slug($label, $this->settings[$model->name]);
				
			$conditions = array($model->name . '.' . $this->settings[$model->name]['slug'] . ' LIKE' => $slug . '%');
				
			if (!empty($model->{$model->primaryKey})) {
				$conditions[$model->name . '.' . $model->primaryKey] = '!= ' . $model->{$model->primaryKey};
			}
				
			$result = $model->find('all', array(
			   'conditions'=>$conditions,
			   'fields'=>array($model->primaryKey, $this->settings[$model->name]['slug']),
			   'page'=>1,
			   'recursive' => 0,
			));
			$sameUrls = null;
				
			if ($result !== false && !empty($result)) {
				$sameUrls = Set::extract($result, '{n}.' . $model->name . '.' . $this->settings[$model->name]['slug']);
			}

			if (!empty($sameUrls)) {
				$begginingSlug = $slug;
				$index = 1;

				while($index > 0) {
					if (!in_array($begginingSlug . $this->settings[$model->name]['separator'] . $index, $sameUrls)) {
						$slug = $begginingSlug . $this->settings[$model->name]['separator'] . $index;
						$index = -1;
					}
					$index++;
				}
			}
				
			$model->data[$model->name][$this->settings[$model->name]['slug']] = $slug;
		}
	}

	/**
	 * Generate a slug for the given strung using specified settings.
	 *
	 * @param string $string	String.
	 * @param array $settings	Settings to use (looks for 'separator' and 'length')
	 *
	 * @return string	Slug for given string.
	 *
	 * @access private
	 */
	function _slug($string, $settings) {
		$string = $this->friendly_name($string);
		//$string = strtolower($string);

		//$string = preg_replace('/[^a-z0-9_]/i', $settings['separator'], $string);
		//$string = preg_replace('/\\' . $settings['separator'] . '[\\' . $settings['separator'] . ']*/', $settings['separator'], $string);

		if (strlen($string) > $settings['length']) {
			$string = substr($string, 0, $settings['length']);
		}

		$string = preg_replace('/\\' . $settings['separator'] . '$/', '', $string);
		$string = preg_replace('/^\\' . $settings['separator'] . '/', '', $string);

		return $string;
	}

	function xxx_friendly_url($nadpis) {
		$url = $nadpis;
		$url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
		$url = trim($url, "-");
		$url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
		return $url;
	}
	
// skynet hosting screws the iconv code :(
	
/**
     * Czech & Slovak diacritical chars => ASCII by dgx
     * -------------------------------------------------
     *
     * This source file is subject to the GNU GPL license.
     *
     * @author     David Grudl aka -dgx- <dave@dgx.cz>
     * @link       http://www.dgx.cz/
     * @copyright  Copyright (c) 2006 David Grudl
     * @license    GNU GENERAL PUBLIC LICENSE
     * @category   Text
     * @version    1.0
     */ 
	
	
	// UTF-8 to ASCII for diacritic chars
	function utf2ascii($s) {
    	static $tbl = array("\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z");
    	return strtr($s, $tbl);
	}
	
	
	function friendly_name($nadpis) {
		$url = $this->unescape_sanitized_html($nadpis);
		$url = $this->utf8_trans_unaccent($url);
		$url = preg_replace('~\s+~', '-', $url);
		$url = $this->utf2ascii($url);
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
		$url = trim($url, "-");
		return $url;
	}
	
	/* if sanitized string is used in a form, than every amp is recursively growing (&quote; -> &amp;quote; -> ... */
	function unescape_sanitized_html($string) {
		$replacements = array("&", "%", "<", ">", '"', "'", "(", ")", "+", "-");
		$patterns = array("/\&amp;/", "/\&#37;/", "/\&lt;/", "/\&gt;/", "/\&quot;/", "/\&#39;/", "/\&#40;/", "/\&#41;/", "/\&#43;/", "/\&#45;/");
		$string = preg_replace($patterns, $replacements, $string);
		return $string;
	}
	
	function utf8_trans_unaccent($instr) {
   		$tranmap = array(
	      "\xC3\x80" => "A",   "\xC3\x81" => "A",   "\xC3\x82" => "A",   "\xC3\x83" => "A",
	      "\xC3\x84" => "A",   "\xC3\x85" => "A",   "\xC3\x86" => "AE",  "\xC3\x87" => "C",
	      "\xC3\x88" => "E",   "\xC3\x89" => "E",   "\xC3\x8A" => "E",   "\xC3\x8B" => "E",
	      "\xC3\x8C" => "I",   "\xC3\x8D" => "I",   "\xC3\x8E" => "I",   "\xC3\x8F" => "I",
	      "\xC3\x90" => "D",   "\xC3\x91" => "N",   "\xC3\x92" => "O",   "\xC3\x93" => "O",
	      "\xC3\x94" => "O",   "\xC3\x95" => "O",   "\xC3\x96" => "O",   "\xC3\x98" => "O",
	      "\xC3\x99" => "U",   "\xC3\x9A" => "U",   "\xC3\x9B" => "U",   "\xC3\x9C" => "U",
	      "\xC3\x9D" => "Y",   "\xC3\x9E" => "P",   "\xC3\x9F" => "ss",
	      "\xC3\xA0" => "a",   "\xC3\xA1" => "a",   "\xC3\xA2" => "a",   "\xC3\xA3" => "a",
	      "\xC3\xA4" => "a",   "\xC3\xA5" => "a",   "\xC3\xA6" => "ae",  "\xC3\xA7" => "c",
	      "\xC3\xA8" => "e",   "\xC3\xA9" => "e",   "\xC3\xAA" => "e",   "\xC3\xAB" => "e",
	      "\xC3\xAC" => "i",   "\xC3\xAD" => "i",   "\xC3\xAE" => "i",   "\xC3\xAF" => "i",
	      "\xC3\xB0" => "o",   "\xC3\xB1" => "n",   "\xC3\xB2" => "o",   "\xC3\xB3" => "o",
	      "\xC3\xB4" => "o",   "\xC3\xB5" => "o",   "\xC3\xB6" => "o",   "\xC3\xB8" => "o",
	      "\xC3\xB9" => "u",   "\xC3\xBA" => "u",   "\xC3\xBB" => "u",   "\xC3\xBC" => "u",
	      "\xC3\xBD" => "y",   "\xC3\xBE" => "p",   "\xC3\xBF" => "y",
	      "\xC4\x80" => "A",   "\xC4\x81" => "a",   "\xC4\x82" => "A",   "\xC4\x83" => "a",
	      "\xC4\x84" => "A",   "\xC4\x85" => "a",   "\xC4\x86" => "C",   "\xC4\x87" => "c",
	      "\xC4\x88" => "C",   "\xC4\x89" => "c",   "\xC4\x8A" => "C",   "\xC4\x8B" => "c",
	      "\xC4\x8C" => "C",   "\xC4\x8D" => "c",   "\xC4\x8E" => "D",   "\xC4\x8F" => "d",
	      "\xC4\x90" => "D",   "\xC4\x91" => "d",   "\xC4\x92" => "E",   "\xC4\x93" => "e",
	      "\xC4\x94" => "E",   "\xC4\x95" => "e",   "\xC4\x96" => "E",   "\xC4\x97" => "e",
	      "\xC4\x98" => "E",   "\xC4\x99" => "e",   "\xC4\x9A" => "E",   "\xC4\x9B" => "e",
	      "\xC4\x9C" => "G",   "\xC4\x9D" => "g",   "\xC4\x9E" => "G",   "\xC4\x9F" => "g",
	      "\xC4\xA0" => "G",   "\xC4\xA1" => "g",   "\xC4\xA2" => "G",   "\xC4\xA3" => "g",
	      "\xC4\xA4" => "H",   "\xC4\xA5" => "h",   "\xC4\xA6" => "H",   "\xC4\xA7" => "h",
	      "\xC4\xA8" => "I",   "\xC4\xA9" => "i",   "\xC4\xAA" => "I",   "\xC4\xAB" => "i",
	      "\xC4\xAC" => "I",   "\xC4\xAD" => "i",   "\xC4\xAE" => "I",   "\xC4\xAF" => "i",
	      "\xC4\xB0" => "I",   "\xC4\xB1" => "i",   "\xC4\xB2" => "IJ",  "\xC4\xB3" => "ij",
	      "\xC4\xB4" => "J",   "\xC4\xB5" => "j",   "\xC4\xB6" => "K",   "\xC4\xB7" => "k",
	      "\xC4\xB8" => "k",   "\xC4\xB9" => "L",   "\xC4\xBA" => "l",   "\xC4\xBB" => "L",
	      "\xC4\xBC" => "l",   "\xC4\xBD" => "L",   "\xC4\xBE" => "l",   "\xC4\xBF" => "L",
	      "\xC5\x80" => "l",   "\xC5\x81" => "L",   "\xC5\x82" => "l",   "\xC5\x83" => "N",
	      "\xC5\x84" => "n",   "\xC5\x85" => "N",   "\xC5\x86" => "n",   "\xC5\x87" => "N",
	      "\xC5\x88" => "n",   "\xC5\x89" => "n",   "\xC5\x8A" => "N",   "\xC5\x8B" => "n",
	      "\xC5\x8C" => "O",   "\xC5\x8D" => "o",   "\xC5\x8E" => "O",   "\xC5\x8F" => "o",
	      "\xC5\x90" => "O",   "\xC5\x91" => "o",   "\xC5\x92" => "CE",  "\xC5\x93" => "ce",
	      "\xC5\x94" => "R",   "\xC5\x95" => "r",   "\xC5\x96" => "R",   "\xC5\x97" => "r",
	      "\xC5\x98" => "R",   "\xC5\x99" => "r",   "\xC5\x9A" => "S",   "\xC5\x9B" => "s",
	      "\xC5\x9C" => "S",   "\xC5\x9D" => "s",   "\xC5\x9E" => "S",   "\xC5\x9F" => "s",
	      "\xC5\xA0" => "S",   "\xC5\xA1" => "s",   "\xC5\xA2" => "T",   "\xC5\xA3" => "t",
	      "\xC5\xA4" => "T",   "\xC5\xA5" => "t",   "\xC5\xA6" => "T",   "\xC5\xA7" => "t",
	      "\xC5\xA8" => "U",   "\xC5\xA9" => "u",   "\xC5\xAA" => "U",   "\xC5\xAB" => "u",
	      "\xC5\xAC" => "U",   "\xC5\xAD" => "u",   "\xC5\xAE" => "U",   "\xC5\xAF" => "u",
	      "\xC5\xB0" => "U",   "\xC5\xB1" => "u",   "\xC5\xB2" => "U",   "\xC5\xB3" => "u",
	      "\xC5\xB4" => "W",   "\xC5\xB5" => "w",   "\xC5\xB6" => "Y",   "\xC5\xB7" => "y",
	      "\xC5\xB8" => "Y",   "\xC5\xB9" => "Z",   "\xC5\xBA" => "z",   "\xC5\xBB" => "Z",
	      "\xC5\xBC" => "z",   "\xC5\xBD" => "Z",   "\xC5\xBE" => "z",   "\xC6\x8F" => "E",
	      "\xC6\xA0" => "O",   "\xC6\xA1" => "o",   "\xC6\xAF" => "U",   "\xC6\xB0" => "u",
	      "\xC7\x8D" => "A",   "\xC7\x8E" => "a",   "\xC7\x8F" => "I",
	      "\xC7\x90" => "i",   "\xC7\x91" => "O",   "\xC7\x92" => "o",   "\xC7\x93" => "U",
	      "\xC7\x94" => "u",   "\xC7\x95" => "U",   "\xC7\x96" => "u",   "\xC7\x97" => "U",
	      "\xC7\x98" => "u",   "\xC7\x99" => "U",   "\xC7\x9A" => "u",   "\xC7\x9B" => "U",
	      "\xC7\x9C" => "u",
	      "\xC7\xBA" => "A",   "\xC7\xBB" => "a",   "\xC7\xBC" => "AE",  "\xC7\xBD" => "ae",
	      "\xC7\xBE" => "O",   "\xC7\xBF" => "o",
	      "\xC9\x99" => "e",
	
	      "\xC2\x82" => ",",        // High code comma
	      "\xC2\x84" => ",,",       // High code double comma
	      "\xC2\x85" => "...",      // Tripple dot
	      "\xC2\x88" => "^",        // High carat
	      "\xC2\x91" => "\x27",     // Forward single quote
	      "\xC2\x92" => "\x27",     // Reverse single quote
	      "\xC2\x93" => "\x22",     // Forward double quote
	      "\xC2\x94" => "\x22",     // Reverse double quote
	      "\xC2\x96" => "-",        // High hyphen
	      "\xC2\x97" => "--",       // Double hyphen
	      "\xC2\xA6" => "|",        // Split vertical bar
	      "\xC2\xAB" => "<<",       // Double less than
	      "\xC2\xBB" => ">>",       // Double greater than
	      "\xC2\xBC" => "1/4",      // one quarter
	      "\xC2\xBD" => "1/2",      // one half
	      "\xC2\xBE" => "3/4",      // three quarters
	
	      "\xCA\xBF" => "\x27",     // c-single quote
	      "\xCC\xA8" => "",         // modifier - under curve
	      "\xCC\xB1" => ""          // modifier - under line
	   );
	   return strtr($instr, $tranmap);
	}
}
?>