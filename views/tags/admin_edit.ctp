<div class="tags form">
<?php echo $form->create('Tag');?>
	<fieldset>
 		<legend><?php __('Edit Tag');?></legend>
	<?php
		echo $form->input('id');
		
		echo $form->input('name', array(
			'label' => __('Name:', true),
			'error' => array(
				'notEmpty' => __('Tag name cannot be left blank', true),
				'isUnique' => __('This Tag already exists.', true),
			)
		));
		
		echo $form->input('slug', array(
			'label' => __('Slug:', true),
			'error' => __('Slug cannot be left blank', true)
		));
	?>
	</fieldset>
<?php echo $form->end(__('Submit', true));?> 
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Tag.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Tag.id'))); ?></li>
		<li><?php echo $html->link(__('List Tags', true), array('action'=>'index'));?></li>
	</ul>
</div>
