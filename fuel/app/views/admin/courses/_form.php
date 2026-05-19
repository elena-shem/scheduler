<?php echo Form::open(array("class"=>"form-horizontal")); echo Form::csrf();?>

	<fieldset>
        <div class="form-group">
            <?php echo Form::label('Official Course Id', 'special_id', array('class'=>'control-label')); ?>

            <?php echo Form::input('special_id', Input::post('special_id', isset($course) ? $course->special_id : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Official Course Id')); ?>

        </div>
		<div class="form-group">
			<?php echo Form::label('Code', 'code', array('class'=>'control-label')); ?>

				<?php echo Form::input('code', Input::post('code', isset($course) ? $course->code : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Code')); ?>

		</div>

        <div class="form-group">
            <?php echo Form::label('Code2', 'code2', array('class'=>'control-label')); ?>

            <?php echo Form::input('code2', Input::post('code2', isset($course) ? $course->code2 : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Code2')); ?>

        </div>
        
		<div class="form-group">
			<?php echo Form::label('Title', 'title', array('class'=>'control-label')); ?>

				<?php echo Form::input('title', Input::post('title', isset($course) ? $course->title : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Title')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Number of supervisors Winter', 'number_of_supervisors_winter', array('class'=>'control-label')); ?>

				<?php echo Form::input('number_of_supervisors_winter', Input::post('number_of_supervisors_winter', isset($course) ? $course->number_of_supervisors_winter : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Number of supervisors Winter')); ?>

		</div>
        <div class="form-group">
            <?php echo Form::label('Number of supervisors Summer', 'number_of_supervisors_summer', array('class'=>'control-label')); ?>

            <?php echo Form::input('number_of_supervisors_summer', Input::post('number_of_supervisors_summer', isset($course) ? $course->number_of_supervisors_summer : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Number of supervisors Summer')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('Number of supervisors September', 'number_of_supervisors_september', array('class'=>'control-label')); ?>

            <?php echo Form::input('number_of_supervisors_september', Input::post('number_of_supervisors_september', isset($course) ? $course->number_of_supervisors_september : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Number of supervisors September')); ?>

        </div>
        <?php if($type != "create"): ?>
            <div class="form-group">
                <?php echo Form::label('Professors', 'professors', array('class'=>'control-label')); ?>


                <?php echo Form::select('professors[]', $possesed_professors,$professors, array('multiple' => 'multiple', 'name'=> 'professors', 'class' => 'col-md-4 form-control', 'placeholder'=>'Professors', 'style' => "resize:both;")); ?>

            </div>
        <?php endif; ?>
        <div class="form-group">
            <div class="form-actions-bar">

                <?php
                echo Html::anchor(
                    'admin/courses',
                    '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                    ['class' => 'btn btn-sm btn-warning']
                );
                ?>

                <?php if ($type != "create"): ?>
                    <?php
                    echo Html::anchor(
                        'admin/courses/view/'.$course->id,
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
