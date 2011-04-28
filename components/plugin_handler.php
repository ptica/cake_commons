<?php
//File: /app/controllers/components/plugin_handler.php

/**
 * PluginHandler component adds a basic functionality
 * required for the plugin development. Main features
 * are plugin configuration autoloading and callbacks
 * from the controller.
 *
 * @author Sky_l3ppard
 * @version 1.4
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @category Components
 */
class PluginHandlerComponent extends Object {
    /**
     * Reference to the controller
     *
     * @var object
     * @access private
     */
    var $__controller = null;

    /**
     * Plugin Settings, available options:
     *         autoload - array of configuration files to be loaded
     *         permanently - true to load configuration files before any action,
     *             false - loaded only for a plugin's controller actions
     * Notice: bootstrap is loaded then the component initialize method is
     * fired and for the same reason routes will not work. If you want to include
     * then from the plugin. Use the app bootstrap to scan plugins for routes
     *
     * @var array
     * @access private
     */
    var $__settings = array(
        'autoload' => array(
            'bootstrap',
            'core',
            'inflections'
        ),
        'permanently' => true
    );

    /**
     * Initializes component by loading all configuration files from
     * all plugins found in application. Configuration files should be
     * placed in \app\plugins\your_plugin\config\ directory. Be careful,
     * it will overwrite all settings loaded from \app\config if the
     * setting name matches.
     * At the end it will execute an 'initialize' callback method loaded
     * from \plugins\your_plugin\{your_plugin}_auto_loader.php file
     *
     * @param object $controller - reference to the controller
     * @param array $settings - component settings, list of autoload files
     * @return void
     * @access public
     */
    function initialize(&$controller, $settings = array()) {
        $this->__controller = $controller;
        $this->__settings = array_merge_recursive($this->__settings, (array)$settings);

        foreach (App::objects('plugin') as $plugin) {
            $is_parent_class = strpos(get_parent_class($controller), Inflector::classify($plugin)) !== false;
            if ($this->__settings['permanently'] || (!$this->__settings['permanently'] && $is_parent_class)) {
                foreach ($this->__settings['autoload'] as $type) {
                    App::import(
                        'Plugin',
                        Inflector::classify("{$plugin}_{$type}"),
                        array('file' => Inflector::underscore($plugin).DS.'config'.DS.$type.'.php')
                    );
                }
            }
        }

        $this->loaderExecute('initialize');
    }

    /**
     * Executes a 'beforeFilter' callback method loaded
     * from \plugins\your_plugin\{your_plugin}_auto_loader.php file
     *
     * @param object $controller - reference to the controller
     * @return void
     * @access public
     */
    function startup(&$controller) {
        $this->loaderExecute('beforeFilter');
    }

    /**
     * Executes a 'beforeRender' callback method loaded
     * from \plugins\your_plugin\{your_plugin}_auto_loader.php file
     *
     * @param object $controller - reference to the controller
     * @return void
     * @access public
     */
    function beforeRender(&$controller) {
        $this->loaderExecute('beforeRender');
    }

    /**
     * Initializes \plugins\your_plugin\{your_plugin}_auto_loader.php file
     * and executes specified callback $method from AutoLoader class for
     * all plugins found in application.
     *
     * @param string $method - name of the method to execute
     * @return void
     * @access public
     */
    function loaderExecute($method) {
        foreach (App::objects('plugin') as $plugin) {
            $loader_file = Inflector::underscore($plugin).'_auto_loader';
            $loader_class = Inflector::classify($loader_file);
            $loader_instance = null;

            if (!ClassRegistry::isKeySet($loader_class)) {
                App::import('Plugin', $loader_class, Inflector::underscore($plugin).DS.$loader_file.'.php');
                if (class_exists($loader_class)) {
                    ClassRegistry::addObject($loader_class, new $loader_class());
                }
            } else {
                $loader_instance =& ClassRegistry::getObject($loader_class);
            }

            if (!empty($loader_instance) && in_array($method, get_class_methods($loader_class))) {
                $loader_instance->{$method}($this->__controller);
            }
        }
    }
}
?>