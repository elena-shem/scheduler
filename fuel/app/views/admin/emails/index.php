<div class="container">
<br>
    <p>
        <?php echo Html::anchor('admin/emails/create', '<i class="glyphicon glyphicon-plus-sign"></i> Add new Email', array('class' => 'btn btn-success')); ?>

    </p>
<?php if ($emails): ?>
<div class="table-responsive">
    <table class="table  table-hover">
        <thead>
            <tr>
                <th>Email #</th>
                <th>Identification Title</th>
                <th>Subject</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($emails as $item): ?>
        <tr>
            <td><?php echo $item->id; ?></td>
            <td><?php echo $item->title; ?></td>
            <td><?php echo $item->subject; ?></td>
            <td>
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <?php echo Html::anchor('admin/emails/view/'.$item->id, '<i class="glyphicon glyphicon-search"></i> View', array('class' => 'btn btn-sm btn-primary')); ?>
                        <?php echo Html::anchor('admin/emails/edit/'.$item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-warning')); ?>
                        <?php echo Html::anchor('admin/emails/delete/'.$item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>
                    </div>
                </div>

            </td>
        </tr>
    <?php endforeach; ?>	</tbody>
    </table>
</div>
<?php else: ?>
<p>No Emails.</p>

<?php endif; ?>
</div>