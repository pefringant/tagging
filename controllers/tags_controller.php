<?php
class TagsController extends TaggingAppController
{
	var $name = 'Tags';
	
	var $paginate = array(
		'Tag' => array(
			'order' => 'Tag.name ASC',
			'limit' => 20
		)
	);
	
	var $components = array('RequestHandler');
	
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
	 * List Tags
	 * You have to create a view for this action in {your_app}/views/tags/index.ctp
	 */
	function index()
	{
		$this->set('data', $this->paginate());
	}
	
	/**
	 * View Tag
	 * Checks $this->params['pass'] for tag slug or tag id
	 * You have to create a view for this action in {your_app}/views/tags/view.ctp
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
		
		if(!$data = $this->Tag->{$findMethod}($param))
		{
			$this->cakeError('error404', array(array('url' => $this->action)));
		}
		
		$this->set(compact('data'));
	}
	
	/**
	 * List Tags
	 */
	function admin_index()
	{
		$this->set('data', $this->paginate());
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
				$this->Session->setFlash(__('The Tag has been saved', true));
				$this->redirect(array('action'=>'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Tag could not be saved. Please, try again.', true));
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
			$this->Session->setFlash(__('Invalid Tag', true));
			$this->redirect(array('action'=>'index'));
		}
		
		if(!empty($this->data))
		{
			if($this->Tag->save($this->data))
			{
				$this->Session->setFlash(__('The Tag has been saved', true));
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The Tag could not be saved. Please, try again.', true));
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
			$this->Session->setFlash(__('Invalid id for Tag', true));
		}
		
		if($this->Tag->del($id))
		{
			$this->Session->setFlash(__('Tag deleted', true));
		}
		
		$this->redirect(array('action' => 'index'));
	}
}
?>