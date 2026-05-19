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
      'admin/professors',
      '<i class="glyphicon glyphicon-arrow-left"></i> Active Professors',
      ['class' => 'btn btn-success']
    ); ?>

  </div>

  <hr>

  <div class="quick-search-block">
    <h5>Quick Search: </h5>
    <input type="text" id="searchId" placeholder="Id">
    <input type="text" id="searchSurname" placeholder="Surname">
    <input type="text" id="searchName" placeholder="Name">
    <input type="text" id="searchEmail" placeholder="Email">
    <input type="text" id="searchTelephone" placeholder="Telephone">
    <input type="text" id="searchOffice" placeholder="Office">
  </div>

  <div class="row">
    <div class="col-lg-12">
      <?php if (!empty($professors)): ?>
        <div class="table-responsive">
          <div class="table-scroll" id="professorsTableScroll">
            <table class="table table-hover sortable" id="sortabletable">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>Surname</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Telephone</th>
                  <th>Office</th>
                  <th>Deleted at</th>
                  <th></th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($professors as $item): ?>
                  <tr id="<?php echo (int)$item->id; ?>">
                    <td><?php echo (int)$item->id; ?></td>
                    <td><?php echo e($item->surname); ?></td>
                    <td><?php echo e($item->name); ?></td>
                    <td><?php echo e($item->email); ?></td>
                    <td><?php echo e($item->telephone); ?></td>
                    <td><?php echo e($item->office); ?></td>
                    <td><?php echo $item->deleted_at ? date('Y-m-d H:i', (int)$item->deleted_at) : '-'; ?></td>

                    <td>
                      <div class="btn-toolbar">
                        <div class="btn-group">
                          <?php echo Html::anchor(
                            'admin/professors/restore/' . (int)$item->id,
                            '<i class="glyphicon glyphicon-repeat"></i> Restore',
                            array(
                              'class' => 'btn btn-sm btn-success',
                              'onclick' => "return confirm('Restore this professor?')"
                            )
                          ); ?>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
          </div>
        </div>
      <?php else: ?>
        <p>No deleted professors</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
(function () {
  function norm(v){ return (v || '').toString().toLowerCase().trim(); }

  function filterRows() {
    var qId   = norm(document.getElementById('searchId').value);
    var qSur  = norm(document.getElementById('searchSurname').value);
    var qName = norm(document.getElementById('searchName').value);
    var qEm   = norm(document.getElementById('searchEmail').value);
    var qTel  = norm(document.getElementById('searchTelephone').value);
    var qOff  = norm(document.getElementById('searchOffice').value);

    var rows = document.querySelectorAll('#sortabletable tbody tr');
    rows.forEach(function(row){
      var tds = row.querySelectorAll('td');

      var idTxt   = norm(tds[0] ? tds[0].innerText : '');
      var surTxt  = norm(tds[1] ? tds[1].innerText : '');
      var nameTxt = norm(tds[2] ? tds[2].innerText : '');
      var emTxt   = norm(tds[3] ? tds[3].innerText : '');
      var telTxt  = norm(tds[4] ? tds[4].innerText : '');
      var offTxt  = norm(tds[5] ? tds[5].innerText : '');

      var ok =
        (!qId   || idTxt.indexOf(qId) !== -1) &&
        (!qSur  || surTxt.indexOf(qSur) !== -1) &&
        (!qName || nameTxt.indexOf(qName) !== -1) &&
        (!qEm   || emTxt.indexOf(qEm) !== -1) &&
        (!qTel  || telTxt.indexOf(qTel) !== -1) &&
        (!qOff  || offTxt.indexOf(qOff) !== -1);

      row.style.display = ok ? '' : 'none';
    });
  }

  ['searchId','searchSurname','searchName','searchEmail','searchTelephone','searchOffice']
    .forEach(function(id){
      var el = document.getElementById(id);
      if (el) el.addEventListener('input', filterRows);
    });
})();
</script>