<h2>Listing Files <small><?php echo (count($files) ? 'Viewing '.(Pagination::$offset+1).' - '.(Pagination::$offset+count($files)).' of '.$total_items : ''); ?></small></h2>
<br />

<?php echo Form::open('admin/file/mass'); ?>

<div class="control-group">
	<?php echo Html::anchor('admin/file/create', '<i class="icon-white icon-pencil"></i> Add New Files', array('class' => 'btn btn-primary')); ?>		
	<div class="controls pull-right">
		<div class="input-append">
		<?php
			echo Form::select('submit_type', null, array('Bulk Actions','del'=>'Delete'), array('class'=>'span3')).
			Form::button('submit', 'Apply', array('type'=>'submit','class'=>'btn','onclick' => "return confirm('Are you sure?')")) ?>
		</div>
	</div>	
</div>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th><?php echo Form::checkbox('chk_all') ?></th>
			<th>Name</th>
			<th>Location</th>
			<th>Path</th>
			<th>Type</th>
			<th>User</th>
			<th>Updated At</th>
			<th>Created At</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php if ($files): ?>
<?php foreach ($files as $file): ?>		<tr>
			<td><?php echo Form::checkbox('chk[]', $file->id, false, array('id'=>'form_chk_'.$file->id,'class'=>'form_chk')) ?></td>
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
			<td><span title="<?php echo date('d-m-Y H:i',$file->updated_at); ?>"><?php echo Date::time_ago($file->updated_at); ?></span></td>
			<td><?php echo date('d-m-Y H:i',$file->created_at); ?></td>
			<td>
				<?php echo Html::anchor('admin/file/delete/'.$file->id, '<i class="icon-trash icon-white"></i>', array('class'=>'btn btn-mini btn-danger','onclick' => "return confirm('Are you sure?')")); ?>
			</td>
		</tr>
<?php endforeach; ?>
<?php else: ?>
		<tr><td colspan="9">No files</td></tr>
<?php endif; ?>
	</tbody>
</table>

<?php echo $pagination ?>

<?php echo Form::close(); ?>
<script type="text/javascript">
	$('#form_chk_all').change( function() {
		$('.form_chk').attr('checked',$(this).is(':checked'));
	});
</script>
