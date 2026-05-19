<div class="container">

    <div class="bg-danger" id="mouse-alert-div" style="display: none;">
        <p>Έχετε κάνει αλλαγές που <br>ΔΕΝ έχουν αποθηκευτεί!</p>
    </div>

    <h1 style="color:darkred;">Προσοχή!</h1>
    <h4>Για να αποθηκευτούν οι προτιμήσεις σας, <em>μην ξεχάσετε να πατήσετε το <strong>κουμπί</strong></em>
        <a href="#save-section">Save</a>
        <em> στο κάτω μέρος της σελίδας.</em>
    </h4>

    <br>
    <br>
    <br>
    <div class="well">
        <ul id="guide">
            <li>Στον παρακάτω πίνακα, εμφανίζονται οι μέρες και ώρες των εξεταζόμενων μαθημάτων της εξεταστικής <strong>"<?php echo $examperiod->getGreekSeason($examperiod->season) . " " . $examperiod->academic_year; ?>"</strong></li>
            <li>Ένα κελί με πράσινο χρώμα δηλώνει πως τη συγκεκριμένη μέρα/ώρα μπορείτε να παρεβρεθείτε
                για επιτήρηση εξεταζόμενου μαθήματος,<br />ενώ με κόκκινο χρώμα δηλώνει πως αδυνατείτε.</li>
            <li>Αρχικά, όλες οι ώρες εξεταζόμενων μαθημάτων είναι προεπιλεγμένες ώστε να είστε διαθέσιμος για επιτήρηση.</li>
            <li>Μπορείτε να αλλάξετε τη διαθεσιμότητά σας για μία συγκεκριμένη μέρα/ώρα κλικάροντας πάνω στο αντίστοιχο κελί,<br />
                για μία ολόκληρη ημέρα κλικάροντας στο κελί που βρίσκεται η περιγραφή της ημέρας,<br />
                και για μια ώρα (για όλη την εξεταστική) πατώντας πάνω στην περιγραφή της ώρας.</li>
        </ul>
    </div>
    <br />
    
    <form class="availability">
        On click:<br />
        <input class="pref-radio-button" type="radio" name="mode" value="add" /> Θέσε «Διαθέσιμος»<br />
        <input class="pref-radio-button" type="radio" name="mode" value="remove" checked="checked" /> Θέσε «Μη διαθέσιμος»
    </form>
    
    <?php /*********************************************************************
    Creates the schedule table *********************************************/ ?>
    
    <table id="schedule">
        <thead></thead>
        <tbody>
            <?php /**** create the examhours row ***************************/ ?>
            <tr>
                <td></td>
                <?php foreach($examperiod->examhours as $examhour): ?>
                <td class="examhour" id="h<?php echo $examhour['id']; ?>">
                    <?php echo substr($examhour['start'], 0, 5).' - '.substr($examhour['end'], 0, 5); ?>
                </td>
                <?php endforeach; ?>
            </tr>
            
            <?php /**** create all examdays rows ***************************/ ?>
            <?php foreach($examperiod->examdays as $examday): ?>
            <tr>
                <?php /**** create the row's date description cell *********/ ?>
                <td class="examday" id="d<?php echo $examday['id']; ?>">
                    <?php
                    echo date('l', strtotime($examday['day'])).'&nbsp&nbsp;';
                    echo Util\Dateformatter::serverToclient($examday['day']);
                    ?>
                </td>
                
                <?php /**** create the row's examcourses cell **************/ ?>
                <?php
                foreach($examperiod->examhours as $examhour)
                {
                    $classes = "d{$examday['id']} h{$examhour['id']}";
                    
                    $key = $examday['id'].'|'.$examhour['id'];
                    if(array_key_exists($key, $exam_days_x_hours))
                    {
                        $classes .= " examcourse";
                        
                        if(array_key_exists($key, $availabilities))
                        {
                            $classes .= " available";
                        }
                    }
                    
                    print "<td class=\"$classes\"></td>";
                }
                ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br />

    <a name="save-section"></a>
    <?php echo Form::open(array("class"=>"form-horizontal", "id"=>"submission", "action"=>Controller_Preferences::getUrl())); echo Form::csrf();?>
        <fieldset>
            <div class="form-group">
                <span style="font-weight: bold;">
                    Επιθυμείτε να κάνετε αραιές ή πυκνές επιτηρήσεις μέσα στην εξεταστική;
                </span>
                <br />
                <input class="pref-radio-button"  type="radio" name="density" value="sparse" <?php echo $preferences['density'] == 'sparse' ? 'checked="checked" ' : ''; ?>/> <strong>Αραιές</strong><br />
                <input class="pref-radio-button" type="radio" name="density" value="dense" <?php echo $preferences['density'] == 'dense' ? 'checked="checked" ' : ''; ?>/> <strong>Πυκνές</strong>
            </div>
            <div class="form-group">
                <?php echo Form::label('Επιθυμείτε να πραγματοποιείτε συνεχόμενες επιτηρήσεις σε μία ημέρα;', 'density_day', array('class'=>'control-label'))."<br/>"; ?>

                <?php
                    echo Form::radio('density_day', '0', ($preferences['density_day'] == '0' ? true : false), array('class' => 'pref-radio-button'));
                    echo Form::label('&nbsp;Όχι', 'density_day')."<br/>";

                    echo Form::radio('density_day', '2',($preferences['density_day'] == '2' ? true : false), array('class' => 'pref-radio-button'));
                    echo Form::label('&nbsp;Δύο', 'density_day')."<br/>";

                    echo Form::radio('density_day', '3',($preferences['density_day'] == '3' ? true : false), array('class' => 'pref-radio-button'));
                    echo Form::label('&nbsp;Τρείς', 'density_day')."<br/>";

                    echo Form::radio('density_day', '4',($preferences['density_day'] == '4' ? true : false), array('class' => 'pref-radio-button'));
                    echo Form::label('&nbsp;Τέσσερις', 'density_day')."<br/>";
                ?>


            </div>
            
            <div class="form-group">
                <?php
                echo Form::label('Άλλα σχόλια / προτιμήσεις:', 'comment', array('class'=>'control-label'));
                echo Form::textarea(
                        'comment',
                        $preferences['comment'],
                        array('class' => 'col-md-8 form-control', 'rows' => 6, 'placeholder'=>'Comment')
                     );
                ?>
            </div>
            
            <div class="form-group">
                <?php echo Form::hidden('available', '', array('class' => 'col-md-4 form-control')); ?>
            </div>
            
            <div class="form-group">
                <?php
                echo Form::submit('submit', ' Save', array(
                    'class' => 'btn btn-lg btn-success',
                    'style' => "font-family: 'Glyphicons Halflings';")
                );
                ?>
            </div>
        </fieldset>
    <?php echo Form::close(); ?>
    
</div>