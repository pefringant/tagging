<?php
class Tagged extends TaggingAppModel
{
	var $name = 'Tagged';
	
	var $useTable = 'tagged';
	
	var $belongsTo = array('Tag' => array(
		'className' => 'Tagging.Tag',
		'counterCache' => 'count'
	));
	
	/**
	 * Finds tags related to a record
	 *
	 * @param string $model Model name
	 * @param int $model_id Related model primary key
	 * @return mixed Found related tags
	 */
	function findTags($model, $model_id)
	{
		$conditions = array(
			'Tagged.model' => $model,
			'Tagged.model_id' => $model_id
		);
		
		$fields    = array('Tag.id', 'Tag.name', 'Tag.slug', 'Tag.created');
		$order     = 'Tag.name ASC';
		$recursive = 0;

		return $this->find('all', compact('fields', 'conditions', 'order', 'recursive'));
	}

	/**
	 * Find tag cloud for a model
	 *
	 * @param string $model Model name
	 * @param array $options Options (same as classic find options)
	 * @return array
	 */
	function tagCloud($model, $options = array())
	{
		$conditions = array(
			'Tagged.model' => $model
		);
		
		$options = Set::merge(compact('conditions'), $options);
		
		// Fields imposed
		$options['fields'] = array('Tag.id', 'Tag.name', 'Tag.slug', 'Tag.created', 'COUNT(Tag.id) as count');
		
		// Counting bounds:
		// 'min_count' and/or 'max_count' in $options ?
		$having = '';
		$count_bounds = array();
		
		if(isset($options['min_count']))
		{
			$count_bounds[] = 'count >= ' . $options['min_count'];
			unset($options['min_count']);
		}
		
		if(isset($options['max_count']))
		{
			$count_bounds[] = 'count <= ' . $options['max_count'];
			unset($options['max_count']);
		}
		
		if(!empty($count_bounds))
		{
			$having = ' HAVING ' . join(' AND ', $count_bounds);
		}
		
		// GROUP BY imposed
		$options['group'] = array('Tag.id' . $having);

		// Recursive imposed
		$options['recursive'] = 0;
		
		$results = $this->find('all', $options);
		
		// Move the 'count' key into the right place
		foreach($results as $k => $row)
		{
			$results[$k]['Tag']['count'] = $row[0]['count'];
			
			unset($results[$k][0]);
		}
		
		return $results;
	}
	
	/**
	 * Find records tagged with $tag_ids, excluding a model_id
	 *
	 * @param string $model Model name
	 * @param mixed $tag_ids Tag id(s)
	 * @param int $exclude_id Model id to exclude
	 * @return array
	 */
	function taggedWith($model = null, $tag_ids = null, $exclude_id = null, $limit = null)
	{
		$conditions = array(
			'tag_id' => $tag_ids,
		);
		
		if($model)
		{
			$conditions['model'] = $model;
		}
		
		if($exclude_id)
		{
			$exclude_ids = array_values($this->find('list', array(
				'fields'     => 'id',
				'conditions' => array('model' => $model, 'model_id' => $exclude_id),
				'recursive'  => -1
			)));
			
			$conditions['NOT'] = array('id' => $exclude_ids);
		}
		
		$fields    = array('model', 'model_id', 'COUNT(*) as count');
		$group     = array('model', 'model_id');
		$order     = 'count DESC';
		$recursive = -1;
		
		return $this->find('all', compact('fields', 'conditions', 'group', 'order', 'limit', 'recursive'));
	}
}
?>