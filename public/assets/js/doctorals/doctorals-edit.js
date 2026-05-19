$(document).ready(function () {

    var totalHoursToDo =
        parseInt($("#form_hours_remaining").val()) +
        parseInt($("#form_hours_completed").val());

    // remaining → completed
    $("#form_hours_remaining").on("change keyup", function () {

        var newHoursRemaining = parseInt($(this).val()) || 0;
        var newHoursCompleted = totalHoursToDo - newHoursRemaining;

        if (newHoursCompleted < 0) {
            newHoursCompleted = 0;
        }

        $("#form_hours_completed").val(newHoursCompleted);
    });

    // completed → remaining
    $("#form_hours_completed").on("change keyup", function () {

        var newHoursCompleted = parseInt($(this).val()) || 0;
        var newHoursRemaining = totalHoursToDo - newHoursCompleted;

        if (newHoursRemaining < 0) {
            newHoursRemaining = 0;
        }

        $("#form_hours_remaining").val(newHoursRemaining);
    });

});