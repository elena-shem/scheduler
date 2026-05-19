$(document).ready(function () {

    //This is set when the submit button is pressed.
    var userSubmission = false;

    //This is set when something is changed.
    var userChangedSomething = false;


    //Store the existence of the php success message. The one that comes through session getflash in the template.
    var flashSuccessfulActionReturned = ($('#flash-successful-action-returned').length > 0);

    /*******************************************************************************
     * Prevent user from leaving the page
     */
    $(window).bind("beforeunload", function (event) {
        if(userChangedSomething && !flashSuccessfulActionReturned && !userSubmission){
            return "\n\n\n\nΦαίνεται πως δεν έχετε σώσει τις προτιμήσεις ή τις αλλαγές σας σε αυτές!\n\nΤο κουμπί αποθήκευσης βρίσκεται στο κάτω μέρος της σελίδας.\n\n\n\n";
        }
    });

    /**
     * Sense changes in radios and text area
     */
    $('.pref-radio-button').change(function(){
        signalChange();
    });

    $('#form_comment').on('change keyup paste', function() {
        signalChange();
    });

    /*******************************************************************************
     * Change availability for a day/hour combination.
     */

    $("#schedule td.examcourse").click(function () {
        changeAvailability($(this));
        signalChange();
    });

    $("#schedule td.examday, #schedule td.examhour").click(function () {
        var id = $(this).attr("id");
        var selector = "." + id;
        $(selector).each(function () {
            changeAvailability($(this));
        });
        console.log('change');
        userChangedSomething = true;
        flashSuccessfulActionReturned=false;
    });


    var changeAvailability = function (elem) {
        if ($(".availability input:radio[name=mode]:checked").val() === "add") {
            if (elem.hasClass("examcourse") && !elem.hasClass("available")) {
                elem.addClass("available");
            }
        } else {
            if (elem.hasClass("examcourse") && elem.hasClass("available")) {
                elem.removeClass("available");
            }
        }
    };

    var signalChange = function (){
        console.log('Change detected.');
        userChangedSomething = true;
        flashSuccessfulActionReturned=false;
    };


    /*******************************************************************************
     *
     */

    $("#submission").submit(function () {
        userSubmission = true;
        var JSONobj = [];

        $("td.examcourse").each(function () {
            if ($(this).hasClass("available")) {
                var class_str = $(this).attr("class");
                var examday_id = /d[0-9]+/.exec(class_str)[0].substr(1);
                var examhour_id = /h[0-9]+/.exec(class_str)[0].substr(1);
                JSONobj.push(examday_id + "|" + examhour_id);
            }
        });

        $("#form_available").val(JSON.stringify(JSONobj));



        return true;
    });


    /**
     * Follow mouse when something is not saved.
     */
    $(document).on('mousemove', function(e){

        if(userChangedSomething){
            $('#mouse-alert-div').css({
                left:  e.pageX,
                top:   e.pageY - 350,
                display: 'block'
            });
        }
    });

});

