<?php
/**
 * Textile Helper Class for CakePHP.
 * 
 * Requires classTextile.php to be within a vendor directory.
 *
 * @version     0.1
 * @author  tclineks
 * @see     http://textile.thresholdstate.com/
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */

App::import('Vendor', 'classTextile');

/**
 * Textile Helper class
 */
class TextileHelper extends AppHelper {
    
    var $textile;
    var $initialized = false;
    
    /**
     * Creates textile object
     */
    function init() {
        if (!$this->initialized) {
            $this->textile = new Textile();
            if (!is_a($this->textile, 'Textile')) {
                trigger_error('Textile could not be initalized');
            }
            $this->initialized = true;
        }
    }
    
    /**
     * Wrapper for TextileThis
     */
    function TextileThis($text, $lite='', $encode='', $noimage='', $strict='', $rel=''){
        $this->init();
        return $this->textile->TextileThis($text, $lite='', $encode='', $noimage='', $strict='', $rel='');
    }
    
    /**
     * Wrapper for TextileRestricted
     */
    function TextileRestricted($text, $lite=1, $noimage=1, $rel='nofollow'){
        $this->init();
        return $this->textile->TextileRestricted($text, $lite=1, $noimage=1, $rel='nofollow');
    }

}

?>