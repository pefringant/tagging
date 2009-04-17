<?php
$this->pageTitle = __d('tagging', 'Tagging admin', true);
?>

<ul>
	<li><?php echo $html->link(
		__d('tagging', 'Add Tag', true),
		array(
			'plugin' => 'tagging',
			'controller' => 'tags',
			'action' => 'admin_add'
		)
	); ?></li>
	<li><?php echo $html->link(
		__d('tagging', 'List Tags', true),
		array(
			'plugin' => 'tagging',
			'controller' => 'tags',
			'action' => 'admin_index'
		)
	); ?></li>
</ul>