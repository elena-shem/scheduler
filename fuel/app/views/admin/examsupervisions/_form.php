<fieldset>
    <?php
    $is_edit = isset($type) && $type === 'edit';
    ?>

<!-- Doctorals (multiple) -->
<div class="form-group">
    <?php echo Form::label('Doctorals', 'doctoral_id_0', ['class'=>'control-label']); ?>

    <?php
        $doctorals_options = ['' => ''];
        foreach ($doctorals as $d) {
            $doctorals_options[$d->id] = "{$d->surname} {$d->name} (ID: {$d->id})";
        }

        $posted_doctorals = Input::post('doctoral_id', null);
        if (!is_array($posted_doctorals) || count($posted_doctorals) === 0) {
            if ($is_edit && isset($supervision)) {
                $posted_doctorals = [ $supervision->doctoral_id ];
            } else {
                $posted_doctorals = [ '' ];
            }
        }

        $posted_attended = Input::post('attended', null);
        if (!is_array($posted_attended) || count($posted_attended) === 0) {
            if ($is_edit && isset($supervision)) {
                $posted_attended = array( isset($supervision->attended) ? (int)$supervision->attended : 1 );
            } else {
                $posted_attended = array(1);
            }
        }

        $posted_comments = Input::post('comment', null);
        if (!is_array($posted_comments) || count($posted_comments) === 0) {
            if ($is_edit && isset($supervision)) {
                $posted_comments = array( isset($supervision->comment) ? (string)$supervision->comment : '' );
            } else {
                $posted_comments = array('');
            }
        }
        ?>
        
        <div id="doctorals_rows">
        <?php foreach ($posted_doctorals as $i => $val): ?>
            <?php
                $att_val = isset($posted_attended[$i]) ? $posted_attended[$i] : 1;
                $comment_val = isset($posted_comments[$i]) ? $posted_comments[$i] : '';
            ?>

            <div class="doctoral-row" style="display:flex; gap:8px; margin-bottom:8px; align-items:flex-start;">

            <!-- Doctor -->
            <div style="flex:1;">
                <?= Form::select(
                    'doctoral_id[]',
                    $val,
                    $doctorals_options,
                    ['class'=>'form-control select2-searchable', 'data-placeholder'=>'Select a doctoral']
                ); ?>
            </div>

            <!-- Attended -->
            <div style="width:170px;">
                <?= Form::select(
                    'attended[]',
                    $att_val,
                    [1 => 'Yes', 0 => 'No'],
                    ['class'=>'form-control attended-select']
                ); ?>
            </div>

            <!-- Comment -->
            <div style="flex:1;">
                <?= Form::textarea(
                    'comment[]',
                    $comment_val,
                    array(
                        'class' => 'form-control',
                        'rows'  => 1,
                        'placeholder' => 'Comment (optional)'
                    )
                ); ?>
            </div>

            <!-- Remove button -->
            <button type="button" class="btn btn-xs btn-danger remove-doctoral" title="Remove"
                    style="position:relative; z-index:5;">×</button>
            </div>

        <?php endforeach; ?>
            </div>
        <?php if (!$is_edit): ?>
            <button type="button" class="btn btn-xs btn-info" id="add_doctoral">
                + Add doctoral
            </button>
        <?php endif; ?>
    </div>

   <?php if ($is_edit && isset($supervision)): ?>
    
    <?php
    $ec       = $supervision->examcourse;
    $course   = $ec->course;
    $period   = $ec->examperiod;
    $examday  = $ec->examday;
    $examhour = $ec->examhour;

    $course_title = $course
        ? html_entity_decode($course->title, ENT_QUOTES, 'UTF-8')
        : '';
    ?>

    <!-- Exam Year (read-only) -->
    <div class="form-group">
        <?php echo Form::label('Exam Year', 'exam_year_readonly', ['class'=>'control-label']); ?>
        <input type="text"
               id="exam_year_readonly"
               class="col-md-4 form-control"
               value="<?php echo $period ? htmlspecialchars($period->academic_year, ENT_QUOTES, 'UTF-8') : ''; ?>"
               readonly>
    </div>


    <!-- Exam Period (read-only) -->
    <div class="form-group">
        <?php echo Form::label('Exam Period', 'examperiod_readonly', ['class'=>'control-label']); ?>
        <input type="text"
               id="examperiod_readonly"
               class="col-md-4 form-control"
               value="<?php echo $period ? htmlspecialchars(ucfirst($period->season), ENT_QUOTES, 'UTF-8') : ''; ?>"
               readonly>
    </div>

    <!-- Exam Course (read-only) -->
    <div class="form-group">
        <?php echo Form::label('Exam Course', 'examcourse_readonly', ['class'=>'control-label']); ?>
        <input type="text"
               id="examcourse_readonly"
               class="col-md-4 form-control"
               value="<?php echo htmlspecialchars($course_title, ENT_QUOTES, 'UTF-8'); ?>"
               readonly>
    </div>

