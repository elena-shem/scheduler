<?php echo Form::open(array("class"=>"form-horizontal")); echo Form::csrf();?>

	<fieldset>
		<div class="form-group">
			<?php echo Form::label('Period id', 'period_id', array('class'=>'control-label')); ?>

				<?php echo Form::input('period_id', Input::post('period_id', isset($examcourse) ? $examcourse->period_id : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Period id')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Course id', 'course_id', array('class'=>'control-label')); ?>

				<?php echo Form::input('course_id', Input::post('course_id', isset($examcourse) ? $examcourse->course_id : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Course id')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Day', 'day', array('class'=>'control-label')); ?>

				<?php echo Form::input('day', Input::post('day', isset($examcourse) ? $examcourse->day : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Day')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Hour', 'hour', array('class'=>'control-label')); ?>

				<?php echo Form::input('hour', Input::post('hour', isset($examcourse) ? $examcourse->hour : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Hour')); ?>

		</div>
		<div class="form-group">
			<label class='control-label'>&nbsp;</label>
			<?php echo Form::submit('submit', 'Save', array('class' => 'btn btn-primary')); ?>		</div>
	</fieldset>
<?php echo Form::close(); ?>