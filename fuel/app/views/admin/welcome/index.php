
<br>
    <p>
    <?php echo Html::anchor(
        'admin/welcome/create',
        '<i class="glyphicon glyphicon-plus-sign"></i> Add new announcement',
        array('class' => 'btn btn-success')
    ); ?>


    </p>
    <?php  // fetch a previously forged instance, and render it
    echo Pagination::instance('announcements_pagination')->render();
    ?>
<?php if (isset($welcomes)): ?>
    <div class="table-responsive">
        <table class="table  table-hover">
            <thead>
            <tr>
                <th>Content</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($welcomes as $item): ?>
                <tr>

                    <td class="note-td-width"><p><?php echo $item->text; ?></p></td>
                    <td>
                        <div class="btn-toolbar">
                            <div class="btn-group">
                                <?php echo Html::anchor('admin/welcome/edit/' . $item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-warning')); ?>
                                <?php echo Html::anchor('admin/welcome/delete/' . $item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>                    </div>
                        </div>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <p>No announcements</p>

<?php endif; ?>



