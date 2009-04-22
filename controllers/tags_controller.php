<?php
class TagsController extends TaggingAppController
{
	var $name = 'Tags';
	
	var $components = array('RequestHandler');
	
	var $helpers = array('Html', 'Form', 'Tagging.Tagging');
	
	var $paginate = array(
		'Tag' => array(
			'order' => 'Tag.name ASC',
			'limit' => 20,
			'recursive' => -1
		),
		'Tagged' => array(
			'fields' => array('Tagged.model', 'Tagged.model_id'),
			'order' => 'Tagged.id DESC',
			'limit' => 10
		)
	);
	
	/**
	 * JSON format tag suggestions based on first letters of tag name
	 */
	function suggest()
	{
		if($this->RequestHandler->isAjax() && $this->RequestHandler->isPost())
		{
			App::import('Core', 'Sanitize');
			
			$first_letters = Sanitize::clean($this->params['form']['tag']);
			
			$this->set('matches', $this->Tag->suggest($first_letters));
		}
	}
	
	/**
	 * All Tags used at least once
	 * You have to create a view for this action in {your_app}/views/plugins/tagging/tags/index.ctp
	 * 
	 * Available variables in view :
	 * $data : all used tags ordered by name ASC
	 */
	function index()
	{
		$data = $this->Tag->tagCloud();
		
		if(isset($this->params['requested']))
		{
			return $data;
		}
		
		$this->set('data', $data);
	}
	
	/**
	 * View Tag
	 * Checks $this->params['pass'] for slug or id
	 * You have to create a view for this action in {your_app}/views/plugins/tagging/tags/view.ctp
	 * 
	 * Available variables in view :
	 * $tag : Tag data
	 * $data : paginated ressources tagged with this tag
	 */
	function view()
	{
		if(!isset($this->params['pass'][0]))
		{
			$this->cakeError('error404', array(array('url' => $this->action)));
		}
		
		$param = $this->params['pass'][0];

		if(preg_match('/^\d+$/', $param))
		{
			$findMethod = 'findById';
		}
		else
		{
			$findMethod = 'findBySlug';
		}
		
		$this->Tag->recursive = -1;
		
		if(!$tag = $this->Tag->{$findMethod}($param))
		{
			$this->cakeError('error404', array(array('url' => $this->action)));
		}
		
		$tagged = $this->paginate('Tagged', array('Tag.id' => $tag['Tag']['id']));
		
		// Build $data with actual Models data
		$data = array();
		
		foreach($tagged as $row)
		{
			$data[] = ClassRegistry::init($row['Tagged']['model'])->read(null, $row['Tagged']['model_id']);
		}
		
		$this->set(compact('tag', 'data'));
	}
	
	/**
	 * List Tags
	 */
	function admin_index()
	{
		$this->set('data', $this->paginate('Tag'));
	}
	
	/**
	 * Add Tag
	 */
	function admin_add()
	{
		if(!empty($this->data))
		{
			$this->Tag->create();
			
			if($this->Tag->save($this->data))
			{
				$this->Session->setFlash(__d('tagging', 'The Tag has been saved', true));
				$this->redirect(array('action'=>'index'));
			}
			else
			{
				$this->Session->setFlash(__d('tagging', 'The Tag could not be saved. Please, try again.', true));
			}
		}
	}
	
	/**
	 * Edit Tag
	 *
	 * @param int $id Tag id
	 */
	function admin_edit($id = null)
	{
		if(!$id && empty($this->data))
		{
			$this->Session->setFlash(__d('tagging', 'Invalid Tag', true));
			$this->redirect(array('action'=>'index'));
		}
		
		if(!empty($this->data))
		{
			if($this->Tag->save($this->data))
			{
				$this->Session->setFlash(__d('tagging', 'The Tag has been saved', true));
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__d('tagging', 'The Tag could not be saved. Please, try again.', true));
			}
		}
		
		if(empty($this->data))
		{
			$this->data = $this->Tag->read(null, $id);
		}
	}

	/**
	 * Delete Tag
	 *
	 * @param int $id Tag id
	 */
	function admin_delete($id = null)
	{
		if(!$id)
		{
			$this->Session->setFlash(__d('tagging', 'Invalid id for Tag', true));
		}
		
		if($this->Tag->del($id))
		{
			$this->Session->setFlash(__d('tagging', 'Tag deleted', true));
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
?>