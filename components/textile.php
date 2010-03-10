<?php
/**
 * Textile Component Class for CakePHP
 *
 * Requires classTextile.php to be within a vendor directory.
 * 
 * @version     0.1
 * @author  tclineks
 * @see     http://textile.thresholdstate.com/
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::import('Vendor', 'classTextile', array('file'=>'classTextile.php'));

/**
 * Textile Component class
 */
class TextileComponent extends Object {
    
    var $textile;
    
    /**
     * Creates textile object
     */
    function startup(&$controller) {
        $this->textile = new Textile();
        if (!is_a($this->textile, 'Textile')) {
            trigger_error('Textile could not be initalized');
        }
    }
    
    /**
     * Wrapper for TextileThis
     */
    function TextileThis($text, $lite='', $encode='', $noimage='', $strict='', $rel=''){
    	// fo not force bq. to be used as a block (add a paragraph beggining before each bq.)
    	$text = preg_replace('/\bbq. /', "\n\nbq. ", $text);
    }
    
    /**
     * Wrapper for TextileRestricted
     */
    function TextileRestricted($text, $lite=1, $noimage=1, $rel='nofollow'){
    	// fo not force bq. to be used as a block (add a paragraph beggining before each bq.)
    	$text = preg_replace('/\bbq. /', "\n\nbq. ", $text);
        return $this->textile->TextileRestricted($text, $lite, $noimage, $rel);
    }

}

?>