<!-- Exam Day (read-only) -->
    <div class="form-group">
        <?php echo Form::label('Exam Day', 'exam_day_readonly', ['class'=>'control-label']); ?>
        <input type="text"
               id="exam_day_readonly"
               class="col-md-4 form-control"
               value="<?php 
                   echo !empty($supervision->custom_exam_day) 
                       ? htmlspecialchars($supervision->custom_exam_day, ENT_QUOTES, 'UTF-8') 
                       : ($examday ? htmlspecialchars($examday->day, ENT_QUOTES, 'UTF-8') : ''); 
               ?>"
               readonly>
    </div>

    <!-- Exam Hour (read-only) -->
    <div class="form-group">
        <?php echo Form::label('Exam Hour', 'exam_hour_readonly', ['class'=>'control-label']); ?>
        <input type="text"
               id="exam_hour_readonly"
               class="col-md-4 form-control"
               value="<?php
                if (!empty($supervision->custom_exam_hour)) {
                       echo html_entity_decode($supervision->custom_exam_hour, ENT_QUOTES, 'UTF-8');
                   } elseif ($examhour) {
                       $hour_range = $examhour->start . '–' . $examhour->end;
                       echo html_entity_decode($hour_range, ENT_QUOTES, 'UTF-8');
                   }
               ?>"
               readonly>
    </div>

        <!-- examcourse_id -->
        <?= Form::hidden('examcourse_id', $supervision->examcourse_id); ?>

    <?php else: ?>

    
    <?php
    $examperiod_options = [];

    foreach ($examperiods as $p) {
        $examperiod_options[$p->season] = [
            'id'   => $p->id
        ];
    }

    ksort($examperiod_options);
    ?>

    <!-- Exam Year -->
    <div class="form-group">
        <?php echo Form::label('Exam Year', 'exam_year', ['class'=>'control-label']); ?>
        <input type="text" id="exam_year" name="exam_year" class="col-md-4 form-control"
            value="<?php 
            echo Input::post(
                'exam_year',
                isset($supervision) 
                    ? $supervision->examcourse->examperiod->academic_year 
                    : ''
            ); 
        ?>"
            placeholder="e.g. 2023-2024">
    </div>

    <!-- Exam Period -->
    <div class="form-group">
        <?php echo Form::label('Exam Period', 'examperiod_id', ['class'=>'control-label']); ?>
        <select name="examperiod_id" id="examperiod_id" class="col-md-4 form-control select2-searchable"
                data-placeholder="Select an exam period">
            <option value=""></option>

            <?php foreach ($examperiod_options as $season => $data): ?>
                <option 
                    value="<?php echo $season; ?>"
                    <?php 
                        $selected = Input::post(
                            'examperiod_id',
                            isset($supervision) ? $supervision->examcourse->examperiod->season : ''
                        );
                        echo ($selected === $season) ? 'selected' : '';
                    ?>
                >
                    <?php echo ucfirst($season); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

<?php
if (!isset($examcourses_options) || !is_array($examcourses_options) || empty($examcourses_options)) {

    $examcourses_options = [];

    foreach ($examcourses as $e) {
        $course = $e->course;
        if (!$course) continue;

        $title = $course->title;

        if (!isset($examcourses_options[$title])) {
            $examcourses_options[$title] = [];
        }

        $examcourses_options[$title][] = [
            'id'     => $e->id,
            'day'    => $e->examday ? $e->examday->day : '',
            'hour'   => $e->examhour ? ($e->examhour->start . '–' . $e->examhour->end) : '',
            'year'   => $e->examperiod->academic_year,
            'season' => $e->examperiod->season
        ];
    }

    ksort($examcourses_options, SORT_STRING | SORT_FLAG_CASE);
}
?>

<!-- Exam Course -->
<div class="form-group">
    <?php echo Form::label('Exam Course', 'examcourse_id', ['class'=>'control-label']); ?>
    <select name="examcourse_title" id="examcourse_id" 
            class="col-md-4 form-control select2-searchable"
            data-placeholder="Select exam course">

        <option value=""></option>

                <?php
                $selected_id = Input::post('examcourse_id', isset($supervision) ? $supervision->examcourse_id : null);
                $selected_id = $selected_id !== null ? (string)$selected_id : null;
                ?>

                <?php foreach ($examcourses_options as $title => $variants):
                    $variants_json = htmlspecialchars(json_encode($variants), ENT_QUOTES, 'UTF-8');

                    $is_selected = false;
                    if ($selected_id) {
                        foreach ($variants as $v) {
                            if ((string)$v['id'] === $selected_id) { $is_selected = true; break; }
                        }
                    }
                ?>
            <option
                value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
                data-variants="<?= $variants_json ?>"
                <?= $is_selected ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<input type="hidden" name="examcourse_id" id="examcourse_id_real"
       value="<?= Input::post('examcourse_id', isset($supervision) ? $supervision->examcourse_id : '') ?>">

