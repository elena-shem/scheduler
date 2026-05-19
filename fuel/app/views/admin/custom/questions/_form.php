<?php echo Form::open(array("class"=>"form-horizontal")); echo Form::csrf();?>

	<fieldset>
		<div class="form-group">
			<?php echo Form::label('Title', 'title', array('class'=>'control-label')); ?>

				<?php echo Form::input('title', Input::post('title', isset($question) ? $question->title : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Title')); ?>

		</div>
		<div class="form-group">
			<?php echo Form::label('Content Html', 'question_html', array('class'=>'control-label')); ?>

				<?php echo Form::textarea('question_html', Input::post('question_html', isset($question) ? $question->question_html : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Content Html', 'rows' => 10)); ?>

		</div>

        <div class="form-group">
            <div class="form-actions-bar">

                <div class="form-actions-left">
                    <?php
                    echo Html::anchor(
                        'admin/custom/questions',
                        '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                        ['class' => 'btn btn-sm btn-warning']
                    );
                    ?>

                    <?php if ($type != "create"): ?>
                        <?php echo Html::anchor(
                            'admin/custom/questions/view/'.$question->id,
                            '<i class="glyphicon glyphicon-search"></i> View',
                            ['class' => 'btn btn-sm btn-primary']
                        ); ?>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-sm btn-success">
                    <i class="glyphicon glyphicon-save"></i> Save
                </button>

            </div>
        </div>

		</div>
	</fieldset>
<?php echo Form::close(); ?>
