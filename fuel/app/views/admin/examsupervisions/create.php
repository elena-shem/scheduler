<div class="container">
    <br>

    <?php echo Form::open(["class"=>"form-horizontal"]); echo Form::csrf(); ?>

        <?php
        echo render('admin/examsupervisions/_form', [
            'type'                  => isset($type) ? $type : 'create',
            'supervision'           => isset($supervision) ? $supervision : null,
            'doctorals'             => $doctorals,
            'examcourses'           => $examcourses,
            'examperiods'           => $examperiods,
            'selected_examperiod_id'=> isset($selected_examperiod_id) ? $selected_examperiod_id : null,
            'examcourses_options' => isset($examcourses_options) ? $examcourses_options : null, 
        ]);
        ?>

        <div class="form-group">
            <div class="form-actions-bar">

                <?= Html::anchor(
                    'admin/examsupervisions',
                    '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                    ['class'=>'btn btn-sm btn-warning']
                ); ?>

                <button type="submit" class="btn btn-sm btn-success">
                    <i class="glyphicon glyphicon-save"></i> Save
                </button>

            </div>
        </div>
    <?php echo Form::close(); ?>
</div>
