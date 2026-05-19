<div class="container-fluid">

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Viewing email: <?php echo $email->title; ?></h2>



                <p>
                    <strong>Subject:</strong>
                    <?php echo $email->subject; ?></p>
                <p>
                    <strong>Links Visited:</strong>
                    <?php echo $emails_used."/".$emails_number; ?></p>
                <p>
                    <strong>Links with saved content:</strong>
                    <?php echo $emails_saved."/".$emails_number; ?></p>
                <p>
                    <strong>Exam Period:</strong>
                    <?php echo $exam_period_info; ?></p>
                <p>

                    <?php echo (isset($email->valid_date)?" <strong>Valid until: </strong>".$email->valid_date:"<strong>Valid for:</strong> ".$email->datespan." days."); ?>

                </p>
                <p>
                    <strong>Content:</strong></p>

                <div class="well"><?php echo $email->html_content; ?></div>
            </div>
        </div>
    </div>
    <hr>
    <h5>Quick Search: </h5>
    <input type="text" id="searchSurname" placeholder="Surname">
    <input type="text" id="searchName" placeholder="Name">
    <input type="text" id="searchEmail" placeholder="Email">
    <hr>
    <?php  echo \Fuel\Core\Form::open(); ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-hover sortable" id="sortabletable">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Surname/Link</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Admin Comment</th>
                                <th>Status</th>
                                <th>Link Visited</th>
                                <th>Valid Until</th>
                                <th>Saved something</th>
                                <!--th>Rearm One</th>
                                <th>Delete One</th-->
                            </tr>
                        </thead>
                    <?php $num=1;?>
                    <?php foreach($doctorals as $doctoral): ?>
                        <tr id="<?php echo 'line'.$num++;?>">
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo Form::checkbox('checkbox_url[]', $doctoral->url_id, false); ?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?> fullButton'><?php echo Html::anchor($doctoral->unique_uri,$doctoral->surname,array('target' => '_blank'));?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo $doctoral->name;?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo str_replace(",","<br/>",$doctoral->email); ?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo \Fuel\Core\Str::truncate($doctoral->comment,20);?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo ($doctoral->sent?"<strong>Already Sent!</strong>":"<strong>Not Sent Yet!</strong>");?></td>
                            <td class='<?php echo ($doctoral->used?"danger":"success");?>'><?php echo ($doctoral->used?"<strong>Visited at: ".$doctoral->updated_at."</strong>":"<strong>Not Visited Yet!</strong>");?></td>
                            <td class='<?php echo ($doctoral->is_still_valid?"danger":"success");?>'><?php echo $doctoral->valid_until;?></td>
                            <td class='<?php echo ($doctoral->has_saved_settings?"danger":"success");?>'><?php echo ($doctoral->has_saved_settings?"<strong>Saved!</strong>":"<strong>Nothing saved!</strong>");?></td>
                            <!--td><?php //echo Html::anchor('admin/emails/rearm_one/'.$doctoral->url_id, '<i class="glyphicon glyphicon-flash"></i> Rearm This!', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?></td>
                            <td><?php //echo Html::anchor('admin/emails/delete_one/'.$doctoral->url_id, '<i class="glyphicon glyphicon-trash"></i> Delete This!', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?></td-->
                        </tr>
                    <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>


        <div class="row" style="margin-top: 100px;">
            <div class="col-md-2">
                <?php
                echo Html::anchor('admin/emails', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning btn-block'));
                ?>
            </div>
            <div class="col-md-2">
                <?php echo Html::anchor('admin/emails/edit/'.$email->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-primary btn-block')); ?>
            </div>
            <div class="col-md-2">
                <?php echo \Fuel\Core\Form::submit('rearm_selected','Rearm Selected!',array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")) ?>
                <?php //echo Html::anchor('admin/emails/rearm_selected/'.$email->id, '<i class="glyphicon glyphicon-flash"></i> Rearm Selected!', array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")); ?>
            </div>
            <div class="col-md-2">
                <?php echo \Fuel\Core\Form::submit('delete_selected','Delete Selected!',array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")) ?>
                <?php //echo Html::anchor('admin/emails/delete_selected/'.$email->id, '<i class="glyphicon glyphicon-trash"></i> Delete Selected!', array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")); ?>
            </div>
            <div class="col-md-2">
                <?php echo Html::anchor('admin/emails/rearm/'.$email->id, '<i class="glyphicon glyphicon-flash"></i> Rearm All!', array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")); ?>
            </div>
            <div class="col-md-2">
                <?php echo Html::anchor('admin/emails/send/'.$email->id, '<i class="glyphicon glyphicon-send"></i> Send Now!', array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")); ?>
            </div>
        </div>
    <?php echo \Fuel\Core\Form::close(); ?>

</div>
