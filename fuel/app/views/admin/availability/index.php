<script>
  window.URI_BASE = "<?php echo Uri::base(false); ?>";
</script>

    <div class="container-fluid">
      <div class="container">

        <?php echo Form::open(['id' => 'availabilityForm', 'class' => 'form-horizontal']); echo Form::csrf(); ?>

    <div class="row filters-row" style="margin-bottom: 15px;">
      <div class="col-md-3 col-md-offset-3">
        <div class="form-group">
          <?php echo Form::label('Exam Year', 'exam_year', ['class'=>'control-label']); ?>
          <input type="text" id="exam_year" name="exam_year" class="form-control"
            value="<?php echo e(Input::get('exam_year', Input::post('exam_year',''))); ?>"
            placeholder="e.g. 2023-2024">
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <?php echo Form::label('Exam Period', 'season', ['class'=>'control-label']); ?>
          <select name="season" id="season" class="form-control">
            <option value=""></option>
            <?php foreach ($examperiod_options as $season_key => $row): ?>
              <option value="<?php echo e($season_key); ?>"
                <?php
                  $selected = Input::get('season', Input::post('season',''));
                  echo ($selected === $season_key) ? 'selected' : '';
                ?>
              >
                <?php echo e($row['label']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="col-md-2 calendar-wrapper">
          <button type="button" id="openCalendarBtn" class="btn btn-calendar">
              <i class="glyphicon glyphicon-calendar"></i>
              Calendar
          </button>
      </div>
    </div>

    <input type="hidden" id="form_examperiod" name="examperiod_id" value="">

    <?php echo Form::close(); ?>

<script>
  var EXAM_MAP = <?php echo json_encode($examperiod_map, JSON_UNESCAPED_UNICODE); ?>;

  function normalizeYear(y) {
    return (y || "")
      .trim()
      .replace(/\s+/g,'')
      .replace('/','-')
      .replace('–','-')
      .replace('—','-');
  }

  function updateExamperiodId() {
    var season = document.getElementById('season').value;
    var year = normalizeYear(document.getElementById('exam_year').value);

    var id = (EXAM_MAP[season] && EXAM_MAP[season][year]) ? EXAM_MAP[season][year] : '';
    var hidden = document.getElementById('form_examperiod');
    hidden.value = id;

    var container = document.getElementById('data-container');
    if (!id) {
      if (container) container.innerHTML = '';
      return;
    }

    hidden.dispatchEvent(new Event('change', { bubbles: true }));
  }

function openCalendar() {
  var year = normalizeYear(document.getElementById('exam_year').value);
  var season = document.getElementById('season').value;

  var url = window.URI_BASE + 'admin/availabilitycalendar';

  var params = [];
  if (year)   params.push('exam_year=' + encodeURIComponent(year));
  if (season) params.push('season=' + encodeURIComponent(season));

  if (params.length) url += '?' + params.join('&');

  window.location.href = url;
}

  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('season').addEventListener('change', updateExamperiodId);
    document.getElementById('exam_year').addEventListener('input', updateExamperiodId);
    document.getElementById('openCalendarBtn').addEventListener('click', openCalendar);

    updateExamperiodId();
  });
</script>

<script>
  // decode HTML entities (&Nu;... -> Ν...)
  function decodeHtmlEntities(str) {
    var txt = document.createElement('textarea');
    txt.innerHTML = str || '';
    return txt.value;
  }

  function escapeHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

function buildCommentCell(id, comment, limit) {
  limit = limit || 80;

  if (!comment || !comment.length) return '—';

  var decoded = decodeHtmlEntities(comment).replace(/\s+/g, ' ').trim();
  if (!decoded) return '—';

  if (decoded.length <= limit) {
    return '<span class="comment-full">' + escapeHtml(decoded) + '</span>';
  }

  var first = decoded.slice(0, limit);
  var rest = '';

  var cut = first.lastIndexOf(' ');
  if (cut > 20) {
    first = decoded.slice(0, cut);
    rest  = decoded.slice(cut).trimStart();
  } else {
    rest  = decoded.slice(limit).trimStart();
  }

  var cid = 'cmt_' + id;

  return ''
    + '<span class="comment-preview">' + escapeHtml(first) + '</span> '
    + '<span id="'+cid+'" class="collapse comment-rest">' + escapeHtml(rest) + '</span> '
    + '<a class="comment-toggle-link" role="button" data-toggle="collapse" data-target="#'+cid+'" aria-controls="'+cid+'" aria-expanded="false">More</a>';
}

$(document).on('click', 'a.comment-toggle-link', function (e) {
  e.preventDefault();

  var $link = $(this);
  var target = $link.data('target');
  var $box = $(target);

  var expanded = ($link.attr('aria-expanded') === 'true');

  if (expanded) {
    $link.text('More').attr('aria-expanded', 'false');
  } else {
    $link.text('Hide').attr('aria-expanded', 'true');
  }

  $box.collapse('toggle');
});
  
</script>

    <h5>Quick Search: </h5>
    <input type="text" id="searchId" placeholder="Id">
    <input type="text" id="searchSurname" placeholder="Surname">
    <input type="text" id="searchName" placeholder="Name">
    
    <hr>
        <div class="row" style="margin-bottom: 100px;">
            <div class="col-lg-12">
               <table class="table table-hover sortable" id="sortabletable">
                    <thead>
                        <tr>
                        <th id="lolcat1">Id</th>
                        <th>Surname</th>
                        <th>Name</th>
                        <th>Supervisors</th>
                        <th>Density</th>
                        <th>Daily Density</th>
                        <th class="col-comment">Comment</th>
                        </tr>
                    </thead>

                    <tbody id="data-container"></tbody>
                    </table>

                    <script id="tableTmpl" type="text/x-jsrender">
                      <tr>
                          <td>{{:id}}</td>
                          <td id="docSurname{{:id}}">{{:surname}}</td>
                          <td id="docName{{:id}}">{{:name}}</td>
                          <td style="max-width: 50px;">{{:professors}}</td>
                          <td>{{:density}}</td>
                          <td>{{:density_day}}</td>
                          <td class="col-comment">{{:commentHtml}} </td>
                      </tr>
                    </script>
                    </tbody>
                </table>
            </div>
        </div>

</div>
</div>