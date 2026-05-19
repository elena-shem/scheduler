<div class="container">
    <h2>New Doctoral</h2>
    <br>

    <?php echo render('admin/doctorals/_form', array(
        'possesed_professors' => $possesed_professors,
        'professors'          => $professors,
        'type'                => $type
    )); ?>
</div>