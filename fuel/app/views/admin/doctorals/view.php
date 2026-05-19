<div class="container">
    <h2><?php echo $doctoral->name.' '.$doctoral->surname; ?></h2>

    <p>
        <strong>Id:</strong>
        <?php echo $doctoral->id; ?></p>

    <p>
        <strong>AM:</strong>
        <?php echo $doctoral->am; ?></p>

    <p>
        <strong>Name:</strong>
        <?php echo $doctoral->name; ?></p>

    <p>
        <strong>Surname:</strong>
        <?php echo $doctoral->surname; ?></p>

    <p>
        <strong>Email:</strong>
    </p>

    <p>
        <?php echo str_replace(",", "<br/>", $doctoral->email); ?></p>


    <p>
        <strong>Registration Date:</strong>
        <?php echo $doctoral->registrationdate; ?></p>

    <p>
        <strong>Telephone:</strong>
        <?php echo $doctoral->telephone; ?></p>
    <p>
        <strong>Απόφοιτος/Ενεργός:</strong>
        <?php echo $doctoral->graduated ? "Απόφοιτος" : "Ενεργός"; ?></p>

    <p>
        <strong>Αποστολή Email:</strong>
        <?php echo $doctoral->sendemail ? "ΝΑΙ" : "ΟΧΙ"; ?></p>

    <p>
        <strong>Comment:</strong></p>

    <div class="well well-sm"><?php echo $doctoral->comment; ?></div>
    <p>
        <strong>Hours remaining:</strong>
        <?php echo $doctoral->hours_remaining; ?></p>

    <p>
        <strong>Hours completed:</strong>
        <?php echo $doctoral->hours_completed; ?></p>

    <p>
        <strong>Max Assignments Per Schedule:</strong>
        <?php echo $doctoral->max_assignments; ?></p>

    <p>
        <strong>Bonus Weight Per Course:</strong>
        <?php echo $doctoral->bonus_weight; ?></p>


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
                            <tr id="<?php echo $professor->id; ?>">
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
    <div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                    Αναλυτικές Επιτηρήσεις 
                </a>
            </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse in">
            <div class="panel-body table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Μάθημα</th>
                            <th>Ημερομηνία</th>
                            <th>Ώρες</th>
                            <th>Παρουσία</th>
                            <th>Σχόλιο</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($doctoral->supervisions as $s): ?>
                    <?php
                    $examcourse = $s->examcourse;
                    $course     = $examcourse ? $examcourse->course : null;
                    $examday    = $examcourse ? $examcourse->examday : null;
                    $examhour   = $examcourse ? $examcourse->examhour : null;

                    $when = '–';

                    if ($examday) {
                        $dateFormatted = date('d/m/Y', strtotime($examday->day));
                        $when = '<strong>'.$dateFormatted.'</strong>';

                        if ($examhour) {
                            $start = substr($examhour->start, 0, 5);
                            $end   = substr($examhour->end, 0, 5);

                            $when .= ' &nbsp;&nbsp; <span style="font-weight:600; color:#1f3558;">'.$start.'–'.$end.'</span>';
                        }
                    }
                    ?>

                        <tr>
                            <td>
                                <?= $course ? ($course->code . ' – ' . $course->title) : '–' ?>
                            </td>

                            <td><?= $when ?></td>

                            <td><?= $s->hours ?></td>
                            <td><?= $s->attended ? 'ΝΑΙ' : 'ΟΧΙ' ?></td>
                            <td><?= $s->comment ?></td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


    <?php echo Html::anchor('admin/doctorals/', '<i class="glyphicon glyphicon-arrow-left"></i> Back', array('class' => 'btn btn-sm btn-warning')); ?>
    <?php echo Html::anchor('admin/doctorals/edit/' . $doctoral->id, '<i class="glyphicon glyphicon-wrench"></i> Edit', array('class' => 'btn btn-sm btn-primary')); ?>


</div>