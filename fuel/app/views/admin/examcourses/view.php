<h2>Viewing #<?php echo $examcourse->id; ?></h2>

<p>
	<strong>Period id:</strong>
	<?php echo $examcourse->period_id; ?></p>
<p>
	<strong>Course id:</strong>
	<?php echo $examcourse->course_id; ?></p>
<p>
	<strong>Day:</strong>
	<?php echo $examcourse->day; ?></p>
<p>
	<strong>Hour:</strong>
	<?php echo $examcourse->hour; ?></p>

<?php echo Html::anchor('admin/examcourses/edit/'.$examcourse->id, 'Edit'); ?> |
<?php echo Html::anchor('admin/examcourses', 'Back'); ?>