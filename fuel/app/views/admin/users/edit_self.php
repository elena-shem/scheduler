<div class="container">
<h2>Editing <?php echo (isset($user) ?  $user->username : '');?></h2>
<br>

<?php echo Form::open(array("class"=>"form-horizontal")); echo Form::csrf();?>

	<fieldset>
		<div class="form-group">
			<?php echo Form::label('Username', 'username', array('class'=>'control-label')); ?>

				<?php echo Form::input('username', Input::post('username', isset($user) ? $user->username : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Username','readonly'=>'true')); ?>

		</div>
        <div class="form-group">
            <?php echo Form::label('Old Password', 'old_password', array('class'=>'control-label')); ?>

            <?php echo Form::password('old_password', Input::post('old_password', ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Password')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('New Password', 'password', array('class'=>'control-label')); ?>

            <?php echo Form::password('password', Input::post('password',''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Password')); ?>

        </div>

		<div class="form-group">
			<?php echo Form::label('Group', 'group', array('class'=>'control-label')); ?>
			<?php echo Form::select('group', isset($user) ? $user->group : '' , array(
                        '-1' => Auth::group()->get_name('-1'),
                        '0' =>  Auth::group()->get_name('0'),
                        '1' =>  Auth::group()->get_name('1'),
                        '50' => Auth::group()->get_name('50'),
                        '100' =>Auth::group()->get_name('100'),
                    ),array('class' => 'col-md-4 form-control', 'placeholder'=>'Group')); ?>





		</div>
		<div class="form-group">
			<?php echo Form::label('Email', 'email', array('class'=>'control-label')); ?>

				<?php echo Form::input('email', Input::post('email', isset($user) ? $user->email : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Email')); ?>

		</div>
		<div class="form-group">

            <div class="row">
                <div class="col-md-1">
                    <?php
                    echo Html::anchor('admin/users', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning btn-block'));
                    ?>
                </div>
                <div class="col-md-2 col-md-offset-8">
                    <?php echo Form::submit('submit', ' Save', array('class' => 'btn btn-sm btn-success btn-block','style' => "display: inline-block; font-family: 'Glyphicons Halflings';font-style: normal;font-weight: 400;line-height: 1;position: relative;top: 1px;")); ?>
                </div>
            </div>

        </div>

	</fieldset>
<?php echo Form::close(); ?>


</div>