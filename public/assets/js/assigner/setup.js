$(function() {
    
    /* *************************************************************************
     * Page setup (temporary modifications)
     * ************************************************************************/
    
    $("#page-title").remove();
    $("#push").remove();
    $("#footer").remove();
    $("#wrap > .container-fluid > div > hr").remove();
    $("#wrap > .container-fluid > hr").remove();
    
    $("#assigner").height($(window).height() - 75);
    
    $(window).resize(function() {
        $("#assigner").height($(window).height() - 75);
    });
    
    /* ********************************************************************** */
    
    // namespace definition
    window.Scheduler = window.Scheduler || {};
    window.Scheduler.Assigner = window.Scheduler.Assigner || {};
    
    window.Scheduler.Assigner.dates         = new Scheduler.ArrMap();
    window.Scheduler.Assigner.examcourses   = new Scheduler.ArrMap();
    window.Scheduler.Assigner.doctorals     = new Scheduler.ArrMap();
    window.Scheduler.Assigner.highlighter   = new Scheduler.Assigner.Highlighter();
    window.Scheduler.Assigner.assigner      = new Scheduler.Assigner.Assigner;
    window.Scheduler.Assigner.exid          = -1;
    
    /* ********************************************************************** */
    
    var BASE = (window.APP_BASE ? window.APP_BASE : (location.protocol + "//" + location.host));
    var url  = BASE + "/rest",
        queryString = "",
        matches = location.pathname.match(/\/admin\/assigner\/index\/([^/]+).*/);

    
    
    
    
    if(matches === null || matches.length !== 2) {
        // there is no explictily requested examperiod id.
        // => go for most recent examperiod.
        window.Scheduler.Assigner.exid = parseInt(document.getElementById("selectExamperiod").firstElementChild.getAttribute("value"));
    } else if(parseInt(matches[1]) || matches[1] === "0") {
        window.Scheduler.Assigner.exid = parseInt(matches[1]);
        document.getElementById("selectExamperiod").value = window.Scheduler.Assigner.exid;
    } else {
        // a value is given for examperiod id, but is not an integer.
        // => go for most recent examperiod.
        window.Scheduler.Assigner.exid = parseInt(document.getElementById("selectExamperiod").firstElementChild.getAttribute("value"));
    }
    queryString = "?exid=" + window.Scheduler.Assigner.exid;
    
    document.getElementById("selectExamperiod").addEventListener("change", function() {
        location.href = BASE + "/admin/assigner/index/" + this.value;
    });
    
    (function() {
        var form = $('#closeAssignmentsSubmission');
        var action = form.attr('action');

        // Ensure it ends with "/"
        if (action.charAt(action.length - 1) !== '/') {
            action += '/';
        }

        // Avoid double appending if it already ends with the id
        if (!action.match(new RegExp('/' + window.Scheduler.Assigner.exid + '/?$'))) {
            form.attr('action', action + window.Scheduler.Assigner.exid);
        }
    })();
        
    // If viewing examperiod is active...
    if($('#selectExamperiod').children('option[value="'+ window.Scheduler.Assigner.exid +'"]').attr('data-state') === 'active') {
        var btn = $('#closeAssignments').removeClass('btn-default disabled').addClass('btn-danger');
        btn.children('.glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-off');
        btn.children('.text').text('CLOSE');
        
        $('#clearAssignments').removeClass('hide');
        $('#resetAssignments').removeClass('hide');
        $('#player').removeClass('hide');
        $('#selectDoctoralsToSendEmail').removeClass('hide');
        $('#downloadExcel').removeClass('hide');
        $('#saveAssignments').removeClass('hide');
        $('#saveAssignments > input[name="examperiod"]')[0].value = window.Scheduler.Assigner.exid;
    }
    
    
    $.when(
        $.get(url + "/schedule/list.json" + queryString),
        $.get(url + "/doctorals/list.json" + queryString)
    ).done(function(schedule, doctorals) {
        
        if(schedule[1] !== "success") {
            console.log("Error: failed to load schedule data");
            return;
        }
        if(!Array.isArray(schedule[0].dates)
        || !Array.isArray(schedule[0].hours)
        || !Array.isArray(schedule[0].examcourses)) {
            console.log("Error: retrieved malformed schedule data");
            return;
        }
        
        if(doctorals[1] !== "success") {
            console.log("Error: failed to load doctorals data");
            return;
        }
        if(!Array.isArray(doctorals[0])) {
            console.log("Error: retrieved malformed doctorals data");
            return;
        }
        doctorals[0] = doctorals[0].slice(1);
        
        window.Scheduler.Assigner.active = schedule[0].active;
        
        buildSchedule(schedule[0]);
        buildDoctorals(doctorals[0]);
        addAvailabilities(doctorals[0]);
        addAssignments(schedule[0].examcourses);
        buildSelectDoctoralsModal();
    });
    
    function buildSchedule(data) {
        
        // map namespaced types and variables locally
        var ArrMap      = Scheduler.ArrMap
        ,   ExamCourse  = Scheduler.Assigner.ExamCourse
        ,   dates       = Scheduler.Assigner.dates
        ,   examcourses = Scheduler.Assigner.examcourses
        ,   assigner    = Scheduler.Assigner.assigner
        ;
        // local variables declaration
        var container = document.getElementById("assignments").getElementsByTagName("tbody")[0]
        ,   i
        ,   j
        ,   k
        ,   date
        ,   hour
        ,   examcourse
        ,   totalAssignmentsTodo
        ;
        
        // Build schedule
        
        for(i = 0; i < data.dates.length; i++) {
            date = {
                id:         parseInt(data.dates[i].id)
            ,   day:        data.dates[i].day
            ,   date:       data.dates[i].date
            ,   hours:      new ArrMap()
            ,   toString:   function() { return this.id.toString(); }
            ,   index:      i
            };
            for(j = 0; j < data.hours.length; j++) {
                hour = {
                    id:          parseInt(data.hours[j].id)
                ,   start:       data.hours[j].start
                ,   end:         data.hours[j].end
                ,   examcourses: new ArrMap()
                ,   toString:    function() { return this.id.toString(); }
                };
                date.hours.push(hour);
            }
            dates.push(date);
        }
        
        totalAssignmentsTodo = 0;
        
        for(i = 0; i < data.examcourses.length; i++) {
            examcourse = new ExamCourse(data.examcourses[i]);
            
            dates.get(examcourse.dateId)
                 .hours.get(examcourse.hourId)
                       .examcourses.push(examcourse);
            examcourses.push(examcourse);
            
            totalAssignmentsTodo += examcourse.getNumRemainingSups();
        }
        
        assigner.setTotalAssignmentsTodo(totalAssignmentsTodo);
        
        // Create schedule/assignments table
        
        for(i = 0; i < dates.length; i++) {
            date = dates.at(i);
            for(j = 0; j < date.hours.length; j++) {
                hour = date.hours.at(j);
                for(k = 0; k < hour.examcourses.length; k++) {
                    examcourse = hour.examcourses.at(k);
                    if(k > 0) {
                        examcourse.view.hideHour();
                    }
                    if(j > 0) {
                        examcourse.view.hideDate();
                    }
                    container.appendChild(examcourse.getDomNode());
                }
            }
        }
        
    }
    
    function buildDoctorals(data) {
        
        // map namespaced types and variables locally
        var Doctoral    = Scheduler.Assigner.Doctoral
        ,   doctorals   = Scheduler.Assigner.doctorals
        ;
        // local variables declaration
        var container = document.getElementById("doctorals").getElementsByTagName("tbody")[0]
        ,   doctoral
        ,   i
        ;
        
        // Build doctorals list
        
        for(i = 0; i < data.length; i++) {
            doctoral = new Doctoral(data[i]);
            doctorals.push(doctoral);
        }
        
        // Create doctorals table
        
        for(i = 0; i < doctorals.length; i++) {
            container.appendChild(doctorals.at(i).getDomNode());
        }
        
    }
    
    function addAvailabilities(data) {
        
        // map namespaced types and variables locally
        var doctorals   = Scheduler.Assigner.doctorals
        ,   examcourses = Scheduler.Assigner.examcourses
        ;
        // local variables declaration
        var doctoral
        ,   examcourse
        ,   availabilities
        ,   i, j
        ;
        
        for(i = 0; i < data.length; i++) {
            doctoral = doctorals.get(parseInt(data[i].id), true);
            availabilities = data[i].availabilities;
            for(j = 0; j < availabilities.length; j++) {
                examcourse = examcourses.get(availabilities[j], true);
                // add this doctoral's availabilities both for herself and her
                // available examcourses.
                doctoral.addAvailability(examcourse);
                examcourse.addAvailable(doctoral);
            }
        }
        
        doctorals.arr.forEach(function(doctoral) {
            doctoral.view.updateInitAvailabilities();
        });
    }
    
    function addAssignments(data) {
        
        // map namespaced types and variables locally
        var doctorals   = Scheduler.Assigner.doctorals
        ,   examcourses = Scheduler.Assigner.examcourses
        ,   assigner    = Scheduler.Assigner.assigner
        ;
        // local variables declaration
        var examcourse
        ,   doctoral
        ,   assignments
        ,   i, j
        ;
        
        for(i = 0; i < data.length; i++) {
            examcourse = examcourses.get(parseInt(data[i].id), true);
            assignments = data[i].assignments;
            for(j = 0; j < assignments.length; j++) {
                doctoral = doctorals.get(assignments[j], true);
                if(doctoral) {
                    assigner.makeAssignment(examcourse, doctoral);
                }
            }
        }
        
        assigner.assignmentsStack = [];
    }

    function buildSelectDoctoralsModal() {

        var $doctorals = $('#sendEmailsModal .doctorals');

        $doctorals.on('click', '.doctoral', function(e) {
            $(this).toggleClass('selected');
        });
    }
    
    /* ********************************************************************** */
    
    
    
});