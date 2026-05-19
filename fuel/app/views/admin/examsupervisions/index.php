<div class="container-fluid">

    <div class="btn-toolbar top-buffer" role="toolbar">
        <?= Html::anchor('admin/examsupervisions/create', '<i class="glyphicon glyphicon-plus-sign"></i> Add New Supervision', array('class'=>'btn btn-success')) ?>
    </div>

    <?php if ($supervisions): ?>
        <div class="table-responsive top-buffer">
            <table class="table table-hover sortable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctoral</th>
                        <th>Course</th>
                        <th>Hours</th>
                        <th>Attended</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($supervisions as $s): ?>
                    <tr>
                        <td><?= $s->id ?></td>
                        <td><?= $s->doctoral->surname . ' ' . $s->doctoral->name ?></td>
                        <td><?= $s->examcourse->course_id ?></td>
                        <td><?= $s->hours ?></td>
                        <td><?= $s->attended ? 'Yes' : 'No' ?></td>
                        <td>
                            <div class="btn-group">
                                <?= Html::anchor(
                                    'admin/examsupervisions/edit/'.$s->id,
                                    '<i class="glyphicon glyphicon-wrench"></i> Edit',
                                    array('class' => 'btn btn-sm btn-warning')
                                ) ?>

                                <?= Html::anchor(
                                    'admin/examsupervisions/delete/'.$s->id,
                                    '<i class="glyphicon glyphicon-trash"></i> Delete',
                                    array(
                                        'class' => 'btn btn-sm btn-danger',
                                        'onclick' => "return confirm('Are you sure?')"
                                    )
                                ) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No supervisions found.</p>
    <?php endif; ?>
</div>