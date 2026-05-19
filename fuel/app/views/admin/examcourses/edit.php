<h2>Editing Examcourse</h2>
<br>

<?php echo render('admin/examcourses/_form'); ?>
<p>
	<?php echo Html::anchor('admin/examcourses/view/'.$examcourse->id, 'View'); ?> |
	<?php echo Html::anchor('admin/examcourses', 'Back'); ?></p>
