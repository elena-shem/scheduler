<?php echo Form::open(array("class"=>"form-horizontal")); echo Form::csrf();?>

	<fieldset>
		<div class="form-group">
			<?php echo Form::label('Text', 'text', array('class'=>'control-label')); ?>
            <?php echo Form::textarea('text', Input::post('text', isset($welcome) ? $welcome->text : ''), array('class' => 'col-md-8 form-control', 'rows' => 8, 'placeholder'=>'Text')); ?>
		</div>

        <div class="form-group">
            <div class="form-actions-bar">
                <?php
                echo Html::anchor(
                    'admin/welcome',
                    '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                    array('class' => 'btn btn-sm btn-warning')
                );
                ?>

                <button type="submit" class="btn btn-sm btn-success">
                    <i class="glyphicon glyphicon-save"></i> Save
                </button>
            </div>
        </div>

	</fieldset>
<?php echo Form::close(); ?>


