<div class="container">
<br>
    <p>
        <?php echo Html::anchor('admin/custom/questions/create', '<i class="glyphicon glyphicon-plus-sign"></i> Add new question', array('class' => 'btn btn-success')); ?>

    </p>
<?php if ($questions): ?>
<div class="table-responsive">
    <table class="table  table-hover">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>

                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($questions as $item): ?>		<tr>

                 <td><?php echo $item->id; ?></td>
                <td><?php echo $item->title; ?></td>


                <td>
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <?php echo Html::anchor('admin/custom/questions/view/'.$item->id, '<i class="glyphicon glyphicon-search"></i> View', array('class' => 'btn btn-sm btn-primary')); ?>
                            <?php echo Html::anchor('admin/custom/questions/edit/'.$item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-warning')); ?>
                            <?php echo Html::anchor('admin/custom/questions/delete/'.$item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>
                        </div>
                    </div>
                </td>
            </tr>
    <?php endforeach; ?>	</tbody>
    </table>
</div>
<?php else: ?>
<p>No Custom Questions.</p>

<?php endif; ?>
</div>