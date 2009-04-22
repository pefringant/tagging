<?php
class Tag extends TaggingAppModel
{
	var $name = 'Tag';
	
	var $displayField = 'name';

	var $hasMany = array('Tagged' => array(
		'className' => 'Tagging.Tagged',
		'dependent' => true
	));
	
	var $actsAs = array(
		'Tagging.Sluggable' => array(
			'label' => 'name',
			'length' => 160,
			'translation' => 'utf-8'
		)
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'notEmpty'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'on' => 'create',
				'message' => 'isUnique'
			)
		),
		'slug' => array(
			'rule' => 'notEmpty',
			'on' => 'update'
		),
	);
	
	/**
	 * Returns tags matching first letters
	 *
	 * @param string $first_letters
	 * @return array Matching tag names as a simple associative array
	 */
	function suggest($first_letters = '')
	{
		if(empty($first_letters))
		{
			return;
		}
		
		$first_letters = trim($first_letters);
		
		App::import('Core', 'Multibyte');
		
		if(Multibyte::strlen($first_letters) <= 2)
		{
			return;
		}

		$fields     = array('name');
		$conditions = array('name LIKE' => "{$first_letters}%");
		$order      = 'name ASC';
		$limit      = 10;
		$recursive  = -1;
		
		return array_values($this->find('list', compact(
			'fields', 'conditions', 'order', 'limit', 'recursive'
		)));
	}
	
	/**
	 * Save a tag and the association with the tagged model
	 *
	 * @param string $tag Tag name
	 * @param array $tagged Tagged model parameters array : tagged model name and tagged model primary key
	 */
	function saveTag($tag = '', $tagged = array())
	{
		if(empty($tag) or empty($tagged))
		{
			return;
		}
		
		// Tag exists ?
		$this->recursive = -1;
		
		if(!$this->data = $this->find(array('name' => $tag)))
		{
			$this->data = array('Tag' => array('name' => $tag));
		}
		
		// Related model
		$this->data['Tagged'] = array($tagged);
		
		return $this->saveAll($this->data);
	}

	/**
	 * Find used tags, all models
	 *
	 * @param array $options Options (same as classic find options)
	 * Two new keys available :
	 * - min_count : minimum number of times a tag is used
	 * - max_count : maximum number of times a tag is used
	 * @return array
	 */
	function tagCloud($options = array())
	{
		// Counting bounds:
		// 'min_count' and/or 'max_count' in $options ?
		$conditions = array();
		
		if(isset($options['min_count']))
		{
			$conditions[] = 'Tag.count >= ' . $options['min_count'];
			unset($options['min_count']);
		} else {
			$conditions[] = 'Tag.count > 0';
		}
		
		if(isset($options['max_count']))
		{
			$conditions[] = 'Tag.count <= ' . $options['max_count'];
			unset($options['max_count']);
		}
				
		$options = Set::merge(compact('conditions'), $options);
		
		// ORDER BY default
		if(empty($options['order']))
		{
			$options['order'] = 'name ASC';
		}
		
		// Recursive level imposed
		$options['recursive'] = -1;
		
		return $this->find('all', $options);
	}
}
?>