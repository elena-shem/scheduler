
$("#form_start, #form_end").datepicker({
    prevText: "Προηγούμενος Μήνας",
    nextText: "Επόμενος Μήνας",
    currentText: "Τρέχων Μήνας",
    closeText: "Κλείσιμο",
    weekHeader: "Εβδομάδα",
    
    monthNames: ["Ιανουάριος","Φεβρουάριος","Μάρτιος","Απρίλιος","Μάιος","Ιούνιος",
                 "Ιούλιος","Αύγουστος","Σεπτέμβριος","Οκτώβριος","Νοέμβριος","Δεκέμβριος"],
    monthNamesShort: ["Ιαν","Φεβ","Μαρ","Απρ","Μαι","Ιουν", "Ιουλ","Αυγ","Σεπ","Οκτ","Νοε","Δεκ"],
    dayNames: ["Κυριακή","Δευτέρα","Τρίτη","Τετάρτη","Πέμπτη","Παρασκευή","Σάββατο"],
    dayNamesShort: ["Κυρ","Δευ","Τρι","Τετ","Πεμ","Παρ","Σαβ"],
    dayNamesMin: ["Κυ","Δε","Τρ","Τε","Πε","Πα","Σα"],
    firstDay: 1, // first day of the week is Monday
    
    dateFormat: "dd/mm/yy",
    
    showOtherMonths: true,
    showButtonPanel: true,
    constrainInput: true,
    showAnim: "slideDown",
    autoSize: true,
    duration: 250
});


/*******************************************************************************
 * Hour ranges
 */

function addHourRange()
{
    var index = Date.now();

    var rowHtml = `
        <div class="hour-range-row" style="margin-bottom:5px; display:flex; gap:10px;">
            <input class="col-md-4 form-control hour-input"
                   name="examhours[${index}][range]"
                   value=""
                   type="text" />
            <a href="#" class="remove-hour" style="color:red;">Remove</a>
        </div>
    `;

    $("#examhours").append(rowHtml);
}

// --- REMOVE ---
$(document).on('click', '.remove-hour', function(e) {
    e.preventDefault();
    $(this).closest('.hour-range-row').remove();
});