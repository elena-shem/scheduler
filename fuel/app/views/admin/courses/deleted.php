<style>
  .quick-search-block { margin-bottom: 30px; }
  .top-button-bar .btn { min-width: 180px; text-align: center; }

  #sortabletable thead th,
  #sortabletable thead th.sortable,
  #sortabletable thead th.sorted,
  #sortabletable thead th.asc,
  #sortabletable thead th.desc,
  #sortabletable thead th.headerSortUp,
  #sortabletable thead th.headerSortDown {
    font-weight: 600 !important;
  }
</style>

<div class="container-fluid">
  <br>

  <div class="top-button-bar" style="margin-bottom: 10px; display: flex; gap: 12px; flex-wrap: wrap;">

    <?= Html::anchor(
      'admin/courses',
      '<i class="glyphicon glyphicon-arrow-left"></i> Active Courses',
      ['class' => 'btn btn-success']
    ); ?>

  </div>

  <hr>

  <div class="quick-search-block">
    <h5>Quick Search: </h5>
    <input type="text" id="searchId" placeholder="Id">
    <input type="text" id="searchSpecialId" placeholder="Special Id">
    <input type="text" id="searchCode" placeholder="Code">
    <input type="text" id="searchTitle" placeholder="Title">
  </div>

  <div class="row">
    <div class="col-lg-12">
      <?php if (!empty($courses)): ?>
        <div class="table-responsive">
          <div class="table-scroll" id="coursesTableScroll">
            <table class="table table-hover sortable" id="sortabletable">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>Special Id</th>
                  <th>Code</th>
                  <th>Title</th>
                  <th>Deleted at</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($courses as $item): ?>
                  <tr id="<?php echo $item->id; ?>">
                    <td><?php echo $item->id; ?></td>
                    <td><?php echo e($item->special_id); ?></td>
                    <td><?php echo e($item->code); ?></td>
                    <td><?php echo e($item->title); ?></td>
                    <td>
                      <?php
                        echo $item->deleted_at
                          ? date('Y-m-d H:i', (int)$item->deleted_at)
                          : '-';
                      ?>
                    </td>

                    <td>
                      <?php echo Html::anchor(
                        'admin/courses/restore/' . $item->id,
                        '<i class="glyphicon glyphicon-repeat"></i> Restore',
                        array(
                          'class' => 'btn btn-sm btn-success',
                          'onclick' => "return confirm('Restore this course?')"
                        )
                      ); ?>
                    </td>

                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php else: ?>
        <p>No deleted courses</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
(function () {
  function normalize(s) {
    return (s || '').toString().toLowerCase().trim();
  }

  function filterTable() {
    var idQ       = normalize(document.getElementById('searchId').value);
    var specialQ  = normalize(document.getElementById('searchSpecialId').value);
    var codeQ     = normalize(document.getElementById('searchCode').value);
    var titleQ    = normalize(document.getElementById('searchTitle').value);

    var rows = document.querySelectorAll('#sortabletable tbody tr');

    rows.forEach(function (row) {
      var cells = row.querySelectorAll('td');

      var idTxt      = normalize(cells[0]?.innerText);
      var specialTxt = normalize(cells[1]?.innerText);
      var codeTxt    = normalize(cells[2]?.innerText);
      var titleTxt   = normalize(cells[3]?.innerText);

      var ok =
        (!idQ      || idTxt.includes(idQ)) &&
        (!specialQ || specialTxt.includes(specialQ)) &&
        (!codeQ    || codeTxt.includes(codeQ)) &&
        (!titleQ   || titleTxt.includes(titleQ));

      row.style.display = ok ? '' : 'none';
    });
  }

  ['searchId','searchSpecialId','searchCode','searchTitle']
    .forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.addEventListener('input', filterTable);
    });
})();
</script>