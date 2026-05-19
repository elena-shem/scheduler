<style>
  .top-button-bar .btn { min-width: 180px; text-align: center; }
</style>

<div class="container-fluid">
  <br>

  <div class="row">
    <div class="col-lg-12">

      <div class="top-button-bar" style="display:flex; gap:12px; margin-bottom:10px;">
        <?= Html::anchor(
          'admin/courses/create',
          '<i class="glyphicon glyphicon-plus-sign"></i> Add new Course',
          ['class' => 'btn btn-success']
        ); ?>

        <?= Html::anchor(
          'admin/excel/courses/export_excel',
          '<i class="glyphicon glyphicon-download"></i> Export Excel',
          ['class' => 'btn btn-info']
        ); ?>

        <button class="btn btn-danger"
                type="button"
                data-toggle="collapse"
                data-target="#collapseImport"
                aria-expanded="false"
                aria-controls="collapseImport">
          <i class="glyphicon glyphicon-upload"></i> Import Courses
        </button>

        <?php echo Html::anchor('admin/courses/deleted',
        '<i class="glyphicon glyphicon-trash"></i> Deleted',
        ['class' => 'btn btn-primary']); ?>
      </div>

      <div class="collapse import-danger" id="collapseImport">
        <div class="import-panel">

          <div class="import-warning">
            <div class="import-warning__title">
              <i class="glyphicon glyphicon-warning-sign"></i> Warning
            </div>
            <div class="import-warning__text">
              This action <u>will replace ALL existing Course records</u>.
              Use only if you intend to fully reload the database from Excel.
            </div>
          </div>

          <form action="<?= Uri::create('admin/excel/common/upload/courses'); ?>"
                method="post"
                enctype="multipart/form-data">

            <div id="uploadConsole" class="import-upload">
              <input type="file" name="fileToUpload" id="fileToUpload" accept=".xlsx,.xls">

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <?= Fuel\Core\Form::submit(
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
    <h5>Quick Search: </h5>
    <input type="text" id="searchId" placeholder="Id">
    <input type="text" id="searchOfCourseId" placeholder="Official Course Id">    
    <input type="text" id="searchCode" placeholder="Code">
    <input type="text" id="searchCode2" placeholder="Code2">
    <input type="text" id="searchTitle" placeholder="Title">
    <input type="text" id="searchProfessor" placeholder="Professor">
    <input type="text" id="searchWinter" placeholder="Winter">
    <input type="text" id="searchSummer" placeholder="Summer">
    <input type="text" id="searchSeptember" placeholder="September">

    <div class="row top-buffer">
        <div class="col-lg-12">
            <?php if ($courses): ?>
                <div class="table-responsive">
                    <table class="table table-hover sortable" id="sortabletable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Official Course Id</th>
                            <th>Code</th>
                            <th>Code2</th>
                            <th>Title</th>
                            <th>Professors</th>
                            <th>Sups Winter</th>
                            <th>Sups Summer</th>
                            <th>Sups September</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($courses as $item): ?>
                            <?php if($item->title == 'DUMMY' && $item->code=='DUM'): continue; ?>

                            <?php else: ?>
                                <tr id="<?php echo $item->id; ?>">

                                    <td><?php echo $item->id; ?></td>
                                    <td><?php echo $item->special_id; ?></td>
                                    <td><?php echo $item->code; ?></td>
                                    <td><?php echo $item->code2; ?></td>
                                    <td><?php echo $item->title; ?></td>
                                    <td><?php  echo (!empty($item->professors))? implode(', ',array_map(function($prof){return $prof->name . ' ' . $prof->surname;},$item->professors)) : '-'; ?></td>
                                    <td><?php echo $item->number_of_supervisors_winter; ?></td>
                                    <td><?php echo $item->number_of_supervisors_summer; ?></td>
                                    <td><?php echo $item->number_of_supervisors_september; ?></td>
                                    <td>
                                        <div class="btn-toolbar">
                                            <div class="btn-group">
                                                <?php echo Html::anchor('admin/courses/view/' . $item->id, '<i class="glyphicon glyphicon-search"></i> View', array('class' => 'btn btn-sm btn-primary')); ?>
                                                <?php echo Html::anchor('admin/courses/edit/' . $item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-warning')); ?>
                                                <?php echo Html::anchor('admin/courses/delete/' . $item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>    </tbody>
                    </table>
                </div>

            <?php else: ?>
                <p>No Courses.</p>

            <?php endif; ?><p>
                <?php echo Html::anchor('admin/courses/create', '<i class="glyphicon glyphicon-plus-sign"></i> Add new Course', array('class' => 'btn btn-success')); ?>

            </p>
        </div>
    </div>
</div>