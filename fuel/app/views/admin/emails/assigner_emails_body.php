<h2 style="margin:0 0 12px; font-family:Arial,sans-serif;">Exam invigilator assignments</h2>

<p style="font-family:Arial,sans-serif; margin:0 0 10px;">
  <strong>Exam period:</strong> <?php echo e($examperiod['label']); ?>
</p>

<p style="font-family:Arial,sans-serif; margin:0 0 10px;">
  Dear <strong><?php echo e($professor['surname'].' '.$professor['name']); ?></strong>,
</p>

<p style="font-family:Arial,sans-serif; margin:0 0 16px;">
  Below you can find the list of doctoral students assigned to the examinations of your course(s).
</p>

<?php foreach ($examcourses as $course): ?>
  <h3 style="font-family:Arial,sans-serif; margin:18px 0 6px;">
    <?php echo e($course['course_code']); ?> — <?php echo e($course['course_title']); ?>
  </h3>

  <p style="font-family:Arial,sans-serif; color:#777; margin:0 0 10px;">
    <strong>Date:</strong> <?php echo e($course['exam_day']); ?>
    &nbsp; | &nbsp;
    <strong>Time:</strong>
    <?php echo e(substr($course['exam_start'], 0, 5)); ?> – <?php echo e(substr($course['exam_end'], 0, 5)); ?>
  </p>

  <?php if (!empty($course['assigned_doctorals'])): ?>
    <table style="border-collapse:collapse; width:100%; font-family:Arial,sans-serif; margin:0 0 14px;">
      <thead>
        <tr>
          <th style="border:1px solid #ddd; padding:8px; text-align:left; width:60px;">ID</th>
          <th style="border:1px solid #ddd; padding:8px; text-align:left;">Name</th>
          <th style="border:1px solid #ddd; padding:8px; text-align:left;">Email</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($course['assigned_doctorals'] as $doc): ?>
        <tr>
          <td style="border:1px solid #ddd; padding:8px;"><?php echo (int)$doc['id']; ?></td>
          <td style="border:1px solid #ddd; padding:8px;"><?php echo e($doc['surname'].' '.$doc['name']); ?></td>
          <td style="border:1px solid #ddd; padding:8px;"><?php echo e($doc['email']); ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="font-family:Arial,sans-serif; color:#777; margin:0 0 14px;">
      No invigilators have been assigned to this examination.
    </p>
  <?php endif; ?>
<?php endforeach; ?>

<div style="margin-top:18px; padding-top:12px; border-top:1px solid #eee;">
  <p style="font-family:Arial,sans-serif; color:#777; margin:0;">
    This email was generated automatically by the DI Exam Scheduler.
  </p>
</div>
