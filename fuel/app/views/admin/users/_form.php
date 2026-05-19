<div class="container">
    <?php echo Form::open(array("class"=>"form-horizontal"));?>

    <fieldset>

        <?php if($type == "create"): ?>
            <div class="form-group">
                <?php echo Form::label('Username', 'username', array('class'=>'control-label')); ?>

                <?php echo Form::input('username', Input::post('username', isset($user) ? $user->username : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Username')); ?>

            </div>
            <div class="form-group">
                <?php echo Form::label('Password', 'password', array('class'=>'control-label')); ?>

                <?php echo Form::input('password', Input::post('password', isset($user) ? $user->password : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Password')); ?>

            </div>
        <?php endif; ?>
        <div class="form-group">
            <?php echo Form::csrf(); ?>
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
            <div class="form-actions-bar">

                <?php
                echo Html::anchor(
                    'admin/users',
                    '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                    ['class' => 'btn btn-sm btn-warning']
                );
                ?>

                <?php if ($type != "create"): ?>
                    <?php
                    echo Html::anchor(
                        'admin/users/view/'.$user->id,
                        '<i class="glyphicon glyphicon-search"></i> View',
                        ['class' => 'btn btn-sm btn-primary']
                    );
                    ?>
                <?php endif; ?>

                <button type="submit" class="btn btn-sm btn-success">
                    <i class="glyphicon glyphicon-save"></i> Save
                </button>

            </div>
        </div>
    </fieldset>
    <?php echo Form::close(); ?>
</div>