
<script>
    var rd = REDIPS.drag;
    rd.dropMode = 'multiple';
</script>

<style>
    .clearfix:before,
    .clearfix:after {
        content: "";
        display: table;
    }
    .clearfix:after {
        clear: both;
    }
    .clearfix {
        zoom: 1;
    }
    
    #courses {
        float: left;
        border-collapse: collapse;
    }
    #schedule {
        float: right;
        border-collapse: collapse;
    }
    
    #courses td {
        border: 0;
        padding: 4px 8px;
        background: #eee;
    }
    #courses td.trash {
        height: 50px;
        text-align: center;
        font-weight: bold;
        vertical-align: middle;
        background: #000;
        color: white;
    }
    #schedule td {
        border: 1px solid #666;
        padding: 4px 8px;
        text-align: center;
        width: 190px;
    }
    #schedule td:first-child {
        width: auto;
    }

    div.drag {
        border: 1px solid #000;
        border-radius: 6px;
        padding: 2px 4px;
        width: 170px;
        background: #fff;
    }
    
    .mark {
        background: #eee;
    }
</style>

<!----------------------------------------------------------------------------->


<div style="text-align: right; padding-bottom: 20px;">
    <form method="post" action="examcourses" id="submit-form" >
        <input type="hidden" value="" id="serialized" name="course_positions" />
        <input type="submit" value="save" class="button" />
    </form>
</div>

<div id="drag" class="clearfix">
    
    <table id="courses">
        <?php foreach($courses as $id => $title): ?>
        <tr>
            <td class="single">
                <div id="<?php echo $id; ?>" class="drag clone">
                    <?php echo $title; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr><td class="trash">TRASH</td></tr>
        </tbody>
    </table>
    
    <table id="schedule">
        <tr>
            <td class="mark"></td>
            <?php foreach($hours as $hour_range): ?>
            <td class="mark">
                <?php
                $hour_start = new DateTime($hour_range['start']);
                $hour_end = new DateTime($hour_range['end']);
                echo date_format($hour_start, 'h:i').'-'.date_format($hour_end, 'h:i');
                ?>
            </td>
            <?php endforeach; ?>
        </tr>
        
        <?php
        $count = count($hours);
        $date = new DateTime($day_start);
        $row = 1;
        while(strcmp($date->format('Y-m-d'), $day_end) <= 0) {
        ?>
        <tr>
            <td class="mark" style="text-align: right;">
                <?php echo $date->format('D d/m/Y'); ?>
            </td>
            <?php
            for($col = 1; $col <= $count; $col++) {
                $index = $row.'.'.$col;
                if(array_key_exists($index, $course_positions)) { ?>
                    <td>
                        <?php foreach($course_positions[$index] as $course_id): ?>
                            <div id="<?php echo $course_id; ?>xx" class="drag">
                                <?php echo $courses[$course_id]; ?>
                            </div>
                        <?php endforeach; ?>
                    </td>
                <?php
                } else {
                    echo '<td></td>';
                }
            }
            $row += 1;
            $date->add(new DateInterval('P1D')); // P1D means a period of 1 day
            ?>
        </tr>
        <?php } ?>
    </table>
</div>

<script>
$(document).ready(function(){
    $('#submit-form').submit(function(){ //listen for submit event
        $('#serialized').val(REDIPS.drag.saveContent('schedule', 'json'));
        return true;
    });
});
</script>
    
    
<!--h2>Listing Examcourses</h2>
<br>
<?php /* if ($examcourses): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Period id</th>
			<th>Course id</th>
			<th>Day</th>
			<th>Hour</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($examcourses as $item): ?>		<tr>

			<td><?php echo $item->period_id; ?></td>
			<td><?php echo $item->course_id; ?></td>
			<td><?php echo $item->day; ?></td>
			<td><?php echo $item->hour; ?></td>
			<td>
				<?php echo Html::anchor('admin/examcourses/view/'.$item->id, 'View'); ?> |
				<?php echo Html::anchor('admin/examcourses/edit/'.$item->id, 'Edit'); ?> |
				<?php echo Html::anchor('admin/examcourses/delete/'.$item->id, 'Delete', array('onclick' => "return confirm('Are you sure?')")); ?>

			</td>
		</tr>
<?php endforeach; ?>	</tbody>
</table>

<?php else: ?>
<p>No Examcourses.</p>

<?php endif; ?><p>
	<?php echo Html::anchor('admin/examcourses/create', 'Add new Examcourse', array('class' => 'btn btn-success')); */ ?>

</p-->