<!-- Exam Day -->
<div class="form-group">
    <?php echo Form::label('Exam Day', 'exam_day', ['class'=>'control-label']); ?>
    <input type="text" id="exam_day" name="exam_day" class="col-md-4 form-control" placeholder="e.g.: 2015-06-25">
</div>

<!-- Exam Hour -->
<div class="form-group">
    <?php echo Form::label('Exam Hour', 'exam_hour', ['class'=>'control-label']); ?>
    <input type="text" id="exam_hour" name="exam_hour" class="col-md-4 form-control" placeholder="e.g.: 12:00–15:00">
    <div style="clear:both;"></div>
    <small style="color: #737373; margin-top: 5px; display: block;">
    </small>
</div>
<?php endif; ?>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    // ---- SELECT2 ----
    if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) {
        console.error('jQuery or Select2 are not loaded');
        return;
    }

    var $ = jQuery;
    var isV4 = !!$.fn.select2.amd; 

    $('select.select2-searchable').each(function () {
        var $el = $(this);

        if ($el.data('select2')) {
            try { $el.select2('destroy'); } catch (e) {}
        }

        var opts = {
            width: '100%',
            allowClear: true,
            minimumResultsForSearch: 0,
            placeholder: $el.data('placeholder') || ''
        };

        // matcher
        if (isV4) {
            opts.matcher = function (params, data) {
                var term = $.trim(params.term || '').toLowerCase();
                if (term === '') return data;
                if (!data || !data.text) return null;

                var text = $.trim((data.text || '').toLowerCase());
                return text.indexOf(term) === 0 ? data : null;
            };
        } else {
            opts.matcher = function (term, text) {
                term = $.trim(term || '').toLowerCase();
                if (term === '') return true;

                text = $.trim((text || '').toLowerCase());
                return text.indexOf(term) === 0;
            };
        }

        $el.select2(opts);
    });


