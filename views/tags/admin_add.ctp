<div class="tags form">
<?php echo $form->create('Tag');?>
	<fieldset>
 		<legend><?php __('Add Tag');?></legend>
	<?php
		echo $form->input('name', array(
			'label' => __('Name:', true),
			'error' => array(
				'notEmpty' => __('Tag name cannot be left blank', true),
				'isUnique' => __('This Tag already exists.', true),
			)
		));
	?>
	</fieldset>
<?php echo $form->end(__('Submit', true));?> 
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Tags', true), array('action'=>'index'));?></li>
	</ul>
</div>
