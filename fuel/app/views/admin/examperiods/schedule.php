<?php
function minifyCourseTitle($title)
{
    return $title;
    $title = html_entity_decode($title, ENT_COMPAT, 'utf-8');
    return mb_strlen($title, 'utf-8') <= 32 ? $title : mb_substr($title, 0, 32, 'utf-8').' ...';
}

$allCourseModels = Model_Course::find('all');
?>

<div class="container schedule-page">
    <div id="schedule" class="flex-box flex-box-ver">
    
<div id="topbar" class="flex-item-static">

    <?php echo Form::open(); ?>
    <?php echo Form::csrf(); ?>

    <?php echo Form::hidden('schedule', ''); ?>

   <div class="form-group">
    <div class="form-actions-bar">

        <div class="form-actions-left">
            <?php
            echo Html::anchor(
                'admin/examperiods',
                '<i class="glyphicon glyphicon-arrow-left"></i> Back',
                ['class' => 'btn btn-sm btn-warning']
            );

            echo Html::anchor(
                'admin/examperiods/view/'.$examperiod->id,
                '<i class="glyphicon glyphicon-search"></i> View',
                ['class' => 'btn btn-sm btn-primary']
            );

            echo Html::anchor(
                'admin/examperiods/edit/'.$examperiod->id,
                '<i class="glyphicon glyphicon-wrench"></i> Edit',
                ['class' => 'btn btn-sm btn-info']
            );
            ?>
        </div>

        <button type="submit" class="btn btn-sm btn-success">
            <i class="glyphicon glyphicon-save"></i> Save
        </button>

    </div>
</div>


    <?php echo Form::close(); ?>

</div>

    <!-- set height to 1px for firefox to work correctly -->
    <div id="drag" class="flex-item-fill flex-box flex-box-hor" style="height:1px;">
        
        <div id="courses" class="flex-item-static fill-height">

                <div class="courses-filter">
                <select id="courseSearch" style="width:100%">
                    <option value=""></option>
                    <?php foreach($allCourseModels as $course): ?>
                    <option value="c<?php echo (int)$course['id']; ?>">
                        <?php echo e($course['title']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                </div>

            <table>
                <tbody id="courses-only-dummy">
                    
                </tbody>
                <tbody id="courses-unassigned">
                <?php foreach($allCourseModels as $course): ?>
                    <tr>
                        <td class="single">
                            <div id="c<?php echo $course['id']; ?>"
                                 class="drag clone<?php echo $course->code == 'DUM' ? ' dummy' : ''; ?>"
                                 data-assignmentsnum="0">
                                <?php echo minifyCourseTitle($course['title']); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th>Assigned courses (at least once)</th>
                    </tr>
                </thead>
                <tbody id="courses-assigned">
                    
                </tbody>
            </table>
        </div>
        
        <div id="calendar" class="flex-item-fill fill-height">
            <!-- The table containing the calendar with the assigned courses -->
            <table id="schedule-contents">
                <thead>
                    <tr>
                        <th class="mark"></th>
                        <?php foreach($examperiod->examhours as $examhour): ?>
                        <th class="mark" id="h<?php echo $examhour['id']; ?>">
                            <?php echo substr($examhour['start'], 0, 5).' - '.substr($examhour['end'], 0, 5); ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // $copiesNum holds for each course in the calendar, the number of its appearances minus 1.
                    // If no appearance, then it simply does not contain the course's key/id.
                    // 
                    // This is done to avoid HTML Elements id duplication in case that a course is assigned
                    // more than once in an examperiod..
                    $copiesNum = array();
                    
                    foreach($examperiod->examdays as $examday) { ?>
                    <tr id="d<?php echo $examday['id']; ?>">
                        <?php /**** create the row's date description cell *********/ ?>
                        <td class="mark">
                            <?php
                            echo date('l', strtotime($examday['day'])).'&nbsp&nbsp;';
                            echo Util\Dateformatter::serverToclient($examday['day']);
                            ?>
                        </td>
                        <?php /**** create the row's examcourses cell *****************/
                        foreach($examperiod->examhours as $examhour)
                        {
                            $index = $examday['id'].'.'.$examhour['id'];
                            print '<td>';
                            if(isset($examcourses[$index]))
                            {
                                foreach($examcourses[$index] as $course_id) {
                                    $copiesNum[$course_id] = array_key_exists($course_id, $copiesNum) ? ($copiesNum[$course_id] + 1) : 0;
                                ?>
                                    <div id="c<?php echo $course_id.'s'.$copiesNum[$course_id]; ?>"
                                         class="drag<?php echo $courses[$course_id]['code'] == 'DUM' ? ' dummy' : ''; ?>">
                                        <?php echo minifyCourseTitle($courses[$course_id]['title']); ?>
                                        <div class="glyphicon glyphicon-remove remove"></div>
                                    </div>
                                <?php
                                }
                            }
                            print '</td>';
                        }
                        ?>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
        </div>
  </div>

</div>