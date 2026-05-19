<div class="container">
<h2>Viewing #<?php echo $user->id; ?></h2>

<p>
	<strong>Username:</strong>
	<?php echo $user->username; ?></p>

<p>
	<strong>Group:</strong>
	<?php echo Auth::group()->get_name($user->group); ?></p>
<p>
	<strong>Email:</strong>
	<?php echo $user->email; ?></p>
<p>
	<strong>Last login:</strong>
	<?php echo gmdate("d-m-Y H:i:s",  $user->last_login); ?></p>

    <?php echo Html::anchor('admin/users/', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning')); ?>
    <?php echo Html::anchor('admin/users/edit/'.$user->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-primary')); ?>

</div>