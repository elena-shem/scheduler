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

  /* comment */
  .comment-preview, .comment-full, .comment-rest {
    white-space: normal;
    word-break: normal;
    overflow-wrap: normal;    
    word-wrap: normal;
  }

  .comment-toggle-link {
    font-weight: 600;
    margin-left: 6px;
    cursor: pointer;
  }

  td.col-comment { text-align: left; }
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
      'admin/excel/doctorals/export_graduated_excel',
      '<i class="glyphicon glyphicon-download"></i> Export Graduated',
      ['class' => 'btn btn-info']
    ); ?>

  </div>

  <hr>

  <div class="quick-search-block">
    <h5>Quick Search:</h5>
    <input type="text" id="searchId" placeholder="Id">
    <input type="text" id="searchAM" placeholder="AM">
    <input type="text" id="searchSurname" placeholder="Surname">
    <input type="text" id="searchName" placeholder="Name">
    <input type="text" id="searchProfessor" placeholder="Professor">
    <input type="text" id="searchEmail" placeholder="Email">
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

  // режем так, чтобы не обрывать слово возле "More"
  $first = mb_substr($decoded, 0, $limit, 'UTF-8');
  $cutPos = mb_strrpos($first, ' ', 0, 'UTF-8');

  if ($cutPos !== false && $cutPos > 20) {
    $first = mb_substr($decoded, 0, $cutPos, 'UTF-8');
    $rest  = ltrim(mb_substr($decoded, $cutPos, null, 'UTF-8'));
  } else {
    $rest  = ltrim(mb_substr($decoded, $limit, null, 'UTF-8'));
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
                  <th class="col-comment">Comment</th>
                  <th></th>
                </tr>
              </thead>

              <tbody>
              <?php foreach ($doctorals as $item): ?>
                <tr id="<?php echo (int)$item->id; ?>">
                  <td><?php echo (int)$item->id; ?></td>
                  <td><?php echo e($item->am); ?></td>
                  <td><?php echo e($item->surname); ?></td>
                  <td><?php echo e($item->name); ?></td>

                  <td>
                    <?php
                      if (!empty($item->professors)) {
                        $sup_names = array_map(function($p) {
                          return $p->surname . ' ' . $p->name;
                        }, $item->professors);
                        echo implode('<br/>', array_map('e', $sup_names));
                      } else {
                        echo '<span class="text-muted">—</span>';
                      }
                    ?>
                  </td>

                  <td><?php echo str_replace(",", "<br/>", e($item->email)); ?></td>
                  <td><?php echo e($item->registrationdate); ?></td>

                  <td class="col-telephone">
                    <?php
                      $prettyTel = Util\Telephone::beautify($item->telephone);
                      echo str_replace([",", ";"], "<br/>", e($prettyTel));
                    ?>
                  </td>

                  <td class="col-comment">
                    <?= render_comment_preview($item->id, $item->comment, 80); ?>
                  </td>

                  <td>
                    <div class="btn-toolbar">
                      <div class="btn-group">
                        <?php echo Html::anchor(
                          'admin/doctorals/view/' . $item->id,
                          '<i class="glyphicon glyphicon-search"></i> View',
                          array('class' => 'btn btn-sm btn-primary')
                        ); ?>

                        <?php echo Html::anchor(
                          'admin/doctorals/edit/' . $item->id,
                          '<i class="glyphicon glyphicon-wrench"></i> Edit',
                          array('class' => 'btn btn-sm btn-warning')
                        ); ?>

                        <?php echo Html::anchor(
                          'admin/doctorals/delete/' . $item->id,
                          '<i class="glyphicon glyphicon-trash"></i> Delete',
                          array(
                            'class' => 'btn btn-sm btn-danger',
                            'onclick' => "return confirm('Are you sure?')"
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
        <p>No graduated doctorals</p>
      <?php endif; ?>

    </div>
  </div>
</div>