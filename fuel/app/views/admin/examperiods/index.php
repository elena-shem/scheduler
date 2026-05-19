<div class="container-fluid">
<br>
<p>
    <?php echo Html::anchor('admin/examperiods/create', '<i class="glyphicon glyphicon-plus-sign"></i> Add new Exam Period', array('class' => 'btn btn-success')); ?>
</p>
<?php if ($examperiods): ?>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Season</th>
			<th>Academic year</th>
			<th>Start</th>
			<th>End</th>
			<th>Comment</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
        <?php foreach ($examperiods as $item): ?>
        <tr>
			<td><?php echo $item->getGreekSeason($item->season); ?></td>
			<td><?php echo $item->academic_year; ?></td>
            <td><?php echo Util\Dateformatter::serverToclient($item->start); ?></td>
            <td><?php echo Util\Dateformatter::serverToclient($item->end); ?></td>
			<td><?php echo $item->comment; ?></td>
			<td>
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <?php echo Html::anchor('admin/examperiods/view/'.$item->id, '<i class="glyphicon glyphicon-search"></i> View', array('class' => 'btn btn-small btn-primary')); ?>
                        <?php echo Html::anchor('admin/examperiods/schedule/'.$item->id, '<i class="glyphicon glyphicon-calendar"></i> Schedule', array('class' => 'btn btn-small btn-info')); ?>
                        <?php echo Html::anchor('admin/examperiods/edit/'.$item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-small btn-warning')); ?>
                        <?php echo Html::anchor('admin/examperiods/delete/'.$item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-small btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>
                    </div>
                </div>
			</td>
		</tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No Examperiods.</p>
<?php endif; ?>
</div>