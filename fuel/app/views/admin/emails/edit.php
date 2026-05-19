<div class="container">
<h2>Editing Email</h2>
<br>

<?php echo render('admin/emails/_form',array("all_active_doctorals"=>$all_active_doctorals,"already_selected_doctoral_ids"=>$already_selected_doctoral_ids,"exam_periods"=>$exam_periods,"already_selected_exam_period"=>$already_selected_exam_period,"type"=>'edit')); ?>

</div>