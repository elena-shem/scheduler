<style>
.report-table {
  border-collapse: collapse;
  margin: 12px 0;
  table-layout: auto;
  width: auto;
  max-width: 100%;
}
.report-table th,
.report-table td {
  border: 1px solid #000;
  padding: 6px 12px;
  text-align: center;
  white-space: nowrap;
}
.report-table td:last-child,
.report-table th:last-child {
  white-space: normal;
  text-align: left;
}
  .report-filter {
    margin: 8px 0 14px 0;
  }
</style>

<?php if (empty($years)): ?>
    <p><strong>No exam periods found.</strong></p>
    <?php return; ?>
<?php endif; ?>

<?php if (isset($examperiod) && $examperiod): ?>
    <p style="margin-top:0;">
        <strong>Exam period:</strong>
        <?php echo e($examperiod->getGreekSeason($examperiod->season) . " " . $examperiod->academic_year); ?>
    </p>
<?php endif; ?>

<form id="report-filter-form" method="get"
      action="<?php echo Uri::create('admin/reports/examperiod_noncompliance'); ?>"
      class="form-inline report-filter">

    <label for="year"><strong>Academic year:</strong></label>
    <select id="year" name="year" class="form-control">
        <?php foreach ($years as $y => $label): ?>
            <option value="<?php echo e($y); ?>" <?php echo ((string)$y === (string)$selected_year) ? 'selected="selected"' : ''; ?>>
                <?php echo e($label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="season" style="margin-left:10px;"><strong>Period:</strong></label>
    <select id="season" name="season" class="form-control">
        <?php
            $seasons = isset($seasons_by_year[$selected_year]) ? $seasons_by_year[$selected_year] : array();

            if (!empty($seasons) && (!isset($seasons[$selected_season])))
            {
                reset($seasons);
                $selected_season = key($seasons);
            }
        ?>
        <?php foreach ($seasons as $s => $s_label): ?>
            <option value="<?php echo e($s); ?>" <?php echo ((string)$s === (string)$selected_season) ? 'selected="selected"' : ''; ?>>
                <?php echo e($s_label); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<script>
(function() {
    var form = document.getElementById('report-filter-form');
    if (!form) return;

    var year = document.getElementById('year');
    var season = document.getElementById('season');

    if (year) {
        year.addEventListener('change', function() {
            if (season && season.options && season.options.length > 0) {
                season.selectedIndex = 0;
            }
            form.submit();
        });
    }
    if (season) {
        season.addEventListener('change', function() {
            form.submit();
        });
    }
})();
</script>

<div style="padding: 0 12px;">
    
    <?php if (!empty($report)): ?>
        <div style="margin-top: 15px; margin-bottom: 15px;">
            <?= Html::anchor(
                'admin/excel/doctorals/export_noncompliance_excel?year=' . urlencode($selected_year) . '&season=' . urlencode($selected_season),
                '<i class="glyphicon glyphicon-download"></i> Export to Excel',
                ['class' => 'btn btn-info']
            ); ?>
        </div>
    <?php endif; ?>

    <hr/>

    <?php if (empty($report)): ?>
        <p><strong>No non-compliance found for this exam period.</strong></p>
    <?php else: ?>

        <?php foreach ($report as $prof): ?>
            <h3>
                Professor: <?php echo e($prof['professor_name']); ?>
                (ID: <?php echo (int)$prof['professor_id']; ?>)
            </h3>

            <?php $has_no_response = !empty($prof['no_response']); ?>
            <?php $has_no_show = !empty($prof['no_show']); ?>

            <?php if (!$has_no_response && !$has_no_show): ?>
                <p>No issues.</p>
                <?php continue; ?>
            <?php endif; ?>

            <?php if ($has_no_response): ?>
                <h4> No response to availability declaration</h4>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Doctoral ID</th>
                            <th>Doctoral Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prof['no_response'] as $d): ?>
                            <tr>
                                <td><?php echo (int)$d['doctoral_id']; ?></td>
                                <td><?php echo e($d['doctoral_name']); ?></td>
                                <td><?php echo e($d['doctoral_email']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($has_no_show): ?>
                <h4>Did not attend scheduled supervision</h4>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Doctoral</th>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prof['no_show'] as $d): ?>
                            <?php foreach ($d['events'] as $ev): ?>
                                <tr>
                                    <td><?php echo e($d['doctoral_name']); ?></td>
                                    <td><?php echo (int)$d['doctoral_id']; ?></td>
                                    <td><?php echo e($d['doctoral_email']); ?></td>
                                    <td><?php echo e($ev['exam_day']); ?></td>
                                    <td>
                                        <?php
                                            $start = substr($ev['exam_start'], 0, 5);
                                            $end   = substr($ev['exam_end'], 0, 5);
                                            echo e($start . ' - ' . $end);
                                        ?>
                                    </td>
                                    <td><?php echo e($ev['course_code'] . ' — ' . $ev['course_title']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <hr/>
        <?php endforeach; ?>

    <?php endif; ?>
</div>