// ---- DAY/HOUR SUBSTITUTION ----
    <?php if (!$is_edit): ?>

    var select       = document.getElementById('examcourse_id');
    var realIdInput  = document.getElementById('examcourse_id_real');
    var dayInput     = document.getElementById('exam_day');
    var hourInput    = document.getElementById('exam_hour');
    var yearInput    = document.getElementById('exam_year');
    var periodSelect = document.getElementById('examperiod_id');

    var lastAutoDay = '';
    var lastAutoHour = '';

    function clearFields() {
        dayInput.value    = '';
        hourInput.value   = '';
        realIdInput.value = '';
        lastAutoDay       = '';
        lastAutoHour      = '';
    }

    function updateFields() {
        var selectedOption = select.querySelector('option:checked');

        if (!selectedOption || !selectedOption.value) {
            clearFields();
            return;
        }

        var currentYear   = (yearInput.value || '').trim();
        var currentSeason = (periodSelect.value || '').trim();

        var variants;
        try {
            variants = JSON.parse(selectedOption.getAttribute('data-variants') || '[]');
        } catch (e) {
            variants = [];
        }

        if (!Array.isArray(variants) || variants.length === 0) {
            clearFields();
            return;
        }

        var foundVariant = variants.find(function(v) {
            var vYear   = (v.year   || '').trim();
            var vSeason = (v.season || '').trim();
            return vYear === currentYear && vSeason === currentSeason;
        });

        if (!foundVariant) {
            foundVariant = variants.find(function(v) {
                var vYear = (v.year || '').trim();
                return vYear === currentYear;
            });
        }

        // Фоллбек: берем первый доступный ID, если точного совпадения нет
        if (!foundVariant) {
            foundVariant = variants;
        }

        realIdInput.value = foundVariant.id || '';

        var newAutoDay = foundVariant.day || '';
        
        // Преобразование HTML-сущностей и обрезка секунд
        var newAutoHour = (foundVariant.hour || '')
            .replace(/&ndash;|&#8211;|&#x2013;|&mdash;|&#8212;|&#x2014;/g, '–')
            .replace(/:[0-9]{2}(?=\s*[-–—]|\s*$)/g, '');

        // Подстановка только в том случае, если пользователь не вводил данные вручную
        if (dayInput.value === '' || dayInput.value === lastAutoDay) {
            dayInput.value = newAutoDay;
            lastAutoDay = newAutoDay;
        }
        
        if (hourInput.value === '' || hourInput.value === lastAutoHour) {
            hourInput.value = newAutoHour;
            lastAutoHour = newAutoHour;
        }
    }

    $('#examcourse_id').on('change', updateFields);
    $('#examperiod_id').on('change', updateFields);
    $('#exam_year').on('input', updateFields);

    updateFields();
    
    <?php endif; ?>
});
document.addEventListener('DOMContentLoaded', function () {
  var container = document.getElementById('doctorals_rows');
if (!container) return;

    var addBtn = document.getElementById('add_doctoral');
  if (addBtn) {
    addBtn.addEventListener('click', function () {
      var firstRow = container.querySelector('.doctoral-row');
      if (!firstRow) return;

      var clone = firstRow.cloneNode(true);
      clone.querySelectorAll('span.select2').forEach(function(el){ el.remove(); });

      var sel = clone.querySelector('select[name="doctoral_id[]"]');
      if (sel) {
        sel.value = '';
        sel.classList.remove('select2-hidden-accessible');
        sel.removeAttribute('data-select2-id');
        sel.removeAttribute('tabindex');
        sel.removeAttribute('aria-hidden');
      }

      var att = clone.querySelector('select[name="attended[]"]');
      if (att) att.value = '1';

      var comment = clone.querySelector('textarea[name="comment[]"]');
      if (comment) comment.value = '';

      container.appendChild(clone);
      initSelect2InRow(clone);
      enforceUniqueDoctorals();
    });
  }
  function initSelect2ForSelect(sel) {
    if (!window.jQuery || !jQuery.fn || !jQuery.fn.select2) return;
    var $ = jQuery;

    var $sel = $(sel);

    $sel.closest('.doctoral-row').find('span.select2').remove();
    $sel.removeClass('select2-hidden-accessible');
    $sel.removeAttr('data-select2-id tabindex aria-hidden');
    $sel.find('option').removeAttr('data-select2-id');

    var isV4 = !!$.fn.select2.amd;

    var opts = {
      width: '100%',
      allowClear: true,
      minimumResultsForSearch: 0,
      placeholder: $sel.data('placeholder') || ''
    };

    if (isV4) {
      opts.matcher = function (params, data) {
        if (!data) return null;
        if (data.element && data.element.disabled) return null;

        var term = $.trim(params.term || '').toLowerCase();
        if (term === '') return data;

        var text = $.trim((data.text || '').toLowerCase());
        return text.indexOf(term) === 0 ? data : null;
      };
    } else {
      opts.matcher = function (term, text, opt) {
        if (opt && opt.disabled) return false;

        term = $.trim(term || '').toLowerCase();
        if (term === '') return true;

        text = $.trim((text || '').toLowerCase());
        return text.indexOf(term) === 0;
      };
    }

    $sel.select2(opts);
  }

  function initSelect2InRow(row) {
    row.querySelectorAll('select.select2-searchable').forEach(function(sel){
      initSelect2ForSelect(sel);
    });
  }

  function enforceUniqueDoctorals() {
    var selects = Array.from(container.querySelectorAll('select[name="doctoral_id[]"]'));

    var chosen = new Set();
    selects.forEach(function(sel){
      var v = (sel.value || '').trim();
      if (v) chosen.add(v);
    });

    selects.forEach(function(sel){
      var my = (sel.value || '').trim();

      Array.prototype.forEach.call(sel.options, function(opt){
        var val = (opt.value || '').trim();
        if (!val) { opt.disabled = false; return; }
        opt.disabled = (val !== my) && chosen.has(val);
      });

      if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
        jQuery(sel).trigger('change.select2');
      }
    });
  }

  container.addEventListener('click', function (e) {
    var btn = e.target.closest('.remove-doctoral');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    var row = btn.closest('.doctoral-row');
    var rows = container.querySelectorAll('.doctoral-row');

    if (rows.length <= 1) {
      var sel = row.querySelector('select[name="doctoral_id[]"]');
      if (sel) {
        sel.value = '';
        if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
          jQuery(sel).val('').trigger('change');
        }
      }
      enforceUniqueDoctorals();
      
      return;
    }

    row.remove();
    enforceUniqueDoctorals();
    
  });

  container.addEventListener('change', function (e) {
    if (e.target && (e.target.matches('select[name="doctoral_id[]"]') || e.target.matches('select[name="attended[]"]'))) {
      enforceUniqueDoctorals();
      
    }
  });

  // initial
  enforceUniqueDoctorals();
  
});

</script>

</fieldset>

