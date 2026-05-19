<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <?php
	echo Asset::css(array(
		'bootstrap-flatly-theme.min.css',
		'template.css',
		'modern-admin.css',
	));
	if (isset($css)) {
		echo Asset::css($css);
	}
	?>

	<!-- jQuery + Bootstrap + Bootbox + Select2 -->
	<script src="/scheduler/public/assets/js/vendor/jquery/jquery-1.11.1.min.js"></script>
	<script src="/scheduler/public/assets/js/vendor/bootstrap.min.js"></script>
	<script src="/scheduler/public/assets/js/vendor/bootbox.min.js"></script>
	<link rel="stylesheet" href="/scheduler/public/assets/css/vendor/select2/select2.min.css">
	<script src="/scheduler/public/assets/js/vendor/select2/select2.min.js"></script>

	<?php
	if (isset($js)) {
		echo Asset::js($js);
	}
?>

</head>
<body>
<!-- Wrap all page content -->
<div id="wrap">

	<div class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo Uri::create('/'); ?>">
					Scheduler
				</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">

                    <?php if (!isset($current_user)): ?>
                        <li class="<?php echo Uri::segment(1) == 'about' ? 'active' : ''?>">
                            <?php echo Html::anchor('about', 'About'); ?>
                        </li>
                        <li><?php echo Html::anchor('admin/login', 'Login'); ?></li>
                    <?php else: ?>
                        <li><?php echo Html::anchor('admin', 'Dashboard') ?></li>
                        <li class="<?php echo Uri::segment(1) == 'about' ? 'active' : ''?>">
                            <?php echo Html::anchor('about', 'About'); ?>
                        </li>
                        <li><?php echo Html::anchor('admin/logout', 'Logout'); ?></li>
                    <?php endif;?>
				</ul>
			</div>
		</div>
	</div>



	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="page-header">
					<h1 class="page-title"><?php echo e($title); ?></h1>
				</div>
<?php if (Session::get_flash('success')): ?>
				<div id="flash-successful-action-returned" class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<p>
					<?php echo implode('</p><p>', (array) Session::get_flash('success')); ?>
					</p>
				</div>
<?php endif; ?>
<?php if (Session::get_flash('error')): ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<p>
					<?php echo implode('</p><p>', (array) Session::get_flash('error')); ?>
					</p>
				</div>
<?php endif; ?>
			</div>
			<div class="col-md-12">
				<div class="content-card">
					<?php echo $content; ?>
				</div>
			</div>
	</div>
</div>
<footer class="app-footer">
    <div class="container">
        <span class="text-muted">DI Exam Scheduler</span>
    </div>
</footer>

</body>
</html>
