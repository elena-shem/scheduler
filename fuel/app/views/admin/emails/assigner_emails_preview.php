<p>
    <strong>Exam period:</strong> <?php echo e($examperiod['label']); ?>
    <br>
    <strong>Professor:</strong> <?php echo e($professor['surname'].' '.$professor['name']); ?>
    (<?php echo e($professor['email']); ?>)
</p>

<form method="post"
      action="<?php echo Uri::create('admin/assigner_emails/send_one/'.$examperiod['id'].'/'.$professor['id']); ?>"
      onsubmit="return confirm('Send this email to <?php echo e($professor['surname'].' '.$professor['name']); ?>?');"
      style="margin-bottom: 10px;">
    <?php echo \Form::csrf(); ?>
    <button type="submit" class="btn btn-danger">Send to this professor</button>

    <a class="btn btn-default"
       href="<?php echo Uri::create('admin/assigner_emails/preview/'.$examperiod['id']); ?>">
        Back to recipients
    </a>
</form>

<hr>

<!-- Вставляем реальный HTML письма -->
<div style="border:1px solid #ddd; padding:10px; background:#fff;">
    <?php echo $email_html; ?>
</div>
