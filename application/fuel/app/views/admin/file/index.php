<h2>Listing <?php echo count($files); ?> Files</h2>
<br />
<p>
<?php echo Html::anchor('admin/file/create', 'Add new File', array('class' => 'btn success')); ?>
</p>
<?php if ($files): ?>
<table class="zebra-striped">
	<thead>
		<tr>
			<th>Name</th>
			<th>Location</th>
			<th>File</th>
			<th>Type</th>
			<th>User id</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($files as $file): ?>		<tr>

			<td><?php switch($file->name) {
				case '0':
					echo 'Parsing';
					break;
				case '1':
					echo 'Error';
					break;
				default:
					echo $file->name;
					break;
			}?></td>
			<td><?php if ($file->name != '0' and $file->name != '1') echo $file->latitude.','.$file->longitude; ?></td>
			<td><?php echo $file->path; ?></td>
			<td><?php echo $file->type; ?></td>
			<td><?php echo $file->user->username; ?></td>
			<td>
				<?php echo Html::anchor('admin/file/view/'.$file->id, 'View'); ?> |
				<?php echo Html::anchor('admin/file/delete/'.$file->id, 'Delete', array('onclick' => "return confirm('Are you sure?')")); ?>

			</td>
		</tr>
<?php endforeach; ?>	</tbody>
</table>

<?php else: ?>
<p>No Files.</p>

<?php endif; ?>
