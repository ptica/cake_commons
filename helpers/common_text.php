<?php

class CommonTextHelper extends Helper
{
	function get_first_sentence($txt, $one_line = false, $max_len = 72) {
		if ($one_line) {
			list($txt) = explode("\n", $txt);  
		}
		$txt = html_entity_decode($txt, ENT_COMPAT, 'UTF-8');
		// some dots do not qualify
		$txt = preg_replace('/(?<=\d)\./', '__SPECIAL_DOT__', $txt);
		
		
		$txt = preg_replace('/\s+/', ' ', $txt);
		$num = preg_match('/^[^.]*\. /', $txt, $res);
		if ($num > 0) $txt = @$res[0];
		
		// if too long, explode on spaces and try to cut after a comma
		if (strlen($txt) > $max_len) {
			$parts = explode(',', $txt);
			$sum_len = 0;
			$i = 0;
			foreach ($parts as $part) {
				if ($sum_len > $max_len) break;
				$sum_len += strlen($part);
				$i++;
			}
			$txt = $i > 1 ? implode(',', array_slice($parts, 0, $i-1)) : $parts[0];			
		}
		
		if (strlen($txt) > $max_len) {
			$parts = explode(' ', $txt);
			$sum_len = 0;
			$i = 0;
			foreach ($parts as $part) {
				if ($sum_len > $max_len) break;
				$sum_len += strlen($part);
				$i++;
			}
			$txt = $i > 1 ? implode(' ', array_slice($parts, 0, $i-1)) : $parts[0];			
		}
		
		// remove trailing prepositions or conjunctions
		$txt = preg_replace('/ (a|ad|během|bez|blízko|blíž|díky|dle|do|doprostřed|doprostředka|dovnitř|ibn|in|k|kol|kolem|kontra|krom|kromě|kvůli|mezi|mimo|místo|na|nad|namísto|napospas|naproti|napříč|navzdory|nedaleko|o|ob|od|of|ohledně|okolo|oproti|po|poblíž|pod|podél|podle|pomocí|pro|prostřed|prostřednictvím|proti|protivá|př|před|přes|při|s|skrz|u|uprostřed|uvnitř|v|van|vč|včetně|vedle|veprostřed|versus|vevnitř|vně|vprostřed|vprostředku|vpůli|vs|vstříc|vůči|vůkol|vyjma|vzdor|z|za|zevnitř|zhloubi|zkraje|zpod|zpoza|zprostřed|zprostředka|ačkoli|ale|alias|and|aneb|anebo|ani|anžto|avšak|až|buď|buďto|či|čili|et|i|jak|jednak|jenomže|jenže|jesli|kdežto|neb|nebo|neboli|neboť|nicméně|nýbrž|ovšem|proto|tak|tj|tudíž|však|vždyť|x)$/', '', $txt);
		// revert special dots
		$txt = preg_replace('/__SPECIAL_DOT__/', '.', $txt);
		
		return $txt;
	}
	
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
	
	
	function to_ascii($nadpis) {
		$url = $nadpis;
		$url = preg_replace('~\s+~', '-', $url);
		$url = $this->utf2ascii($url);
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
		$url = trim($url, "-");
		return $url;
	}
}

?>