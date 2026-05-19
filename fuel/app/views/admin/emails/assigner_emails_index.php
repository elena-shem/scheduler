<h3>Select exam period</h3>

<form method="get" action="<?php echo Uri::create('admin/assigner_emails'); ?>" class="form-inline" style="margin:10px 0;">
    <label><strong>Year:</strong></label>
    <select name="year" class="form-control" onchange="this.form.submit()">
        <?php foreach ($years as $y => $label): ?>
            <option value="<?php echo e($y); ?>" <?php echo ((string)$y === (string)$selected_year) ? 'selected' : ''; ?>>
                <?php echo e($label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label style="margin-left:10px;"><strong>Period:</strong></label>
    <select name="season" class="form-control" onchange="this.form.submit()">
        <?php
            $seasons = isset($seasons_by_year[$selected_year]) ? $seasons_by_year[$selected_year] : array();
        ?>
        <?php foreach ($seasons as $s => $s_label): ?>
            <option value="<?php echo e($s); ?>" <?php echo ((string)$s === (string)$selected_season) ? 'selected' : ''; ?>>
                <?php echo e($s_label); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (empty($selected_examperiod_id)): ?>
    <p><strong>No exam period found for selected filters.</strong></p>
<?php else: ?>
    <p>
        Selected:
        <strong><?php echo e($seasons_by_year[$selected_year][$selected_season] . ' ' . $selected_year); ?></strong>
    </p>

    <p>
        <a class="btn btn-primary"
           href="<?php echo Uri::create('admin/assigner_emails/preview/'.$selected_examperiod_id); ?>">
            Preview recipients
        </a>
    </p>
<?php endif; ?>
