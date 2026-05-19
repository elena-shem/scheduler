<div class="container">
<br>
    <p>
        <?php echo Html::anchor('admin/users/create', '<i class="glyphicon glyphicon-plus-sign"></i> Add new User', array('class' => 'btn btn-success')); ?>

    </p>
<?php if ($users): ?>
<div class="table-responsive">

    <table class="table  table-hover">
        <thead>
            <tr>
                <th>Username</th>

                <th>Group</th>
                <th>Email</th>
                <th>Last login</th>


                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($users as $item): ?>		<tr>

                <td><?php echo $item->username; ?></td>

                <td><?php echo Auth::group()->get_name($item->group); ?></td>
                <td><?php echo $item->email; ?></td>
                <td><?php echo gmdate("d-m-Y H:i:s",  $item->last_login); ?></td>


                <td>
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <?php echo Html::anchor('admin/users/view/'.$item->id, '<i class="glyphicon glyphicon-search"></i> View', array('class' => 'btn btn-sm btn-primary')); ?>
                            <?php echo Html::anchor('admin/users/edit/'.$item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-warning')); ?>
                            <?php echo Html::anchor('admin/users/delete/'.$item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>
                        </div>
                    </div>
                </td>
            </tr>
    <?php endforeach; ?>	</tbody>
    </table>
    <?php echo Pagination::instance('mypagination')->render(); ?>
</div>
<?php else: ?>
<p>No Users.</p>

<?php endif; ?>
</div>