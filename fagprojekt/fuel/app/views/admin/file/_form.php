<?php echo Form::open(array('class' => 'form-stacked', 'enctype'=>'multipart/form-data')); ?>

	<fieldset>
		<div class="clearfix">
			<?php echo Form::label('Path', 'path'); ?>

			<div class="input">
				<?php echo Form::input('path', Input::post('path', isset($file) ? $file->path : ''), array('class'=>'span6','type'=>'file')); ?>

			</div>
		</div>
		<div class="actions">
			<?php echo Form::submit('submit', 'Save', array('class' => 'btn primary')); ?>

		</div>
	</fieldset>
<?php echo Form::close(); ?>