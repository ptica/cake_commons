<?php

App::import('Helper', 'time'); 

class AtomHelper extends Helper {

function get_summary($text, $wordnum = 15) {
    // count multiple spaces as one space
    $text  = preg_replace("/ +/", " ", $text);
    $pos   = 0;
    $count = 0;
    
    while(($pos = strpos($text, ' ', $pos+1)) !== false) {
        if (++$count >= $wordnum) {
            break;
        }
    }
    
    $summary = substr($text, 0, $pos);
    return($summary);
}

// OBSOLETED BY prep_atom_text_construct
function to_xhtmlTextConstruct($content) {
	// nahradit html entities jejich glyphy
	$content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');
	// the content of the Text construct MUST be a single XHTML div element
	$content = '<div xmlns="http://www.w3.org/1999/xhtml">'."\n" . $content . '</div>';
	return $content;
}

// OBSOLETED BY prep_atom_text_construct
function to_plainTextConstruct($content) {
	// nahradit html entities jejich glyphy (&nbsp; &ndash; ...)
	$content = html_entity_decode($content, ENT_COMPAT, 'UTF-8');
	// pripadne tagy jako &lt; ...
	$content = htmlspecialchars($content);
	return $content;
}

/**
 * Determine the type of a string of data with the data formatted.
 *
 * Tell whether the type is text, html, or xhtml, per RFC 4287 section 3.1.
 *
 * In the case of WordPress, text is defined as containing no markup,
 * xhtml is defined as "well formed", and html as tag soup (i.e., the rest).
 *
 * Container div tags are added to xhtml values, per section 3.1.1.3.
 *
 * @link http://www.atomenabled.org/developers/syndication/atom-format-spec.php#rfc.section.3.1
 *
 * @package WordPress
 * @subpackage Feed
 * @since 2.5
 *
 * @param string $data Input string
 * @return array array(type, value)
 */
function prep_atom_text_construct($data) {
	if (strpos($data, '<') === false && strpos($data, '&') === false) {
		return array('text', $data);
	}
	
	// try to alleviate the undeclared entity error
	// xml_parser knows just few entities - no &ndash; no &nbsp; ...
	// we convert them to utf glyphs
	// but still bare ampersands remain so we convert them by hand
	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
	$data = preg_replace('/&(?![a-zA-Z]{2,8};)/', '&amp;', $data);
	// translate to numeric doesnt have full list (no &ndash; ...) :(
	// $data = $this->_translateLiteral2NumericEntities($data);
	

	$parser = xml_parser_create();
	xml_parse($parser, '<div>' . $data . '</div>', true);
	$code = xml_get_error_code($parser);
	xml_parser_free($parser);

	if (!$code) {
		if (strpos($data, '<') === false) {
			return array('text', $data);
		} else {
			if (strpos($data, '<o:') === false) {
				$data = "<div xmlns='http://www.w3.org/1999/xhtml'>$data</div>";
			} else {
				// define namespace for office embeded code
				$data = "<div xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>$data</div>";
			}
			return array('xhtml', $data);
		}
	}
	

	if (strpos($data, ']]>') == false) {
		return array('html', xml_error_string($code)."<![CDATA[$data]]>");
	} else {
		return array('html', htmlspecialchars($data));
	}
}

/**
 * Translate literal entities to their numeric equivalents and vice versa.
 *
 * PHP's XML parser (in V 4.1.0) has problems with entities! The only one's that are recognized
 * are &amp;, &lt; &gt; and &quot;. *ALL* others (like &nbsp; &copy; a.s.o.) cause an 
 * XML_ERROR_UNDEFINED_ENTITY error. I reported this as bug at http://bugs.php.net/bug.php?id=15092
 * The work around is to translate the entities found in the XML source to their numeric equivalent
 * E.g. &nbsp; to &#160; / &copy; to &#169; a.s.o.
 * 
 * NOTE: Entities &amp;, &lt; &gt; and &quot; are left 'as is'
 * 
 * @author Sam Blum bs_php@users.sourceforge.net
 * @param string $xmlSource The XML string
 * @param bool   $reverse (default=FALSE) Translate numeric entities to literal entities.
 * @return The XML string with translatet entities.
 */
function _translateLiteral2NumericEntities($xmlSource, $reverse = FALSE) {
    static $literal2NumericEntity;
    
    if (empty($literal2NumericEntity)) {
		$transTbl = get_html_translation_table(HTML_ENTITIES);
		foreach ($transTbl as $char => $entity) {
			if (strpos('&"<>', $char) !== FALSE) continue;
			$literal2NumericEntity[$entity] = '&#'.ord($char).';';
			echo "$entity ".ord($char)."<br>";
		}
	}
	if ($reverse) {
		return strtr($xmlSource, array_flip($literal2NumericEntity));
	} else {
		return strtr($xmlSource, $literal2NumericEntity);
	}
}

}
?>