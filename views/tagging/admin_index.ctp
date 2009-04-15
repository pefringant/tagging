<?php
$this->pageTitle = __('Tagging admin', true);
?>

<ul>
	<li><?php echo $html->link(
		__('Add Tag', true),
		array(
			'plugin' => 'tagging',
			'controller' => 'tags',
			'action' => 'admin_add'
		)
	); ?></li>
	<li><?php echo $html->link(
		__('List Tags', true),
		array(
			'plugin' => 'tagging',
			'controller' => 'tags',
			'action' => 'admin_index'
		)
	); ?></li>
</ul>