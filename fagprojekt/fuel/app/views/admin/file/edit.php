<h2>Editing File</h2>
<br>

<?php echo render('admin\file/_form'); ?>
<p>
	<?php echo Html::anchor('admin/file/view/'.$file->id, 'View'); ?> |
	<?php echo Html::anchor('admin/file', 'Back'); ?></p>
