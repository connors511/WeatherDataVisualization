<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?></title>
		<?php echo Asset::css('bootstrap.min.css'); ?>
		<style>
			body { padding-top: 60px; }
		</style>
		<?php
		echo Asset::js(array(
		    'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
		    'bootstrap.min.js'
		));
		?>
	</head>
	<body>
		<?php if ($current_user): ?>
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<?php echo Html::anchor(Uri::base(false), 'WeatherApp', array('class' => 'brand')) ?>
						<div class="btn-group pull-right">
							<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="icon-user"></i> <?php echo $current_user->username ?>
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li><a href="#">Profile</a></li>
								<li class="divider"></li>
								<li><?php echo Html::anchor('admin/logout', 'Logout') ?></li>
							</ul>
						</div>
						<div class="nav-collapse">
							<ul class="nav">
								<li>
									<?php echo Html::anchor(Uri::base(false), 'Map'); ?>
								</li>
								<li class="<?php echo Uri::segment(2) == '' || Uri::segment(2) == 'index' ? 'active' : '' ?>">
									<?php echo Html::anchor('admin', 'Dashboard') ?>
								</li>
								

								<?php foreach (glob(APPPATH . 'classes/controller/admin/*.php') as $controller): ?>

									<?php
									$section_segment = basename($controller, '.php');
									$section_title = Inflector::pluralize(Inflector::humanize($section_segment));
									?>

									<li class="<?php echo Uri::segment(2) == $section_segment ? 'active' : '' ?>">
										<?php echo Html::anchor('admin/' . $section_segment, $section_title) ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div><!--/.nav-collapse -->
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="container">
			<h1><?php echo $title; ?></h1>
			<hr />
			<div class="row">
				<div class="span12">
					<?php if (Session::get_flash('success')): ?>
						<div class="alert alert-success">
							
								<?php echo implode('<br>', (array) Session::get_flash('success')); ?>
							
						</div>
					<?php endif; ?>
					<?php if (Session::get_flash('error')): ?>
						<div class="alert alert-error">
								<?php echo implode('<br>', (array) Session::get_flash('error')); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="span12">
					<?php echo $content; ?>
				</div>
			</div>
			<hr />
			<footer>
				<p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
				<p>
					&copy; 2012 Mads E. Lundt, Matthias S. Larsen & Joachim V. V. Jensen<br>
					<small>Built with <a href="http://fuelphp.com">FuelPHP</a> <?php echo e(Fuel::VERSION); ?></small>
				</p>
			</footer>
		</div>
	</body>
</html>
