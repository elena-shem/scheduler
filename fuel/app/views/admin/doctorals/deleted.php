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

<script>
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

<div class="container-fluid">
  <br>

  <div class="top-button-bar" style="margin-bottom: 10px; display: flex; gap: 12px; flex-wrap: wrap;">

    <?= Html::anchor(
      'admin/doctorals',
      '<i class="glyphicon glyphicon-arrow-left"></i> Active Doctorals',
      ['class' => 'btn btn-success']
    ); ?>

    <?= Html::anchor(
      'admin/excel/doctorals/export_deleted_excel',
      '<i class="glyphicon glyphicon-download"></i> Export Deleted',
      ['class' => 'btn btn-info']
    ); ?>

  </div>

  <hr>

  <div class="quick-search-block">
    <h5>Quick Search: </h5>
    <input type="text" id="searchId" placeholder="Id">
    <input type="text" id="searchAM" placeholder="AM">
    <input type="text" id="searchSurname" placeholder="Surname">
    <input type="text" id="searchName" placeholder="Name">
    <input type="text" id="searchProfessor" placeholder="Professor">
    <input type="text" id="searchEmail" placeholder="Email">
    <input type="text" id="searchHoursRemaining" placeholder="Remaining Hours">
  </div>

<?php
function render_comment_preview($id, $comment, $limit = 80) {
  $comment = trim((string)$comment);

  if ($comment === '' || $comment === '-') {
    return '<span class="text-muted">—</span>';
  }

  $decoded = html_entity_decode($comment, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $decoded = preg_replace("/\r\n|\r|\n/", " ", $decoded);

  $collapseId = 'cmt_' . (int)$id;

  if (mb_strlen($decoded, 'UTF-8') <= $limit) {
    return '<span class="comment-full">'
      . htmlspecialchars($decoded, ENT_QUOTES, 'UTF-8')
      . '</span>';
  }

  $first = mb_substr($decoded, 0, $limit, 'UTF-8');

  $cutPos = mb_strrpos($first, ' ', 0, 'UTF-8');
  if ($cutPos !== false && $cutPos > 20) {
    $first = mb_substr($decoded, 0, $cutPos, 'UTF-8');
    $rest  = ltrim(mb_substr($decoded, $cutPos, null, 'UTF-8'));
  } else {
    $rest  = mb_substr($decoded, $limit, null, 'UTF-8');
    $rest  = ltrim($rest);
  }

  return
    '<span class="comment-preview">'
      . htmlspecialchars($first, ENT_QUOTES, 'UTF-8')
    . '</span> '
    . '<span id="'.$collapseId.'" class="collapse comment-rest">'
      . htmlspecialchars($rest, ENT_QUOTES, 'UTF-8')
    . '</span> '
    . '<a class="comment-toggle-link" role="button" data-toggle="collapse" data-target="#'.$collapseId.'" aria-controls="'.$collapseId.'" aria-expanded="false">More</a>';
}
?>

  <div class="row">
    <div class="col-lg-12">
      <?php if (!empty($doctorals)): ?>
        <div class="table-responsive">
          <div class="table-scroll" id="doctoralsTableScroll">
            <table class="table table-hover sortable" id="sortabletable">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>AM</th>
                  <th>Surname</th>
                  <th>Name</th>
                  <th>Professors</th>
                  <th>Email</th>
                  <th>Registration Date</th>
                  <th class="col-telephone">Telephone</th>
                  <th>Hours remaining</th>
                  <th>Hours completed</th>
                  <th class="col-comment">Comment</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($doctorals as $item): ?>
                  <tr id="<?php echo $item->id; ?>">
                    <td><?php echo $item->id; ?></td>
                    <td><?php echo $item->am; ?></td>
                    <td><?php echo $item->surname; ?></td>
                    <td><?php echo $item->name; ?></td>

                    <td>
                      <?php
                        if (!empty($item->professors)) {
                          $sup_names = array_map(function($p) {
                            return $p->surname . ' ' . $p->name;
                          }, $item->professors);
                          echo implode('<br/>', $sup_names);
                        } else {
                          echo '-';
                        }
                      ?>
                    </td>

                    <td><?php echo str_replace(",", "<br/>", $item->email); ?></td>
                    <td><?php echo $item->registrationdate; ?></td>

                    <td class="col-telephone">
                      <?php
                        $prettyTel = Util\Telephone::beautify($item->telephone);
                        echo str_replace([",", ";"], "<br/>", $prettyTel);
                      ?>
                    </td>

                    <td><?php echo $item->hours_remaining; ?></td>
                    <td><?php echo $item->hours_completed; ?></td>

                    <td class="col-comment">
                      <?= render_comment_preview($item->id, $item->comment, 80); ?>
                    </td>
                        <td>
                          <div class="btn-toolbar">
                            <div class="btn-group">
                              <?php
                                echo Html::anchor(
                                  'admin/doctorals/restore/' . $item->id,
                                  '<i class="glyphicon glyphicon-repeat"></i> Restore',
                                  array(
                                    'class' => 'btn btn-sm btn-success',
                                    'onclick' => "return confirm('Restore this doctoral?')"
                                  )
                                );
                              ?>
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
        <p>No deleted doctorals</p>
      <?php endif; ?>
    </div>
  </div>
</div>