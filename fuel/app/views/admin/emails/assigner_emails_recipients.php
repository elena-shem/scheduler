<p>
    <strong>Exam period:</strong>
    <?php echo e($examperiod['label']); ?>
</p>
<p>
    <a class="btn btn-default"
       href="<?php echo Uri::create('admin/assigner_emails'); ?>">
        Back
    </a>

    <a class="btn btn-primary"
       href="<?php echo Uri::create('admin/assigner_emails/preview/'.$examperiod['id']); ?>">
        Refresh
    </a>
</p>

<hr>

<form method="post"
      action="<?php echo Uri::create('admin/assigner_emails/send/'.$examperiod['id']); ?>"
      onsubmit="return confirm('Send emails to ALL listed professors for this exam period? This cannot be undone.');"
      style="margin: 10px 0;">
    <?php echo \Form::csrf(); ?>
    <button type="submit" class="btn btn-danger">
        Send emails to <?php echo (int)count($by_professor); ?> professor(s)
    </button>
</form>

<?php if (empty($by_professor)): ?>
    <p><strong>No recipients found.</strong></p>
<?php else: ?>
    <p>Total professors: <strong><?php echo count($by_professor); ?></strong></p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Professor</th>
                <th>Email</th>
                <th>Examcourses</th>
                <th>Preview email</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($by_professor as $pid => $row): ?>
            <tr>
                <td><?php echo e($row['professor']['surname'].' '.$row['professor']['name']); ?></td>
                <td><?php echo e($row['professor']['email']); ?></td>
                <td><?php echo (int) count($row['examcourses']); ?></td>
                <td>
                    <a href="<?php echo Uri::create('admin/assigner_emails/preview/'.$examperiod['id'].'?professor_id='.$pid); ?>">
                        Open email preview
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
