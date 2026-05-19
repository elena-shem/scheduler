//TODO emails: exam_id Unique
// TODO preferencesavailable add index for doctoral_id
// TODO preferencesgeneral add index for doctoral_id.
//TODO authdigest sto rest!

var uriBase = window.URI_BASE || (window.location.protocol + "//" + window.location.host + "/");
var calendarJson = null;
var examPeriod = null;

var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&apos;',
    "/": '&#x2F;'
};

$( document ).ready( function() {


    //Doc table specifics
    $('#form_examperiod').change(function() {
        $('#data-container').empty();

        //Set examPeriod
        examPeriod = $(this).val();

        //Get the full schedule
        //Wait till you get the full schedule and then start building the mini calendars
        postJsonFullSchedule(examPeriod).done(function(){
            postJsonDoctoralsAvailability(examPeriod);
        });

    });


    //Search functionality

    /**
     * Name
     */
    $("#searchName").on("keyup", function() {

        //The value that the user inputs
        //Lowercase it so it's case insensitive
        var value = $(this).val().toLowerCase();

        //Get the table rows and foreach one
        $("table tr").each(function(index) {

            if (index !== 0) {

                //get the row
                $row = $(this);

                //find the row's id by searching the column that makes sense
                // (name is the third one here)
                var id = $row.find("td:nth-child(3)").text().toLowerCase();

                //If the search string (value) is not a part of the data (<0)
                // hide it.
                if (id.indexOf(value) < 0) {
                    $row.hide();
                }
                else {
                    $row.show();
                }
            }
        });
    });

    /**
     * ID
     */
    $("#searchId").on("keyup", function() {
        var value = $(this).val();

        $("table tr").each(function(index) {
            if (index !== 0) {

                $row = $(this);

                var id = $row.find("td:nth-child(1)").text();

                if (id.indexOf(value) !== 0) {
                    $row.hide();
                }
                else {
                    $row.show();
                }
            }
        });
    });

    /**
     * Surname
     */
    $("#searchSurname").on("keyup", function() {
        var value = $(this).val().toLowerCase();

        $("table tr").each(function(index) {
            if (index !== 0) {

                $row = $(this);

                var id = $row.find("td:nth-child(2)").text().toLowerCase();

                if (id.indexOf(value) < 0) {
                    $row.hide();
                }
                else {
                    $row.show();
                }
            }
        });
    });



});

/**
 * Get the full schedule
 * @param examPeriodId
 */
function postJsonFullSchedule(examPeriodId){
    var uriJsonDoc = uriBase + "rest/fullschedule/list.json";
    calendarJson = $.ajax({
        type: "POST",
        url: uriJsonDoc,
        data: {
            exid: examPeriodId
        },
        dataType: 'json'
    });
    return calendarJson;
}

/**
 *
 * @param string
 * @returns {string}
 *
 * Escape troublesome characters
 */
function escapeHtml(string) {
    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    }).replace(/(\r\n|\n|\r)/gm,"");;
}


/**
 * @param type
 * @param variable
 * Display Messages
 */
function message (type, variable){
    jQuery("#dismissButton" ).trigger('click');
    var msg = "";
    var color ="";
     if(type === 'error'){
        msg = "Error for exam period: " + variable;
        color = 'danger';
    }

    var messageHtml = '<div class="alert alert-'+ color +' alert-dismissable">' +
        '<button id="dismissButton" class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>'+
        '<p>' + msg + '</p>' +
        '</div>';
    $(messageHtml).appendTo(".col-md-12:nth-child(1)");

    setTimeout(function () {
        jQuery("#dismissButton" ).trigger('click');
    }, 10000);
}

/**
 * Request data for all docs
 * @param examPeriodId
 */
function postJsonDoctoralsAvailability(examPeriodId){
    var uriJsonDoc = uriBase + "rest/availabilitydata/list.json";
    $.ajax({
        type: "POST",
        url: uriJsonDoc,
        data: {
            exid: examPeriodId
        },
        success: function(data){
            giveMeDataDoc(data,examPeriodId);
        },
        error: function(){ message('error',examPeriodId);},
        dataType: 'json'
    });
}


/**
 * Display the mini calendars
 * @param data
 * @param examPeriodId
 */
function giveMeDataDoc(data,examPeriodId){

    //check if we have sane data
    if (typeof data !== 'object') {
        console.log("availabilitydata bad response:", data);
        message('error', examPeriodId);
        return;
    }
    $.each( data, function(key,data ){
        var html = createTableDoc(data);
        $('#data-container').append(html);
    });
}

/**
 * Render the mini calendars
 * @param data
 * @returns {*|{}}
 */
function createTableDoc(data){

    //console.log(data);
    var template = $.templates("#tableTmpl");
    data.commentHtml = buildCommentCell(data.id, data.comment, 80);

    //console.log(shit);
    data.calendar = createCalendar(data.days_hours,data.id,data.name,data.surname);
    //console.log(data);
    return template.render(data);
}


/**
 * Initialize the mini calendars
 * @param days_hours
 * @param docId
 * @param name
 * @param surname
 * @returns {string}
 */
function createCalendar(days_hours,docId,name,surname){


    var calendarData = calendarJson.responseJSON;


//    console.log($.active);
//    console.log(calendarData);
//    console.log(calendarData.days);
//    console.log(calendarData.hours);

    var daysHoursArray = days_hours.split(",");
    //console.log(daysHoursArray);

    var calHtml = "<table class='table table-hover availability-table'>" +
        "<thead>" +
        "<tr>" +
        "<th id='docFullId" + docId + "'>" + name + " " + surname +  "</th>";

    for(var i=0; i < calendarData.hours.length; i++){
        var hourStart = calendarData.hours[i].hour_start;
        var hourEnd = calendarData.hours[i].hour_end;
        calHtml +=  "<th hourId='"+ calendarData.hours[i].hour_id + "'>" + hourStart + " - " + hourEnd + "</th>";
    }
    calHtml += "</thead>";

    var calHtml2_1 = "";
    for( var j=0; j < calendarData.days.length; j++){
        calHtml2_1 += "<tr><td dayId='"+ calendarData.days[j].day_id + "'>" + calendarData.days[j].day + "</td>";
        for(var i=0; i < calendarData.hours.length; i++){
            var active = false;
            //see if it is active or not
            for( var k = 0; k < daysHoursArray.length; k++){
                if(daysHoursArray.indexOf(calendarData.days[j].day_id + "-" + calendarData.hours[i].hour_id) !== -1){
                    active = true;
                }
            }
            var tdClass = "";
            var cellId = " cellId='" + calendarData.days[j].day_id + "-" + calendarData.hours[i].hour_id + "'";
            if(active === true){
                tdClass = ' success ';
            }else{
                tdClass = ' danger ';
            }
            calHtml2_1 += "<td class='" + tdClass + "'" + cellId + " ></td>";
        }
        calHtml2_1 += "</tr>";
    }


    var calHtml2_2 =      "</tr>" +
        "<tbody>";

    calHtml2_1 += calHtml2_2;

    var calHtml3 =  "</tbody>" +
        "</table>";
    //console.log(calHtml + calHtml2 + calHtml3);

    return escapeHtml(calHtml + calHtml2_1 + calHtml3);
}
