<div class="container">
<h2>Editing Professor</h2>
<br>

<?php echo render('admin/professors/_form',array("doctorals"=>$doctorals,"possesed_doctorals"=>$possesed_doctorals,"courses"=>$courses,"possesed_courses"=>$possesed_courses,"type"=>'edit')); ?>

</div>