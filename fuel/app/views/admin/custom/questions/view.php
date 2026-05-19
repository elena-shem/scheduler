<div class="container">
<h3>Viewing Custom Question #<?php echo $question->id; ?></h3>

<p>
	<strong>Title:</strong>
	<?php echo $question->title; ?></p>
    <h5><strong>Content: </strong></h5>
<div class="well">
    <?php echo $question->question_html; ?>
</div>
















    <?php echo Html::anchor('admin/custom/questions/', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning')); ?>
    <?php echo Html::anchor('admin/custom/questions/edit/'.$question->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-primary')); ?>


</div>