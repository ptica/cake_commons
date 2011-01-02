<?php
    foreach ($this->viewVars as $var) {
    	echo $js->object($var);
    }
?>