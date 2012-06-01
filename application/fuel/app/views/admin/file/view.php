<h2>Viewing #<?php echo $file->id; ?></h2>

<p>
	<strong>Path:</strong>
	<?php echo $file->path; ?></p>
<p>
	<strong>Type:</strong>
	<?php echo $file->type; ?></p>
<p>
	<strong>User id:</strong>
	<?php echo $file->user_id; ?></p>

<?php echo Html::anchor('admin/file/edit/'.$file->id, 'Edit'); ?> |
<?php echo Html::anchor('admin/file', 'Back'); ?>