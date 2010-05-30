<?php

App::import('Vendor', 'fb-php-sdk/src/facebook');

class FbComponent extends Object {
	
	//called before Controller::beforeFilter()
	function initialize(&$controller, $settings = array()) {
		// saving the controller reference for later use
		$this->controller =& $controller;
		
		// the component is configured per app in app/config/components/fb.php
		Configure::load('components'.DS.'fb');
		
		// Create Fb Application instance.
	    $app = new Facebook(array(
	      'appId'  => Configure::read('component_fb.appId'),
	      'secret' => Configure::read('component_fb.secret'),
	      'cookie' => false,
	    ));
	    
	    $session = $app->getSession();
	    if ($session) echo 'aaaa';
		
	    $this->app =& $app;
	}

	//called after Controller::beforeFilter()
	function startup(&$controller) {
	}

	//called after Controller::beforeRender()
	function beforeRender(&$controller) {
	}

	//called after Controller::render()
	function shutdown(&$controller) {
	}

	//called before Controller::redirect()
	function beforeRedirect(&$controller, $url, $status=null, $exit=true) {
	}

	function redirectSomewhere($value) {
		// utilizing a controller method
		$this->controller->redirect($value);
	}
}

?>