<div class="container">
    <h2><?php echo $professor->name . ' ' . $professor->surname; ?></h2>

    <p>
        <strong>Id:</strong>
        <?php echo $professor->id; ?></p>

    <p>
        <strong>Name:</strong>
        <?php echo $professor->name; ?></p>

    <p>
        <strong>Surname:</strong>
        <?php echo $professor->surname; ?></p>

    <p>
        <strong>Email:</strong>
        <?php echo $professor->email; ?></p>

    <p>
        <strong>Telephone:</strong>
        <?php echo Util\Telephone::beautify($professor->telephone); ?></p>

    <p>
        <strong>Office:</strong>
        <?php echo $professor->office; ?></p>

    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                        Doctorals
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
                            <th>Hours Remaining</th>
                            <th>Hours Completed</th>
                        </tr>
                        </thead>
                        <?php foreach ($doctorals as $doctoral): ?>
                            <tr class="doctoral-row" data-doctoralId="<?php echo $doctoral->id; ?>">
                                <td><?php echo $doctoral->name; ?></td>
                                <td><?php echo $doctoral->surname; ?></td>
                                <td><?php echo $doctoral->email; ?></td>
                                <td><?php echo $doctoral->hours_remaining; ?></td>
                                <td><?php echo $doctoral->hours_completed; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                        Courses
                    </a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Number of Supervisors Winter</th>
                            <th>Number of Supervisors Summer</th>
                            <th>Number of Supervisors September</th>

                        </tr>
                        </thead>
                        <?php foreach ($courses as $course): ?>
                            <tr class="course-row" data-courseId="<?php echo $course->id; ?>">
                                <td> <?php echo $course->code; ?></td>
                                <td> <?php echo $course->title; ?></td>
                                <td> <?php echo $course->number_of_supervisors_winter; ?></td>
                                <td> <?php echo $course->number_of_supervisors_summer; ?></td>
                                <td> <?php echo $course->number_of_supervisors_september; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <?php echo Html::anchor('admin/professors/', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning')); ?>
    <?php echo Html::anchor('admin/professors/edit/' . $professor->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-primary')); ?>


</div>