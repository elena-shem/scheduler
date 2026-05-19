<div class="container">
<h2>Editing Course</h2>
<br>

<?php echo render('admin/courses/_form',array("professors"=>$professors,"possesed_professors"=>$possesed_professors,"type"=>'edit')); ?>
</div>