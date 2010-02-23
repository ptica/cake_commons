<?php
/**
 * Nullify Behavior class file.
 *
 * Model Behavior to make nulls out of empty strings if schema allows it.
 *
 * @filesource
 * @package	app
 * @subpackage	models.behaviors
 */

/**
 * Make nulls out of empty strings if schema allows it
 *
 * @author	Christian Winther aka "Jippi"
 * @package	app
 * @subpackage	models.behaviors
 */
class NullifyBehavior extends ModelBehavior {
	/**
	 * Initiate behaviour for the model using specified settings.
	 *
	 * @param object $model	Model using the behaviour
	 * @param array $settings	Settings to override for model.
	 *
	 * @access public
	 */
	function setup(&$model, $settings = array()) {
		$default = array();

		if (!isset($this->settings[$model->name])) {
			$this->settings[$model->name] = $default;
		}

		$this->settings[$model->name] = array_merge($this->settings[$model->name], ife(is_array($settings), $settings, array()));
	}

	/**
	 * Run before a model is saved
	 *
	 * @param object $model	Model about to be saved.
	 *
	 * @access public
	 * @since 1.0
	 */
	function beforeSave(&$model) {
		$tableInfo = $model->schema();
        foreach ($tableInfo as $name => $field) {
            if ($field['null']) {
                if (isset($model->data[$model->name][$name]) && $model->data[$model->name][$name] === '') {
                    $model->data[$model->name][$name] = null;
                }
            }
        }
	}
}
?>