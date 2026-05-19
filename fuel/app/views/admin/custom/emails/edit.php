<div class="container">
<h2>Editing Custom Email</h2>
<br>

<?php echo render('admin/custom/emails/_form',array("all_active_doctorals"=>$all_active_doctorals,"already_selected_doctoral_ids"=>$already_selected_doctoral_ids,"questions"=>$questions,"already_selected_question"=>$already_selected_question,"type"=>'edit')); ?>

</div>