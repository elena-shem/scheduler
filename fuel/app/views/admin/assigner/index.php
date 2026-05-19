<div id="assigner" class="flex-box flex-box-ver<?php echo $activeExamperiod->active == 1 ? ' examperiod-active' : ''; ?>">
    
    <div id="topbar" class="flex-item-static">
        <select id="selectExamperiod" class="pull-left">
            <?php foreach($examperiods as $id => $examperiod): ?>
            <option value="<?php echo $id; ?>"
                    data-state="<?php echo $examperiod->active == 1 ? 'active' : 'closed'; ?>">
                <?php echo $examperiod->getGreekSeason($examperiod->season).' '.$examperiod->academic_year; ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <div id="assignmentsCounter" class="pull-left">
            <span id="totalAssignmentsDone" title="Total assignments done"></span>
            /
            <span id="totalAssignmentsTodo" title="Total assignments required"></span>
        </div>
        
        <button id="removeSelections"
                class="btn btn-primary glyphicon glyphicon-eye-close pull-left"
                title="Remove ALL selections {key : ESCAPE}"></button>
        <button id="resetAssignments"
                class="btn btn-primary glyphicon glyphicon-refresh pull-left hide"
                title="Load assignments from Database (ALL UNSAVED PROGRESS WILL BE LOST)"></button>
        <button id="clearAssignments"
                class="btn btn-primary glyphicon glyphicon-trash pull-left hide"
                title="Clear all assignments LOCALLY (ALL UNSAVED PROGRESS WILL BE LOST)"></button>
                
        <div id="player" class="pull-left hide">
            <button id="undoAssignment"
                    class="btn btn-primary glyphicon glyphicon-arrow-left"
                    title="Undo assignment {key : LEFT ARROW}"></button>
            <button id="toggleAssigner"
                    class="btn btn-primary glyphicon glyphicon-play"
                    title="Start/Pause automatic Assigner {key : SPACE}"></button>
            <button id="nextAssignment"
                    class="btn btn-primary glyphicon glyphicon-arrow-right"
                    title="Produce one assignment {key : RIGHT ARROW}"></button>
        </div>
        
        <button id="closeAssignments"
                class="btn btn-default pull-right disabled"
                title="Permanently close assignments for this examperiod (ALL UNSAVED PROGRESS WILL BE LOST)">
                <span class="glyphicon glyphicon-ok"></span> <span class="text">closed</span></button>
        <button id="downloadExcel"
                class="btn btn-primary pull-right glyphicon glyphicon-list-alt hide"
                title="Download an Excel Sheet with the SAVED assignments"></button>
        <button id="selectDoctoralsToSendEmail"
                class="btn btn-primary pull-right glyphicon glyphicon-envelope hide"
                title="Select doctorals to send email"></button>
        <form id="saveAssignments" action="<?php echo Uri::create('admin/assigner/save'); ?>" method="post" class="pull-right hide">
            <input type="hidden" name="examperiod" value="" />
            <input type="hidden" name="assignments" value="" />
            <button type="submit" class="btn btn-primary" title="Save assignments to Database">
                <span class="glyphicon glyphicon-save"></span> Save
            </button>
        </form>
    </div>
    
    <!-- set height to 1px for firefox to work correctly -->
    <div class="flex-item-fill flex-box flex-box-hor" style="height:1px;">
        <div id="assignments" class="flex-item-fill fill-height">
            <!-- The table containing the assignments for the examined courses -->
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Hour</th>
                        <th class="merge-with-right de-pad"></th>
                        <th class="merge-with-right de-pad"></th>
                        <th title="Course name">Course</th>
                        <th title="Available supervisors"<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>av</th>
                        <th title="Supervisors assigned / Supervisors need" colspan="3"<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>sups</th>
                        <th title="Doctorals assigned">Doctorals</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic html for rows in schedule/assignments table -->
                    <tr id="template-examcourse" class="hide">
                        <td class="date"></td>
                        <td class="hour"></td>
                        <td class="merge-with-right de-pad"><button class="showInfo btn btn-primary glyphicon glyphicon-book"></button></td>
                        <td class="merge-with-right de-pad"><button class="searchDoc btn btn-primary glyphicon glyphicon-search hide"></button></td>
                        <td class="course"></td>
                        <td class="avCounter<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>"></td>
                        <td class="supsAssigned merge-with-right de-pad-right text-right<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>"></td>
                        <td class="merge-with-right de-pad<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>">/</td>
                        <td class="supsNeeded de-pad-left text-left<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>"></td>
                        <td class="doctorals"></td>
                    </tr>
                </tbody>
            </table>
            <!-- Dynamic html for assigned doctorals in an examcourse's field "doctorals" -->
            <span id="template-examcourse-doctoral" class="assignedDoctoral pull-left">
                
            </span>
        </div>
        
        <div id="doctorals" class="flex-item-static fill-height">
            <!-- The table containing the doctorals -->
            <table>
                <thead>
                    <tr>
                        <th class="merge-with-right de-pad"></th>
                        <th class="merge-with-right de-pad"></th>
                        <th title="Doctoral name">Doctoral</th>
                        <th title="Remaining hours"<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>rh</th>
                        <th title="Density">d</th>
                        <th title="Density day">dd</th>
                        <th title="Availabilities counter / Initial number of avalabilities"<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?> colspan="3">av</th>
                        <th<?php echo $activeExamperiod->active == 1 ? ' title="Supervisions assigned / Supervisions obliged" colspan="3"' : ''; ?>>sups</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic html for rows in doctorals table -->
                    <tr id="template-doctoral" class="hide">
                        <td class="merge-with-right de-pad"><button class="showInfo btn btn-primary glyphicon glyphicon-user"></button></td>
                        <td class="merge-with-right de-pad"><button class="searchSlot btn btn-primary glyphicon glyphicon-search hide"></button></td>
                        <td>
                            <span class="name"></span>
                        </td>
                        <td class="remHours text-right<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>"></td>
                        <td class="density text-center"></td>
                        <td class="densityDay text-center"></td>
                        <td class="avCounter merge-with-right text-right de-pad-right<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>"></td>
                        <td class="avSlash   merge-with-right de-pad<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>">/</td>
                        <td class="avInitNum text-left de-pad-left<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>"></td>
                        <td class="supsAssigned merge-with-right de-pad-right<?php echo $activeExamperiod->active == 1 ? ' text-right' : ' text-center'; ?>"></td>
                        <td class="merge-with-right de-pad<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>">/</td>
                        <td class="supsObliged text-left de-pad-left<?php echo $activeExamperiod->active == 1 ? '' : ' hide'; ?>"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <div class="modal fade" id="status-modal" tabindex="-1" role="dialog" aria-labelledby="Status">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="status-modal-title">Action Status</h4>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <h5 id="status-modal-text"></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalCloseWarning" tabindex="-1" role="dialog" aria-labelledby="Close Warning" aria-hidden="true">
        <div class="modal-dialog">
        <?php
        echo Form::open(array(
            'id'        => 'closeAssignmentsSubmission',
            'action'    => Uri::create('admin/assigner/close'),
            'method'    => 'post',
        ));
        echo Form::csrf();?>
                
            <!--/admin/assigner/close/ + window.Scheduler.Assigner.exid;-->
            <div class="modal-content" style="background-color: #2c3e50;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    <h4 class="modal-title" id="status-modal-title" style="color: #fff;">
                        Οριστικό κλείσιμο εξεταστικής
                    </h4>
                </div>
                <div class="modal-body" style="text-align: center; background-color: #fff;">
                    <div class="red">
                        <br>
                        <h3 id="closeWarning-title">ΠΡΟΣΟΧΗ!</h3>
                        <br>
                        <p>
                            Επιλέγοντας "Ναι":
                        </p>
                        <ul class="text-left">
                            <li>
                                Η λειτουργία αναθέσεων για την εξεταστική
                                <span class="underline"><?php echo $activeExamperiod->getGreekSeason($activeExamperiod->season).' '.$activeExamperiod->academic_year; ?></span>
                                θα κλείσει <span class="text-danger">ΟΡΙΣΤΙΚΑ</span>.
                            </li>
                            <li>
                                Οι εναπομείνασες και ολοκληρωμένες ώρες επιτηρήσεων των διδακτορικών θα ενημερωθούν στη βάση.
                            </li>
                        </ul>
                        <p class="text-danger">
                            Η διαδικασία είναι μη αναστρέψιμη.
                        </p>
                        <br>
                    </div>
                    <br>
                    <p>
                        Είστε σίγουρος/η πως θέλετε να προχωρήσετε στο κλείσιμο της εξεταστικής
                        <span class="underline"><?php echo $activeExamperiod->getGreekSeason($activeExamperiod->season).' '.$activeExamperiod->academic_year; ?></span>;
                    </p>
                    <br>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="closeOption" class="pred-radio-button" id="closeAssignmentsConfirmation"> &nbsp; ΝΑΙ
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="closeOption" class="pred-radio-button" checked="checked"> &nbsp; OXI
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 0;">
                    <?php echo Form::submit('submit', 'Submit', array('class' => 'btn btn-success'/*, 'data-dismiss' => 'modal'*/)); ?>
                </div>
            </div>
            
        <?php echo Form::close(); ?>
        </div>
    </div>
    
    <div class="modal" id="modal-doctoral-info" tabindex="-1" role="dialog" aria-labelledby="Doctoral Info" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">Doctoral Info</h5>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <table>
                        <tbody>
                            <tr>
                                <td>Doctoral Name</td>
                                <td class="name">-</td>
                            </tr>
                            <tr>
                                <td>Doctoral Email</td>
                                <td class="email">-</td>
                            </tr>
                            <tr>
                                <td>Professors</td>
                                <td class="professors"></td>
                            </tr>
                            <tr<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>
                                <td>Remaining Hours</td>
                                <td class="remHours">0</td>
                            </tr>
                            <tr>
                                <td>Density</td>
                                <td class="density">-</td>
                            </tr>
                            <tr>
                                <td>Density Day</td>
                                <td class="densityDay">-</td>
                            </tr>
                            <tr<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>
                                <td>Availabilities counter</td>
                                <td class="avCounter">0</td>
                            </tr>
                            <tr<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>
                                <td>Supervisions</td>
                                <td>
                                    <span class="supsAssigned">0</span>
                                    /
                                    <span class="supsObliged">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Comment</td>
                                <td class="comment">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal" id="modal-examcourse-info" tabindex="-1" role="dialog" aria-labelledby="ExamCourse Info" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">ExamCourse Info</h5>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <table>
                        <tbody>
                            <tr>
                                <td>Date</td>
                                <td class="date"></td>
                            </tr>
                            <tr>
                                <td>Day</td>
                                <td class="day"></td>
                            </tr>
                            <tr>
                                <td>Hour</td>
                                <td class="hour"></td>
                            </tr>
                            <tr>
                                <td>Course name</td>
                                <td class="course"></td>
                            </tr>
                            <tr>
                                <td>Professors</td>
                                <td class="professors"></td>
                            </tr>
                            <tr<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>
                                <td>Available supervisors</td>
                                <td class="avCounter">0</td>
                            </tr>
                            <tr<?php echo $activeExamperiod->active == 1 ? '' : ' class="hide"'; ?>>
                                <td>Supervisions Covered</td>
                                <td>
                                    <span class="supsAssigned">0</span>
                                    /
                                    <span class="supsNeeded">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Assigned doctorals</td>
                                <td class="doctorals"></td>
                            </tr>
                            <tr>
                                <td>Assigned doctorals emails</td>
                                <td class="doctoralsEmails"><a href="mailto:"></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sendEmailsModal" tabindex="-1" role="dialog" aria-labelledby="Status">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="sendEmailsModal-title">Send email to doctorals</h4>
                    <p>
                        Notifies the selected doctorals in this list about the current <b>**saved**</b> state of schedule
                    </p>
                </div>
                <div class="modal-body">
                    <div class="toolbox btn-group btn-group-sm">
                        <button class="btn btn-default" id="selectDoctoralsNone">select none</button>
                        <button class="btn btn-success" id="selectDoctoralsAllAssigned" title="Doctorals that have at least one assignment">select all assigned</button>
                    </div>
                    <div class="content">
                        <table>
                            <tbody class="doctorals">
                            <tr class="doctoral">
                                <td class="name"></td>
                                <td>
                                    <span class="glyphicon glyphicon-ok"></span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button id="sendEmails" type="button" class="btn btn-success"
                            title="Announce SAVED assignments - send emails">
                    Send Emails
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php if($activeExamperiod->active != 1): ?>
<div class="alert alert-warning alert-dismissable bg-warning">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <p>
        Beware, deleted courses or doctorals do not appear in the tables.
    </p>
</div>

<?php endif; ?>

