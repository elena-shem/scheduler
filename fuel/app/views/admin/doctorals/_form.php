<?php echo Form::open(array("class"=>"form-horizontal"));
      echo Form::csrf();
?>

	<fieldset>
        <div class="form-group">
            <?php echo Form::label('AM', 'am', array('class'=>'control-label')); ?>

            <?php echo Form::input('am', Input::post('am', isset($doctoral) ? $doctoral->am : '-'), array('class' => 'col-md-4 form-control', 'placeholder'=>'AM')); ?>

        </div>
		<div class="form-group">
			<?php echo Form::label('Name', 'name', array('class'=>'control-label')); ?>

				<?php echo Form::input('name', Input::post('name', isset($doctoral) ? $doctoral->name : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Name')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Surname', 'surname', array('class'=>'control-label')); ?>

				<?php echo Form::input('surname', Input::post('surname', isset($doctoral) ? $doctoral->surname : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Surname')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Email (comma separated)', 'email', array('class'=>'control-label')); ?>

				<?php echo Form::input('email', Input::post('email', isset($doctoral) ? $doctoral->email : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Email')); ?>

		</div>

        <div class="form-group">
            <?php echo Form::label('Registration Date', 'registrationdate', array('class'=>'control-label')); ?>

            <?php echo Form::input('registrationdate', Input::post('registrationdate', isset($doctoral) ? $doctoral->registrationdate : '-'), array('class' => 'col-md-4 form-control', 'placeholder'=>'Registration Date')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('Telephone', 'telephone', array('class'=>'control-label')); ?>

            <?php echo Form::input('telephone', Input::post('telephone', isset($doctoral) ? $doctoral->telephone : '-'), array('class' => 'col-md-4 form-control', 'placeholder'=>'Telephone')); ?>

        </div>
		<div class="form-group">
			<?php echo Form::label('Αποστολή email', 'sendemail', array('class'=>'control-label')); ?>
            <?php echo Form::select('sendemail', isset($doctoral) ? $doctoral->sendemail : '1' , array(
                '0' => 'ΟΧΙ',
                '1' =>  'ΝΑΙ',

            ),array('class' => 'col-md-4 form-control', 'placeholder'=>'Αποστολή email')); ?>

		</div>
        <div class="form-group">
            <?php echo Form::label('Απόφοιτος', 'graduated', array('class'=>'control-label')); ?>
            <?php echo Form::select('graduated', isset($doctoral) ? $doctoral->graduated : '0' , array(
                '0' => 'ΟΧΙ',
                '1' =>  'ΝΑΙ',

            ),array('class' => 'col-md-4 form-control', 'placeholder'=>'Απόφοιτος')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('Αναστολή', 'suspended', array('class'=>'control-label')); ?>
            <?php echo Form::select('suspended', isset($doctoral) ? $doctoral->suspended : '0' , array(
                '0' => 'ΟΧΙ',
                '1' =>  'ΝΑΙ',

            ),array('class' => 'col-md-4 form-control', 'placeholder'=>'Αναστολή')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('Comment', 'comment', array('class'=>'control-label')); ?>

            <?php echo Form::textarea('comment', Input::post('comment', isset($doctoral) ? $doctoral->comment : '-'), array('class' => 'col-md-4 form-control', 'rows' => 5, 'placeholder'=>'Comment text')); ?>

        </div>
		<div class="form-group">
			<?php echo Form::label('Hours remaining', 'hours_remaining', array('class'=>'control-label')); ?>

				<?php echo Form::input('hours_remaining', Input::post('hours_remaining', isset($doctoral) ? $doctoral->hours_remaining : '120'), array('class' => 'col-md-4 form-control', 'placeholder'=>'Hours remaining')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Hours completed (auto calculation possible)', 'hours_completed', array('class'=>'control-label')); ?>

				<?php echo Form::input('hours_completed', Input::post('hours_completed', isset($doctoral) ? $doctoral->hours_completed : '0'), array('class' => 'col-md-4 form-control', 'placeholder'=>'Hours completed ')); ?>

		</div>


        <div class="form-group">
            <?php echo Form::label('Max Assignments Per Schedule', 'max_assignments', array('class'=>'control-label')); ?>

            <?php echo Form::input('max_assignments', Input::post('max_assignments', isset($doctoral) ? $doctoral->max_assignments : 5), array('class' => 'col-md-4 form-control', 'placeholder'=>'Max Assignments Per Schedule')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('Bonus Weight Per Course', 'bonus_weight', array('class'=>'control-label')); ?>

            <?php echo Form::input('bonus_weight', Input::post('bonus_weight', isset($doctoral) ? $doctoral->bonus_weight : 0), array('class' => 'col-md-4 form-control', 'placeholder'=>'Bonus Weight Per Course')); ?>

        </div>

        <div class="form-group">
            <?php echo Form::label('Professors', 'professors', array('class'=>'control-label')); ?>

            <?php 
                $selected_professors = isset($professors) ? $professors : array();
                
                echo Form::select(
                    'professors[]', 
                    $possesed_professors, 
                    $selected_professors, 
                    array(
                        'multiple' => 'multiple', 
                        'name'=> 'professors[]',
                        'class' => 'col-md-4 form-control', 
                        'placeholder'=>'Professors', 
                        'style' => "resize:both;"
                    )
                ); 
            ?>
        </div>

        <div class="form-group">
        <div class="form-actions-bar">
            <?php
            echo Html::anchor(
                'admin/doctorals',
                '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                array('class' => 'btn btn-sm btn-warning')
            );
            ?>

            <?php if ($type != "create"): ?>
            <?php echo Html::anchor(
                'admin/doctorals/view/'.$doctoral->id,
                '<i class="glyphicon glyphicon-search"></i> View',
                array('class' => 'btn btn-sm btn-primary')
            ); ?>
            <?php endif; ?>

            <button type="submit" class="btn btn-sm btn-success">
            <i class="glyphicon glyphicon-save"></i> Save
            </button>
        </div>
        </div>

	</fieldset>
<?php echo Form::close(); ?>
