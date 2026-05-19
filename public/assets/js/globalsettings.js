$( document ).ready( function() {

    $("#datepicker").datepicker({
        showOn: 'focus',
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: '2015:2030'
        //minDate: new Date(currentYear, currentMonth, currentDate)
    });

} );

