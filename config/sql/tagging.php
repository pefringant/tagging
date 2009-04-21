<?php
/**
 * This is Tagging plugin Schema file
 *
 * Using the Schema command line utility
 * cake schema run create Tagging -path plugins/tagging/config/sql
 */
class TaggingSchema extends CakeSchema {

	var $name = 'Tagging';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $tags = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => true, 'length' => 160),
		'slug' => array('type'=>'string', 'null' => true, 'length' => 160),
		'count' => array('type'=>'integer', 'null' => false, 'default' => 0, 'length' => 10),
		'created' => array('type'=>'datetime'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

	var $tagged = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'tag_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type'=>'string', 'null' => true, 'length' => 100),
		'model_id' => array('type'=>'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'tag_id' => array('column' => 'tag_id', 'unique' => 0),
			'model' => array('column' => 'model', 'unique' => 0),
			'model_id' => array('column' => 'model_id', 'unique' => 0)
		)
	);

}
?>