<?php
class TaggableBehavior extends ModelBehavior
{
	/**
	 * Tag Model handler
	 *
	 * @var object
	 */
	var $Tag = null;
	
	/**
	 * Tagged Model handler
	 *
	 * @var object
	 */
	var $Tagged = null;
	
	/**
	 * Initializes Tag and Tagged models
	 */
	function setup()
	{
		$this->Tag = ClassRegistry::init('Tagging.Tag');
		$this->Tagged = ClassRegistry::init('Tagging.Tagged');
	}
	
	/**
	 * Save tag and tagged models
	 *
	 * @param object $model
	 */
	function afterSave(&$model)
	{
		if(!isset($model->data[$model->alias]['tags']))
		{
			return;
		}
		
		$tagged_conditions = array(
			'model'     => $model->alias,
			'model_id' => $model->id,
		);
		
		$this->Tagged->deleteAll($tagged_conditions, false, true);

		$tags = Set::normalize($model->data[$model->alias]['tags'], false);
		$tags = array_unique($tags);
		
		foreach($tags as $tag)
		{
			$this->Tag->saveTag($tag, $tagged_conditions);
		}
	}
	
	/**
	 * Populates results array with a new field 'tags' with comma separated tag names
	 * Only for 1 row results sets (find('first') or read())
	 *
	 * @param object $model
	 * @param array $results
	 * @param array $primary
	 * @return array
	 */
	function afterFind(&$model, $results, $primary = false)
	{
		if(count($results) == 1 && isset($results[0][$model->alias][$model->primaryKey]))
		{
			$tags = $this->Tagged->findTags(
				$model->alias,
				$results[0][$model->alias][$model->primaryKey]
			);
					
			$results[0][$model->alias]['tags'] = join(', ', Set::extract('/Tag/name', $tags));
		}
		
		return $results;
	}
	
	/**
	 * Finds tags related to a record
	 *
	 * @param object $model
	 * @param int $id Related model primary key
	 * @param array $options Options (same as classic find options)
	 * @return mixed Found related tags
	 */
	function findTags(&$model, $id = null)
	{
		if(!$id && !$model->id)
		{
			return;
		}
		
		if(!$id)
		{
			$id = $model->id;
		}
		
		return $this->Tagged->findTags($model->alias, $id);
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
	function allTagCloud(&$model, $options = array())
	{
		return $this->Tag->tagCloud($options);
	}
	
	/**
	 * Find used tags, model specific
	 *
	 * @param array $options Options (same as classic find options)
	 * Two new keys available :
	 * - min_count : minimum number of times a tag is used
	 * - max_count : maximum number of times a tag is used
	 * @return array
	 */
	function tagCloud(&$model, $options = array())
	{
		return $this->Tagged->tagCloud($model->alias, $options);
	}

	/**
	 * Returns records which shares the most tags with record of id $id
	 *
	 * @param object $model
	 * @param int $id Record Id
	 * @param bool $restrict_to_model If true, returns related records of the same model, if false return all related records
	 * @param int limit Limit the number of records
	 * @return array Related records
	 */
	function findRelated(&$model, $id = null, $restrict_to_model = true, $limit = null)
	{
		if(is_bool($id))
		{
			$limit = $restrict_to_model;
			$restrict_to_model = $id;
			$id = null;
		}
		
		if(!$id && !$model->id)
		{
			return;
		}
		
		if(!$id)
		{
			$id = $model->id;
		}
		
		if(!$tags = $this->Tagged->findTags($model->alias, $id))
		{
			return;
		}
		
		$tag_ids = Set::extract('/Tag/id', $tags);
		
		// Restrict to Model ?
		$taggedWith_model = null;
		
		if($restrict_to_model)
		{
			$taggedWith_model = $model->alias;
		}
		
		// Exclude this record from results
		$exclude_ids = array_values($this->find('list', array(
			'fields'     => 'id',
			'conditions' => array('model' => $model->alias, 'model_id' => $id),
			'recursive'  => -1
		)));
		
		// Related records
		if(!$related = $this->Tagged->taggedWith($taggedWith_model, $exclude_ids, $id, $limit))
		{
			return;
		}
		
		// Final results
		if($restrict_to_model)
		{
			$model_ids = Set::extract('/Tagged/model_id', $related);
			
			$pk = $model->escapeField($model->primaryKey);
			
			$conditions = array($pk => $model_ids);
			$order = "FIELD({$pk}, " . join(', ', $model_ids) . ")";
			
			$results = $model->find('all', compact('conditions', 'order'));
		}
		else
		{
			$results = array();
			
			foreach($related as $row)
			{
				if($assoc_model = ClassRegistry::init($row['Tagged']['model']))
				{
					$assoc_model->id = $row['Tagged']['model_id'];
		
					$results[] = $assoc_model->read();
				}
			}
		}

		return $results;
	}
}
?>