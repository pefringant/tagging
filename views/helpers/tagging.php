<?php
class TaggingHelper extends AppHelper
{
	var $helpers = array('Html', 'Form', 'Javascript');
	
	/**
	 * Init flag to avoid including js and css files multiple times.
	 *
	 * @var boolean True if $this->init_suggest() is called
	 */
	var $init_done = false;
	
	/**
	 * TagSuggest options
	 *
	 * @var array Options :
	 * - selector : DOM selector to be observed by the plugin (try to keep it simple, id ('#xyz') or class ('.xyz') only).
	 * - url : url to get suggestions via ajax POST call (JSON formatted response)
	 * - delay : sets the delay between keyup and the request (in milliseconds)
	 * - separator : tag separator string
	 * - matchClass : class applied to the suggestions
	 * - sort : boolean to force the sorted order of suggestions
	 * - tagContainer : the type of element uses to contain the suggestions
	 * - tagWrap : the type of element the suggestions a wrapped in
	 * - tags : array of tags specific to this instance of element matches
	 */
	var $options= array(
		'selector' => '.tagSuggest',
		'url' => '/tagging/tags/suggest',
		'delay' => 500,
		'separator' => ', ',
		'matchClass' => 'tagMatches',
		'sort' => false,
		'tagContainer' => 'span',
		'tagWrap' => 'span',
		'tags' => null,
	);
	
	/**
	 * Sets default options for jQuery tag suggest plugin
	 *
	 * @param  mixed $options Default options
	 */
	function options($options = array())
	{
		$this->options = array_merge($this->options, $options);
	}
	
	/**
	 * Add required Js and CSS files in HTML head
	 * Init jQuery tags suggest on inputs of class 'tagSuggest'
	 */
	function init_suggest()
	{
		if($this->init_done)
		{
			return;
		}
		
		// Alert if jQuery not inclduded when debug > 0
		if(Configure::read('debug') > 0)
		{
			$alert_mssg = __d('tagging', 'Tag suggestion requires jQuery !', true);
			
			$alert = "if(typeof jQuery != 'function'){
				alert('{$alert_mssg}');
			}";

			$this->Javascript->codeBlock($alert, array('inline' => false));
		}
		
		// jQuery Tag plugin
		// ©Remy Sharp
		// http://remysharp.com/2007/12/28/jquery-tag-suggestion/
		$this->Javascript->link('/tagging/js/tag.js', false);
		
		// Tag plugin CSS
		$this->Html->css('/tagging/css/tagging.css', null, array('media' => 'screen'), false);
		
		// Init javascript
		$options = $this->options;
		
		$selector = $options['selector'];
		
		unset($options['selector']);
		
