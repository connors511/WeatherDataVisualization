<h2>Listing Users <small><?php echo (count($users) ? 'Viewing '.(Pagination::$offset+1).' - '.(Pagination::$offset+count($users)).' of '.$total_items : ''); ?></small></h2>
<br />

<div class="control-group">
	<?php echo Html::anchor('admin/user/create', '<i class="icon-white icon-pencil"></i> Add New User', array('class' => 'btn btn-primary')); ?>			
</div>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Username</th>
			<th>Group</th>
			<th>Email</th>
			<th>Last login</th>
			<th>Updated At</th>
			<th>Created At</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php if ($users): ?>
<?php foreach ($users as $user): ?>		<tr>

			<td>
				<?php echo $user->username; ?>
			</td>
			<td><?php echo isset($groups[$user->group]) ? $groups[$user->group]['name'] : $user->group; ?></td>
			<td><?php echo $user->email; ?></td>
			<td><?php echo $user->last_login ? '<span title="'.date('d-m-Y H:i',$user->last_login).'">'.Date::time_ago($user->last_login).'</span>' : 'Never'; ?></td>
			<td><span title="<?php echo date('d-m-Y H:i',$user->updated_at); ?>"><?php echo Date::time_ago($user->updated_at); ?></span></td>
			<td><?php echo date('d-m-Y H:i',$user->created_at); ?></td>
			<td>
				<?php echo Html::anchor('admin/user/edit/'.$user->id, '<i class="icon-pencil icon-white"></i>', array('class'=>'btn btn-mini btn-primary')); ?> 
				<?php echo Html::anchor('admin/user/delete/'.$user->id, '<i class="icon-trash icon-white"></i>', array('class'=>'btn btn-mini btn-danger','onclick' => "return confirm('Are you sure?')")); ?>
			</td>
		</tr>
<?php endforeach; ?>
<?php else: ?>
		<tr><td colspan="6">No users</td></tr>
<?php endif; ?>
	</tbody>
</table>

<?php echo $pagination ?>
