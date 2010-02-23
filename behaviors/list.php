<?php

/*
  By Joel Stein
  Version 04.01.2007
*/

class ListBehavior extends ModelBehavior {

  function setup(&$model, $config = array()) {
    $settings = am(array(
      'column' => 'position',
    ), $config);
    $this->settings[$model->name] = $settings;
  }

  // insert item at the bottom of the list upon creation
  function beforeSave(&$model) {
    $name = $model->name;
    extract($this->settings[$name]);
    if (empty($model->{$model->primaryKey})) {
      $bottom_item = $model->find(null, "$name.$column", "$column DESC", 0);
      $model->data[$name][$column] = $bottom_item[$name][$column] + 1;
    }
  }

  // move all items that are lower than the delete item higher
  function beforeDelete(&$model) {
    $name = $model->name;
    extract($this->settings[$name]);
    $item = $model->read();
    $model->updateAll(array($column => "$column - 1"), array("$column >" => "{$item[$name][$column]}"));
  }

  function getHigherItem(&$model, $position) {
    $name = $model->name;
    extract($this->settings[$name]);
    return $model->find(array("$name.$column" => $position - 1), array("$name.id", "$name.$column"), null, 0);
  }

  function getLowerItem(&$model, $position) {
    $name = $model->name;
    extract($this->settings[$name]);
    return $model->find(array("$name.$column" => $position + 1), array("$name.id", "$name.$column"), null, 0);
  }

  function moveHigher(&$model, $id) {
    $name = $model->name;
    extract($this->settings[$name]);
    $item = $model->find(array("$name.id" => $id), array("$name.id", "$name.$column"), null, 0);
    if (!$higher_item = $this->getHigherItem($model, $item[$name][$column])) return;
    $higher_item[$name][$column]++;
    $item[$name][$column]--;
    $model->save($higher_item);
    $model->save($item);
  }

  function moveLower(&$model, $id) {
    $name = $model->name;
    extract($this->settings[$name]);
    $item = $model->find(array("$name.id" => $id), array("$name.id", "$name.$column"), null, 0);
    if (!$lower_item = $this->getLowerItem($model, $item[$name][$column])) return;
    $lower_item[$name][$column]--;
    $item[$name][$column]++;
    $model->save($lower_item);
    $model->save($item);
  }

  function moveToTop(&$model, $id) {
    $name = $model->name;
    extract($this->settings[$name]);
    $item = $model->find(array("$name.id" => $id), array("$name.id", "$name.$column"), null, 0);
    $model->updateAll(array($column => "$column + 1"), array("$column <" => "{$item[$name][$column]}"));
    $item[$name][$column] = 1;
    $model->save($item);
  }

  function moveToBottom(&$model, $id) {
    $name = $model->name;
    extract($this->settings[$name]);
    $item = $model->find(array("$name.id" => $id), array("$name.id", "$name.$column"), null, 0);
    $model->updateAll(array($column => "$column - 1"), array("$column >" => "{$item[$name][$column]}"));
    $bottom_item = $model->find(null, "$name.$column", "$column DESC", 0);
    $item[$name][$column] = $bottom_item[$name][$column] + 1;
    $model->save($item);
  }

}