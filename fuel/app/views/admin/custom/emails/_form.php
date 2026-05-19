<?php echo Form::open(array("class"=>"form-horizontal"));
      echo Form::csrf();
?>

    <fieldset>

        <div class="form-group">
            <?php echo Form::label('Identification Title', 'title', array('class'=>'control-label')); ?>

            <?php echo Form::input('title', Input::post('title', isset($email) ? $email->title : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Identification Title')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('Subject', 'subject', array('class'=>'control-label')); ?>

            <?php echo Form::input('subject', Input::post('subject', isset($email) ? $email->subject : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Subject')); ?>

        </div>

        <div class="form-group">
            <?php echo Form::label('Content', 'html_content', array('class'=>'control-label','id'=>'form_html_content_label')); ?>

            <?php echo Form::textarea('html_content', Input::post('html_content', isset($email) ? $email->html_content : ''), array('class' => 'col-md-8 form-control', 'rows' => 8, 'placeholder'=>'Content')); ?>

        </div>
        <div class="form-group">
            <?php echo Form::label('Question', 'question_id', array('class'=>'control-label')); ?>


            <?php echo Form::select('question_id',$already_selected_question, $questions, array( 'name'=> 'question_id', 'class' => 'col-md-4 form-control', 'placeholder'=>'Question', 'style' => "resize:both;")); ?>

        </div>

        <div class="form-group">
            <?php echo Form::label('Valid email days', 'datespan', array('class'=>'control-label')); ?>

            <?php echo Form::input('datespan', Input::post('datespan',isset($email) ? $email->datespan : ''), array('class' => 'col-md-4 form-control', 'placeholder'=>'Valid email days')); ?>

        </div>

        <div class="form-group">
            <?php echo Form::label('Doctorals', 'doctorals', array('class'=>'control-label')); ?>


            <?php echo Form::select('doctorals[]', $already_selected_doctoral_ids,$all_active_doctorals, array('multiple' => 'multiple', 'name'=> 'doctorals', 'class' => 'col-md-4 form-control', 'placeholder'=>'Doctorals', 'style' => "resize:both;")); ?>

        </div><div class="form-group">
            <div class="form-actions-bar">

                <div class="form-actions-left">
                    <?php
                    echo Html::anchor(
                        'admin/custom/emails',
                        '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                        ['class' => 'btn btn-sm btn-warning']
                    );
                    ?>

                    <?php if ($type != "create"): ?>
                        <?php echo Html::anchor(
                            'admin/custom/emails/view/'.$email->id,
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

    </fieldset>
<?php echo Form::close(); ?>


