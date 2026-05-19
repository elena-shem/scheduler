<div class="container">
<h2>Viewing #<?php echo $examperiod->id; ?></h2>

<p>
	<strong>Season:</strong>
	<?php echo $examperiod->getGreekSeason($examperiod->season); ?></p>
<p>
	<strong>Academic year:</strong>
	<?php echo $examperiod->academic_year; ?></p>
<p>
	<strong>Start:</strong>
	<?php echo Util\Dateformatter::serverToclient($examperiod->start); ?></p>
<p>
	<strong>End:</strong>
	<?php echo Util\Dateformatter::serverToclient($examperiod->end); ?></p>

<!--p>
	<strong><a href="<?php //echo Uri::create('admin/examperiods/getschedule/:id',array('id'=> $examperiod->id)); ?>">Download Schedule in XML format - other formats comming soon</a></strong>
</p-->
<p>
	<strong>Comment:</strong>
	<?php echo $examperiod->comment; ?></p>
<p>
    <strong>Hour ranges:</strong>
    <?php foreach($examperiod->examhours as $examhour): ?>
    <br />
    <span>
        <?php echo $examhour['start']; ?> - 
        <?php echo $examhour['end']; ?>
    </span>
    <?php endforeach; ?>
</p>
    
<br />

 <?php echo Html::anchor('admin/examperiods/', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-small btn-default')); ?>
 <?php echo Html::anchor('admin/examperiods/schedule/'.$examperiod->id, '<i class="glyphicon glyphicon-calendar"></i> Schedule', array('class' => 'btn btn-small btn-info')); ?>
 <?php echo Html::anchor('admin/examperiods/edit/'.$examperiod->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-small btn-warning')); ?>
</div>