		$script = "$(function () {
			$('{$selector}').tagSuggest(
				{$this->Javascript->object($options)}
			);
		});";
		
		$this->Javascript->codeBlock($script, array('inline' => false));
		
		// Init done !
		$this->init_done = true;
	}
	
	/**
	 * Adds selector id or class to the input field call
	 *
	 * @param string $fieldName Input field name
	 * @param array $options Input field options
	 * @return string Input field html code
	 */
	function input($fieldName, $options = array())
	{
		$this->init_suggest();
		
		$selector = $this->options['selector'];
		
		if(!isset($options['id']) && strpos($selector, '#') !== false)
		{
			$options['id'] = substr($selector, strpos($selector, '#')+1);
		}
		
		if(!isset($options['class']) && strpos($selector, '.') !== false)
		{
			$options['class'] = substr($selector, strpos($selector, '.')+1);
		}
		
		return $this->Form->input($fieldName, $options);
	}

	/**
	 * Tag cloud generator
	 *
	 * @param array $data Results from tags table, requires 'count' key for each row
	 * @param array $options Options :
	 * - max_scale : a scale factor, based on tag weight, will be appended to every link css class, between 1 and $max_scale
	 * - linkClass : CSS class prefix for tag links. Scale factor will be appended to this class name
	 * - element : path to an element to render the tag output.
	 *   Available values in the element :
	 *   - $data : Tag record
	 *   - $scale : scale factor
	 *   - $percentage : relative size. May be useful for font size or font color.
	 * - type : type of output, defaults to ul
	 * - id : DOM id for top level 'type'
	 * - class : CSS class for top level 'type'
	 * - itemType : type of item output, defaults to li
	 * - itemClass : CSS class for items of type 'itemType'
	 * - url : URL params to pass to HtmlHelper::url()
	 *   - plugin : plugin name, defaults to 'tagging'. DO NOT use 'false' but 'null' for no plugin in the route
	 *   - controller : Controller name, defaults to 'tags'
	 *   - action : Controller action name, defaults to 'view'
	 *   - pass : arguments to be passed to the Router, defaults to 'slug'. Can only be 'id', 'slug' or array('id', 'slug')
	 *   - admin : boolean admin routing, defaults to false
	 *   URL pattern defaults to '/tags/view/tag-slug'.
	 * @return string Output, defaults to :
	 * <ul>
	 *     <li><a href="/tags/view/tag1-slug" class="tag-size-5">Tag1 name</a></li>
	 *     <li><a href="/tags/view/tag2-slug" class="tag-size-2">Tag2 name</a></li>
	 *     ...
	 * </ul>
	 */
	function generateCloud($data = array(), $options = array())
	{
		$options = Set::merge(array(
			'max_scale' => 7,
			'linkClass' => 'tag-size-',
			'element'   => false,
			'type'      => 'ul',
			'id'        => false,
			'class'     => false,
			'itemType'  => 'li',
			'itemClass' => false,
			'url' => array(
				'plugin'     => 'tagging',
				'controller' => 'tags',
				'action'     => 'view',
				'pass'       => 'slug',
				'admin'      => false
			),
		), (array)$options);
		
		extract($options);
		
		// Weight bounds
		$weights = Set::extract('/Tag/count', $data);
		
		$minWeight = min($weights);
		$maxWeight = max($weights);
		
		// Output
		$output = '<' . $type;
		
		if($id)	$output .= ' id="' . $id . '"';
		if($class) $output .= ' class="' . $class . '"';
		
		$output .= '>' . "\n";
		
		foreach($data as $row)
		{
			$output .= "\t";
			
			if($itemType)
			{
				$output .= '<' . $itemType;
				
				if($itemClass) $output .= ' class="' . $itemClass . '"';
				
				$output .= '>';
			}
			
			$scale = $this->_getScale($row['Tag']['count'], $minWeight, $maxWeight, 1, $max_scale);
			$percentage = $this->_getPercentage($row['Tag']['count'], $minWeight, $maxWeight);
			
			if($element)
			{
				$view =& ClassRegistry::getObject('view');
				
				$elementData = array(
					'data' => $row,
					'scale' => $scale,
					'percentage' => $percentage
				); 
				
				$output .= $view->element($element, $elementData); 
			}
			else
			{
				// URL parameters
				$url_params = array(
					'plugin' => $url['plugin'],
					'controller' => $url['controller'],
					'action' => $url['action'],
					'admin' => $url['admin'],
				);
				
				if(is_string($url['pass']))
				{
					$url['pass'] = array($url['pass']);
				}
				
				foreach($url['pass'] as $param)
				{
					if(isset($row['Tag'][$param]))
					{
						$url_params[] = $row['Tag'][$param];
					}
				}
				
				// Link HTML options
				$url_options = array();
				
				if($linkClass)
				{
					$url_options = array('class' => $linkClass . $scale);
				}
				
				$output .= $this->Html->link($row['Tag']['name'], $url_params, $url_options);
			}
			
			if($itemType) $output .= '</' . $itemType . '>';
			
			$output .= "\n";
		}
		
		$output .= '</' . $type . '>' . "\n";
		
		return $output;
	}
	
	/**
	 * Private scale calculation method
	 *
	 * @param int $weight Tag weight
	 * @param int $minWeight Minimum tag weight in the cloud
	 * @param int $maxWeight Maximum tag weigt in the cloud
	 * @param int $minScale Minimum scale
	 * @param int $maxScale Maximum scale
	 * @return int scale
	 */
	function _getScale($weight, $minWeight, $maxWeight, $minScale, $maxScale)
	{
		$spread = $maxWeight - $minWeight;
		
		if($spread == 0)
		{
			$spread = 1;
		}
		
		$step = ($maxScale - $minScale) / $spread;
		
		$scale = $minScale + (($weight - $minWeight) * $step);
		
		return ceil($scale); 
	}

	/**
	 * Private percentage calculation method
	 *
	 * @param int $weight Tag weight
	 * @param int $minWeight Minimum tag weight in the cloud
	 * @param int $maxWeight Maximum tag weigt in the cloud
	 * @return int Percentage
	 */
	function _getPercentage($weight, $minWeight, $maxWeight)
	{
		return $this->_getScale($weight, $minWeight, $maxWeight, 0, 100);
	}
}
?>