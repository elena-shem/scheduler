<div class="container">
    
<?php echo Form::open(['id'=>'periodForm', 'class'=>'form-horizontal', 'method'=>'get']); ?>

<div class="row" style="margin-bottom: 15px;">
  <div class="col-md-3">
    <div class="form-group">
      <?php echo Form::label('Exam Year', 'exam_year', ['class'=>'control-label']); ?>
      <?php echo Form::select('exam_year', $selected_year, $years, [
        'id'=>'exam_year',
        'class'=>'form-control'
      ]); ?>
    </div>
  </div>

  <div class="col-md-3">
    <div class="form-group">
      <?php echo Form::label('Exam Period', 'season', ['class'=>'control-label']); ?>
      <?php echo Form::select('season', $selected_season, $seasons, [
        'id'=>'season',
        'class'=>'form-control'
      ]); ?>
    </div>
  </div>
</div>

<?php echo Form::close(); ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('periodForm');
    var y = document.getElementById('exam_year');
    var s = document.getElementById('season');

    function submitForm(){ form.submit(); }

    y.addEventListener('change', submitForm);
    s.addEventListener('change', submitForm);
  });
</script>
    
    <table id="schedule">
        <thead></thead>
        <tbody>
            <?php //--- create the examhours row ---// ?>
            <tr>
                <td></td>
                <?php foreach($examperiod->examhours as $examhour): ?>
                <td class="examhour">
                    <?php echo substr($examhour['start'], 0, 5).' - '.substr($examhour['end'], 0, 5); ?>
                </td>
                <?php endforeach; ?>
            </tr>
            
            <?php //--- create the total availabilities row ---// ?>
            <tr class="totalAvailabilities">
                <td>Total Availabilities<br>per Hour</td>
                <?php foreach($examperiod->examhours as $examhour): ?>
                <td>
                    <?php
                    $avTotal = 0;
                    foreach($examperiod->examdays as $examday) {
                        $key = "{$examday['id']}|{$examhour['id']}";
                        if(array_key_exists($key, $availabilities)) {
                            $avTotal += count($availabilities[$key]);
                        }
                    }
                    print $avTotal;
                    ?>
                </td>
                <?php endforeach; ?>
            </tr>
            
            <?php //--- create all examdays rows ---// ?>
            <?php foreach($examperiod->examdays as $examday): ?>
            <tr>
                <?php //--- create the row's date description cell ---// ?>
                <td class="examday">
                    <?php
                    echo date('l', strtotime($examday['day'])).'&nbsp&nbsp;';
                    echo Util\Dateformatter::serverToclient($examday['day']);
                    ?>
                </td>
                
                <?php //--- create the row's examcourses cell ---// ?>
                <?php
                foreach($examperiod->examhours as $examhour)
                {
                    $key = "{$examday['id']}|{$examhour['id']}";
                    if(empty($availabilities[$key]))
                    {
                        print '<td class="noexam"></td>';
                    }
                    else
                    {
                        print '<td>'.count($availabilities[$key]).'</td>';
                    }
                }
                ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
</div>