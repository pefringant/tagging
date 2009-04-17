<?php $paginator->options(array('url' => $this->passedArgs)); ?>

<div class="tags index">
<h2><?php __d('tagging', 'Tags');?></h2>

<br/>
<p>
<?php
echo $paginator->counter(array(
'format' => __d('tagging', 'Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th class="actions"><?php __d('tagging', 'Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($data as $row):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $row['Tag']['id']; ?>
		</td>
		<td>
			<?php echo $row['Tag']['name']; ?>
		</td>
		<td>
			<?php echo $row['Tag']['created']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__d('tagging', 'Edit', true), array('action'=>'edit', $row['Tag']['id'])); ?>
			<?php echo $html->link(__d('tagging', 'Delete', true), array('action'=>'delete', $row['Tag']['id']), null, sprintf(__d('tagging', 'Are you sure you want to delete # %s?', true), $row['Tag']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__d('tagging', 'previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__d('tagging', 'next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__d('tagging', 'New Tag', true), array('action'=>'add')); ?></li>
	</ul>
</div>
