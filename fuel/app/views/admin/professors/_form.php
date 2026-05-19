<?php echo Form::open(array("class"=>"form-horizontal")); echo Form::csrf();?>

	<fieldset>
		<div class="form-group">
			<?php echo Form::label('Name', 'name', array('class'=>'control-label')); ?>

				<?php echo Form::input('name', Input::post('name', isset($professor) ? $professor->name : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Name')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Surname', 'surname', array('class'=>'control-label')); ?>

				<?php echo Form::input('surname', Input::post('surname', isset($professor) ? $professor->surname : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Surname')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Email', 'email', array('class'=>'control-label')); ?>

				<?php echo Form::input('email', Input::post('email', isset($professor) ? $professor->email : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Email')); ?>

		</div>
        <div class="form-group">
			<?php echo Form::label('Telephone', 'telephone', array('class'=>'control-label')); ?>

				<?php echo Form::input('telephone', Input::post('telephone', isset($professor) ? $professor->telephone : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Telephone')); ?>

		</div>
        <div class="form-group">
			<?php echo Form::label('Office', 'office', array('class'=>'control-label')); ?>

				<?php echo Form::input('office', Input::post('office', isset($professor) ? $professor->office : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Office')); ?>

		</div>
        <?php if($type != "create"): ?>
            <div class="form-group">
                <?php echo Form::label('Doctorals', 'doctorals', array('class'=>'control-label')); ?>

                <?php echo Form::select('doctorals[]', $possesed_doctorals,$doctorals, array('multiple' => 'multiple', 'name'=> 'doctorals', 'class' => 'col-md-4 form-control', 'placeholder'=>'Doctorals', 'style' => "resize:both;")); ?>

            </div>

            <div class="form-group">
                <?php echo Form::label('Courses', 'courses', array('class'=>'control-label')); ?>

                <?php echo Form::select('courses[]', $possesed_courses,$courses, array('multiple' => 'multiple', 'name'=> 'courses', 'class' => 'col-md-4 form-control', 'placeholder'=>'Courses', 'style' => "resize:both;")); ?>

            </div>
        <?php endif; ?>

        <div class="form-group">
            <div class="form-actions-bar">

                <?php
                echo Html::anchor(
                    'admin/professors',
                    '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                    ['class' => 'btn btn-sm btn-warning']
                );
                ?>

                <?php if ($type != "create"): ?>
                    <?php echo Html::anchor(
                        'admin/professors/view/'.$professor->id,
                        '<i class="glyphicon glyphicon-search"></i> View',
                        ['class' => 'btn btn-sm btn-primary']
                    ); ?>
                <?php endif; ?>

                <button type="submit" class="btn btn-sm btn-success">
                    <i class="glyphicon glyphicon-save"></i> Save
                </button>

            </div>
        </div>

		</div>
	</fieldset>
<?php echo Form::close(); ?>
