<div class="container-fluid">

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2>Viewing Custom email: <?php echo $email->title; ?></h2>



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
                    <strong>Question Title:</strong>
                    <?php echo $question_info; ?></p>
                <p>

                    <?php echo (isset($email->valid_date)?" <strong>Valid until: </strong>".$email->valid_date:"<strong>Valid for:</strong> ".$email->datespan." days."); ?>

                </p>
                <p>
                    <strong>Content:</strong></p>

                <div class="well"><?php echo $email->html_content; ?></div>
            </div>
        </div>
    </div>

    <script>

        function toggleShowRight(id,comment){
            $(id).popover({
                html: true,
                animation: false,
                content: comment,
                placement: "right",
                container: 'body',
                trigger: 'click'
            });
        }
        function toggleShowLeft(id,comment){
            $(id).popover({
                html: true,
                animation: false,
                content: comment,
                placement: "left",
                container: 'body',
                trigger: 'click'

            })
                .on("show.bs.popover", function(){ $(this).data("bs.popover").tip().css("max-width", "700px"); });
            //http://stackoverflow.com/questions/19448902/changing-the-width-of-bootstrap-popover
        }

    </script>

    <hr>
    <h5>Quick Search: </h5>
    <input type="text" id="searchName" placeholder="Name">
    <input type="text" id="searchSurname" placeholder="Surname">
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
                                <th>Name/Link</th>
                                <th>Surname</th>
                                <th>Email</th>
                                <th>Answer</th>
                                <th>Status</th>
                                <th>Link Visited</th>
                                <th>Valid Until</th>
                                <th>Saved something</th>
                                <!--th>Rearm One</th>
                                <th>Delete One</th-->
                            </tr>
                        </thead>

                    <?php foreach($doctorals as $doctoral): ?>
                        <tr>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo Form::checkbox('checkbox_url[]', $doctoral->url_id, false, array('id'=>'checkbox'.$doctoral->url_id)); ?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?> fullButton'><?php echo Html::anchor($doctoral->unique_uri,$doctoral->name,array('target' => '_blank'));?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo $doctoral->surname;?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'><?php echo str_replace(",","<br/>",$doctoral->email); ?></td>
                            <td class='<?php echo ($doctoral->sent?"danger":"success");?>'>
                                <input id="clickMe<?php echo $doctoral->url_id;?>" type="button"  class="btn btn-primary" value="Answer" onclick="toggleShowRight('#clickMe<?php echo $doctoral->url_id;?>',' <?php echo $doctoral->custom_comment;?> ' ); return false;" />
                            </td>
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
                echo Html::anchor('admin/custom/emails', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning btn-block'));
                ?>
            </div>
            <div class="col-md-2">
                <?php echo Html::anchor('admin/custom/emails/edit/'.$email->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-primary btn-block')); ?>
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
                <?php echo Html::anchor('admin/custom/emails/rearm/'.$email->id, '<i class="glyphicon glyphicon-flash"></i> Rearm All!', array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")); ?>
            </div>
            <div class="col-md-2">
                <?php echo Html::anchor('admin/custom/emails/send/'.$email->id, '<i class="glyphicon glyphicon-send"></i> Send Now!', array('class' => 'btn btn-sm btn-danger btn-block','onclick' => "return confirm('Are you sure?')")); ?>
            </div>
        </div>
        <?php echo \Fuel\Core\Form::close(); ?>

</div>
