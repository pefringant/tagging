<div class="tags form">
<?php echo $form->create('Tag');?>
	<fieldset>
 		<legend><?php __d('tagging', 'Edit Tag');?></legend>
	<?php
		echo $form->input('id');
		
		echo $form->input('name', array(
			'label' => __d('tagging', 'Name:', true),
			'error' => array(
				'notEmpty' => __d('tagging', 'Tag name cannot be left blank', true),
				'isUnique' => __d('tagging', 'This Tag already exists.', true),
			)
		));
		
		echo $form->input('slug', array(
			'label' => __d('tagging', 'Slug:', true),
			'error' => __d('tagging', 'Slug cannot be left blank', true)
		));
	?>
	</fieldset>
<?php echo $form->end(__d('tagging', 'Submit', true));?> 
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__d('tagging', 'Delete', true), array('action'=>'delete', $form->value('Tag.id')), null, sprintf(__d('tagging', 'Are you sure you want to delete # %s?', true), $form->value('Tag.id'))); ?></li>
		<li><?php echo $html->link(__d('tagging', 'List Tags', true), array('action'=>'index'));?></li>
	</ul>
</div>
