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
	 * @param int $assoc_key Related model primary key
	 * @return mixed Found related tags
	 */
	function findTags($model, $assoc_key)
	{
		$conditions = array(
			'Tagged.model' => $model,
			'Tagged.assoc_key' => $assoc_key
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
	 * Find records tagged with $tag_ids, excluding record of id $exclude_id
	 *
	 * @param unknown_type $model
	 * @param unknown_type $tag_ids
	 * @param unknown_type $exclude_assoc_key
	 * @return array
	 */
	function taggedWith($model = null, $tag_ids = array(), $exclude_id = null, $limit = null)
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
			$conditions['id !='] = $exclude_id;
		}
		
		$fields    = array('model', 'assoc_key', 'COUNT(tag_id) as count');
		$group     = 'tag_id';
		$order     = 'count DESC';
		$recursive = -1;
		
		return $this->find('all', compact('fields', 'conditions', 'group', 'order', 'limit', 'recursive'));
	}
}
?>