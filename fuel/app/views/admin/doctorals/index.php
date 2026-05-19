<style>

    .quick-search-block {
        margin-bottom: 30px;
    }

    .top-button-bar .btn {
        min-width: 180px;
        text-align: center;
    }

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

    <div class="top-button-bar" style="margin-bottom: 10px; display:flex; justify-content:space-between; align-items:center;">

  <div class="btn-left" style="display:flex; gap:12px;">
    <?= Html::anchor(
        'admin/doctorals/create',
        '<i class="glyphicon glyphicon-plus-sign"></i> Add new Doctoral',
        ['class' => 'btn btn-success']
    ); ?>

    <?= Html::anchor(
        'admin/excel/doctorals/export_excel',
        '<i class="glyphicon glyphicon-download"></i> Export Excel',
        ['class' => 'btn btn-info']
    ); ?>

    <button class="btn btn-danger"
            type="button"
            data-toggle="collapse"
            data-target="#collapseImport"
            aria-expanded="false"
            aria-controls="collapseImport">
        <i class="glyphicon glyphicon-upload"></i> Import Doctorals
    </button>
  </div>

  <div class="btn-right" style="display:flex; gap:12px;">
    <?= Html::anchor(
        'admin/doctorals/graduated',
        '<i class="glyphicon glyphicon-ok-circle"></i> Graduated',
        ['class' => 'btn btn-primary']
    ); ?>

    <?= Html::anchor(
        'admin/doctorals/deleted',
        '<i class="glyphicon glyphicon-trash"></i> Deleted',
        ['class' => 'btn btn-primary']
    ); ?>
  </div>

    </div>
    <div class="collapse import-danger" id="collapseImport">
        <div class="import-panel">
        <div class="import-warning">
            <div class="import-warning__title">
                <i class="glyphicon glyphicon-warning-sign"></i> Warning
            </div>
            <div class="import-warning__text">
                This action <u>will replace ALL existing Doctoral records</u>.
                Use only if you intend to fully reload the database from Excel.
            </div>
        </div>

        <form action="<?php echo Uri::create('admin/excel/common/upload/doctorals'); ?>" method="post"
            enctype="multipart/form-data">

            <div id="uploadConsole" class="import-upload">

                <input type="file"
                    name="fileToUpload"
                    id="fileToUpload"
                    accept=".xlsx,.xls">

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo Fuel\Core\Form::submit(
                                'submit',
                                'Upload and import',
                                [
                                    'class' => 'btn btn-danger btn-block top-buffer',
                                    'id' => 'uploadBtn',
                                    'onclick' => "return confirm('Are you sure? This will replace everything in the table!')"
                                ]
                            ); ?>
                        </div>
                    </div>
                </div>

            </div>
        </form>
      </div>
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
            <?php if ($doctorals): ?>
                <div class="table-responsive" >
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
                            <th>Αποστολή email</th>
                            <th>Αναστολή</th>
                            <!--th>Active</th-->
                            <th>Hours remaining</th>
                            <th>Hours completed</th>
                            <th>Max Assignments</th>
                            <th>Bonus Weight</th>
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
                                <td><?php echo($item->sendemail ? "NAI" : "OXI"); ?></td>
                                <td><?php echo($item->suspended ? "NAI" : "OXI"); ?></td>
                                <!--td><?php //echo ($item->active ? "Active" : "Not Active"); ?></td-->
                                <td><?php echo $item->hours_remaining; ?></td>
                                <td><?php echo $item->hours_completed; ?></td>
                                <td><?php echo $item->max_assignments; ?></td>
                                <td><?php echo $item->bonus_weight; ?></td>
                                <td class="col-comment">
                                    <?= render_comment_preview($item->id, $item->comment, 80); ?>
                                </td>

                                <td>
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <?php echo Html::anchor('admin/doctorals/view/' . $item->id, '<i class="glyphicon glyphicon-search"></i> View', array('class' => 'btn btn-sm btn-primary')); ?>
                                            <?php echo Html::anchor('admin/doctorals/edit/' . $item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-warning')); ?>
                                            <?php echo Html::anchor('admin/doctorals/delete/' . $item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>    </tbody>
                    </table>
                </div>
                </div>

            <?php else: ?>
                <p>No Doctorals</p>

            <?php endif; ?>
        </div>
    </div>
</div>
</div>