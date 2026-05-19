<div class="settings-container">
<?php echo Form::open(array("class"=>"form-horizontal"));
echo Form::csrf();
?>


    <div role="tabpanel">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#set1" aria-controls="set1" role="tab" data-toggle="tab">Link Expiration</a></li>
        </ul>

        <!-- Tab panes -->


        <fieldset>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="set1">
                <div class="form-group">
                    <?php
                    //var_dump($settings);exit;
                    $field = 'linkexpirationdate';
                    $temp_model = Model_Globalsetting::property($field);
                    echo Form::label( $temp_model['label'], $field, array('class'=>'control-label'));
                    echo Form::input($field, Input::post($field,$settings->$field), array('class' => 'col-md-4 form-control', 'placeholder'=>$settings->$field, 'id'=>'datepicker'));
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    $field = 'overridelinkexpirationdate';
                    $temp_model = Model_Globalsetting::property($field);
                    echo Form::label( $temp_model['label'], $field, array('class'=>'control-label'));
                    echo Form::select($field,$settings->$field, array('0'=>'EMAIL DEFAULT','1'=>'OVERRIDE!'), array( 'name'=> $field, 'class' => 'col-md-4 form-control', 'placeholder'=> $temp_model['label'], 'style' => "resize:both;"));
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    $field = 'alllinksopen';
                    $temp_model = Model_Globalsetting::property($field);
                    echo Form::label( $temp_model['label'], $field, array('class'=>'control-label'));
                    echo Form::select($field,$settings->$field, array('0'=>'EMAIL DEFAULT','1'=>'ALL LINKS OPEN'), array( 'name'=> $field, 'class' => 'col-md-4 form-control', 'placeholder'=> $temp_model['label'], 'style' => "resize:both;"));
                    ?>
                </div>
            </div>
        </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-2">
                        <?php
                        echo Html::anchor('admin/emails', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning btn-block'));
                        ?>
                    </div>
                    <div class="col-md-2 col-md-offset-3">
                        <?php echo Form::submit('submit', ' Save', array('class' => 'btn btn-sm btn-success btn-block','style' => "display: inline-block; height:33px; font-family: 'Glyphicons Halflings';font-style: normal;font-weight: 400;line-height: 1;position: relative;top: 1px;")); ?>
                    </div>
                    <div class="col-md-1 col-md-offset-2">
                        <?php echo Html::anchor('admin/globalsettings/enable', '<i class="glyphicon glyphicon-exclamation-sign"></i> Enable', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure? Current Settings will be overwritten!')")); ?>
                    </div>

                </div>
            </div>
        </fieldset>

    </div>




<?php echo Form::close(); ?>


</div>