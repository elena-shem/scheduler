<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Προσοχή!</h4>
            </div>
            <div class="modal-body" style="text-align: center;">
                Για να αποθηκευτούν οι προτιμήσεις σας, μην ξεχάσετε να πατήσετε το κουμπί<br/>
                <button type="button" class="btn btn-lg btn-success" style="font-family: 'Glyphicons Halflings';"> Save</button>
                <br/> στο κάτω μέρος της σελίδας.

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Εντάξει</button>

            </div>
        </div>
    </div>
</div>


<div class="container">

    <h1 style="color:darkred;">Προσοχή!</h1>
    <h4>Για να αποθηκευτούν οι προτιμήσεις σας, μην ξεχάσετε να πατήσετε το κουμπί
        <button type="button" class="btn btn-lg btn-success" style="font-family: 'Glyphicons Halflings';"> Save</button>
     στο κάτω μέρος της σελίδας.</h4>


    <h3><?php echo $q_title; ?></h3>
    <div class="well">
       <?php echo $question_html; ?>
    </div>
    <br />
    

    
    
    <?php echo Form::open(array("class"=>"form-horizontal", "id"=>"submission", "action"=>Controller_Questionnaire::getUrl())); echo Form::csrf();?>
        <fieldset>
            <div class="form-group">
                <?php echo Form::label('Απάντηση', 'answer', array('class'=>'control-label','id'=>'form_answer_label')); ?>
                <?php echo Form::textarea('answer',isset($answer)?$answer:'', array('class' => 'col-md-8 form-control', 'rows' => 12, 'placeholder'=>'Απάντηση')); ?>
            </div>

            <div class="form-group">
                <?php echo Form::label('Checkbox 1', 'logical1'); ?>
                <?php echo Form::checkbox('logical1', true, isset($logical_1)?$logical_1:false); ?>
            </div>

            <div class="form-group">
                <?php echo Form::label('Checkbox 2', 'logical2'); ?>
                <?php echo Form::checkbox('logical2', true, isset($logical_2)?$logical_2:false); ?>
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