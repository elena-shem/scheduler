<div class="container">
    <h2><?php echo $course->title;; ?></h2>


    <p>
        <strong>Id:</strong>
        <?php echo $course->id; ?></p>

    <p>
        <strong>Official Course Id:</strong>
        <?php echo $course->special_id; ?></p>

    <p>
        <strong>Code:</strong>
        <?php echo $course->code; ?></p>
    <p>
        <strong>Code2:</strong>
        <?php echo $course->code2; ?></p>

    <p>
        <strong>Title:</strong>
        <?php echo $course->title; ?></p>

    <p>
        <strong>Number of supervisors Winter:</strong>
        <?php echo $course->number_of_supervisors_winter; ?></p>

    <p>
        <strong>Number of supervisors Summer:</strong>
        <?php echo $course->number_of_supervisors_summer; ?></p>

    <p>
        <strong>Number of supervisors September:</strong>
        <?php echo $course->number_of_supervisors_september; ?></p>


    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                        Professors
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in">
                <div class="panel-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                        <?php foreach ($professors as $professor): ?>
                            <tr id="<?php echo $professor->id;?>">
                                <td><?php echo $professor->name; ?></td>
                                <td><?php echo $professor->surname; ?></td>
                                <td><?php echo $professor->email; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <?php echo Html::anchor('admin/courses/', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning')); ?>
    <?php echo Html::anchor('admin/courses/edit/' . $course->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-primary')); ?>

</div>