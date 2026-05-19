<div class="container-fluid">
    <p class="text-muted">
        The following professors have doctoral students who have not replied to their latest request.
    </p>

<?php if (!empty($professors_list)): ?>
        <div style="display: flex; gap: 15px; align-items: flex-start; margin-bottom: 20px;">
            
            <form method="post" action="<?php echo Uri::create('admin/doctorals/notify_all_supervisors'); ?>" 
                  onsubmit="return confirm('Notify ALL listed supervisors about their pending students?');" 
                  style="margin: 0;">
                <?php echo \Form::csrf(); ?>
                <button type="submit" class="btn btn-danger">
                    Notify ALL supervisors
                </button>
            </form>

            <?= Html::anchor(
                'admin/excel/doctorals/export_not_responded_excel',
                '<i class="glyphicon glyphicon-download"></i> Export to Excel',
                ['class' => 'btn btn-info']
            ); ?>
            
        </div>
        <hr>
    <?php endif; ?>

    <?php if (!empty($professors_list)): ?>
        <div class="table-responsive">
            <table class="table table-hover sortable" id="sortabletable">
                <thead>
                <tr>
                    <th>Professor Surname</th>
                    <th>Professor Name</th>
                    <th>Email</th>
                    <th>Pending Students Count</th>
                    <th>Students List</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($professors_list as $prof_id => $data): ?>
                    <tr>
                        <td><?= $data['professor']->surname ?></td>
                        <td><?= $data['professor']->name ?></td>
                        <td><?= $data['professor']->email ?></td>
                        
                        <td>
                            <span class="label label-danger"><?= count($data['doctorals']) ?> pending</span>
                        </td>

                        <td>
                            <ul class="list-unstyled" style="margin-bottom: 0;">
                                <?php foreach ($data['doctorals'] as $doc): ?>
                                    <li><small class="text-muted"><?= $doc->surname . ' ' . $doc->name ?> (<?= $doc->email ?>)</small></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>

                        <td>
                            <a href="<?php echo Uri::create('admin/doctorals/notify_single_supervisor/'.$prof_id); ?>"
                               class="btn btn-sm btn-warning"
                               onclick="return confirm('Send grouped email to <?= $data['professor']->surname ?>?');">
                                Notify Supervisor
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <p class="text-success">
            All doctoral students have responded. No action required.
        </p>

    <?php endif; ?>
</div>