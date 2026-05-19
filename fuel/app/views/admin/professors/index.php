<style>
    .top-button-bar .btn {
        min-width: 180px;  
        text-align: center; 
    }
</style>

<div class="container-fluid">
  <br>

  <div class="row">
    <div class="col-lg-12">

      <!-- ACTIONS BAR -->
      <div class="top-button-bar" style="display:flex; gap:12px;">

        <?= Html::anchor(
          'admin/professors/create',
          '<i class="glyphicon glyphicon-plus-sign"></i> Add new Professor',
          ['class' => 'btn btn-success']
        ); ?>

        <?= Html::anchor(
          'admin/excel/professors/export_excel',
          '<i class="glyphicon glyphicon-download"></i> Export Excel',
          ['class' => 'btn btn-info']
        ); ?>

        <button class="btn btn-danger"
                type="button"
                data-toggle="collapse"
                data-target="#collapseImport"
                aria-expanded="false"
                aria-controls="collapseImport">
          <i class="glyphicon glyphicon-upload"></i> Import Professors
        </button>

        <?= Html::anchor(
          'admin/professors/deleted',
          '<i class="glyphicon glyphicon-trash"></i> Deleted Professors',
          ['class' => 'btn btn-primary']
        ); ?>

      </div>
      <!-- IMPORT PANEL -->
      <div class="collapse import-danger" id="collapseImport">
        <div class="import-panel">

          <div class="import-warning">
            <div class="import-warning__title">
              <i class="glyphicon glyphicon-warning-sign"></i> Warning
            </div>
            <div class="import-warning__text">
              This action <u>will replace ALL existing Professor records</u>.
              Use only if you intend to fully reload the database from Excel.
            </div>
          </div>

          <form action="<?= Uri::create('admin/excel/common/upload/professors'); ?>"
                method="post"
                enctype="multipart/form-data">

            <div id="uploadConsole" class="import-upload">

              <input type="file"
                    name="fileToUpload"
                    id="fileToUpload"
                    accept=".xlsx,.xls">

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <?= Fuel\Core\Form::submit(
                      'submit',
                      'Upload & import Professors',
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

    </div>
  </div>

    <hr>
    <h5>Quick Search: </h5>
    <input type="text" id="searchId" placeholder="Id">
    <input type="text" id="searchSurname" placeholder="Surname">
    <input type="text" id="searchName" placeholder="Name">
    <input type="text" id="searchEmail" placeholder="Email">
    <input type="text" id="searchTelephone" placeholder="Telephone">
    <input type="text" id="searchOffice" placeholder="Office">

    <div class="row top-buffer">
        <div class="col-lg-12">
            <?php if ($professors): ?>
                <div class="table-responsive">
                    <table class="table  table-hover sortable" id="sortabletable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Surname</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Office</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($professors as $item): ?>
                            <tr id="<?php echo $item->id; ?>">

                                <td><?php echo $item->id; ?></td>
                                <td><?php echo $item->surname; ?></td>
                                <td><?php echo $item->name; ?></td>
                                <td><?php echo $item->email; ?></td>
                                <td><?php echo Util\Telephone::beautify($item->telephone); ?></td>
                                <td><?php echo $item->office; ?></td>
                                <td>
                                    <div class="btn-toolbar">
                                        <div class="btn-group">
                                            <?php echo Html::anchor('admin/professors/view/' . $item->id, '<i class="glyphicon glyphicon-search"></i> View', array('class' => 'btn btn-sm btn-primary')); ?>
                                            <?php echo Html::anchor('admin/professors/edit/' . $item->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-warning')); ?>
                                            <?php echo Html::anchor('admin/professors/delete/' . $item->id, '<i class="glyphicon glyphicon-trash"></i> Delete', array('class' => 'btn btn-sm btn-danger', 'onclick' => "return confirm('Are you sure?')")); ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>    </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No Professors.</p>

            <?php endif; ?>
        </div>
    </div>
</div>
