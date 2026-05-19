<?php /************************************************************************/
/******************************************************************************/

function getCurrentAcademicYear()
{
    $year = date('Y');
    if(strcmp(date('m/d'), '08/31') <= 0) // if today is before September's 1st of this year
    {
        return ($year - 1).'-'.$year;
    }
    else
    {
        return $year.'-'.($year + 1);
    }
}

/******************************************************************************/
/***************************************************************************/ ?>


<?php echo Form::open(array("class"=>"form-horizontal")); echo Form::csrf();?>

    <fieldset>
        <div class="form-group">
            <?php
            echo Form::label('Season', 'season', array('class'=>'control-label'));
            echo Form::select(
                    'season',
                    isset($form['season']) ? $form['season'] : (isset($examperiod) ? $examperiod->season : ''),
                    Model_Examperiod::getSeasons(),
                    array('class' => 'col-md-4 form-control')
                 );
            ?>
        </div>
        <div class="form-group">
            <?php
            echo Form::label('Academic year', 'academic_year', array('class'=>'control-label'));
            echo Form::input(
                    'academic_year',
                    isset($form['academic_year']) ? $form['academic_year'] : (isset($examperiod) ? $examperiod->academic_year : getCurrentAcademicYear()),
                    array('class' => 'col-md-4 form-control')
                 );
            ?>
        </div>
        <div class="form-group">
            <?php
            echo Form::label('Start Date', 'start', array('class'=>'control-label'));
            echo Form::input(
                    'start',
                    isset($form['start']) ? $form['start'] : (isset($examperiod) ? $examperiod->start : ''),
                    array('class' => 'col-md-4 form-control')
                 );
            ?>
        </div>
        <div class="form-group">
            <?php
            echo Form::label('End Date', 'end', array('class'=>'control-label'));
            echo Form::input(
                    'end',
                    isset($form['end']) ? $form['end'] : (isset($examperiod) ? $examperiod->end : ''),
                    array('class' => 'col-md-4 form-control')
                 );
            ?>
        </div>
        
<div class="form-group">
    <?php echo Form::label('Hour ranges', 'hours[]', array('class'=>'control-label')); ?>
    [ <a href="javascript:void(0)" onclick="addHourRange()">Add</a>]
    <div id="examhours">
        <?php
        $examhours = isset($form['examhours']) ? $form['examhours'] : (isset($examperiod) ? $examperiod->examhours : '');

        if (empty($examhours)) {
            $examhours = array(
                array('range' => '08:45-11:45'),
                array('range' => '12:00-15:00'),
                array('range' => '15:15-18:15'),
                array('range' => '18:30-21:30')
            );
        }

        $maxIndex = 0;
        foreach($examhours as $num => $when) {
            $maxIndex = max($maxIndex, $num);
            $val = '';
            if (isset($when['range'])) {
                $val = $when['range'];
            } elseif (isset($when['start']) && isset($when['end'])) {
                $val = $when['start'] . '-' . $when['end'];
            }
            
            echo '<div class="hour-range-row" style="margin-bottom: 5px; display: flex; align-items: center; gap: 10px;">';
            echo Form::input("examhours[{$num}][range]", $val, array('class' => 'col-md-4 form-control hour-input'));
            echo '<a href="javascript:void(0)" style="color: red;" onclick="this.closest(\'.hour-range-row\').remove();">Remove</a>';
            echo '</div>';
        }
        ?>
    </div>
    <script>var hourRangeIndex = <?php echo $maxIndex + 1; ?>;</script>
</div>
                
        <div class="form-group">
            <?php
            echo Form::label('Comment', 'comment', array('class'=>'control-label'));
            echo Form::textarea(
                    'comment',
                    isset($form['comment']) ? $form['comment'] : (isset($examperiod) ? $examperiod->comment : ''),
                    array('class' => 'col-md-8 form-control', 'rows' => 8, 'placeholder'=>'Comment')
                 );
            ?>
        </div>
        <div class="form-group">
            <?php /* <label class='control-label'>&nbsp;</label>
            <?php
            echo Form::submit(
                    'submit',
                    'Save',
                    array('class' => 'btn btn-primary')
                 );
            ?> */ ?>
            
<div class="form-group">
  <div class="form-actions-bar">

    <div class="form-actions-left">
      <?php
      echo Html::anchor(
        'admin/examperiods',
        '<i class="glyphicon glyphicon-arrow-left"></i> Back',
        ['class' => 'btn btn-sm btn-default']
      );
      ?>

      <?php if ($type != "create"): ?>
        <?php echo Html::anchor(
          'admin/examperiods/view/'.$examperiod->id,
          '<i class="glyphicon glyphicon-search"></i> View',
          ['class' => 'btn btn-sm btn-primary']
        ); ?>

        <?php echo Html::anchor(
          'admin/examperiods/schedule/'.$examperiod->id,
          '<i class="glyphicon glyphicon-calendar"></i> Schedule',
          ['class' => 'btn btn-sm btn-info']
        ); ?>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-sm btn-success">
      <i class="glyphicon glyphicon-save"></i> Save
    </button>

  </div>
</div>

    </fieldset>
<?php echo Form::close(); ?>