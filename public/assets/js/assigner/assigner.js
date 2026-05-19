$(function() {
    var BASE = (window.APP_BASE ? window.APP_BASE : (location.protocol + "//" + location.host));

    
    /* *************************************************************************
     * Assigner: Model
     * ************************************************************************/
    
    var Assigner = function()
    {
        var assigner = this
        ;
        
        this.timeoutId = null;
        this.totalAssignmentsTodo = 0;
        this.totalAssignmentsDone = 0;
        this.assignmentsStack = [];
        
        this.view = new Assigner.View(this);
        this.view.setTotalAssignmentsDone(0);
        this.view.setTotalAssignmentsTodo(0);
        
        document.addEventListener("keydown", function(event) {
            // ignore repeat key events for the same key, when the key is
            // pressed down for some time.
            if(event.repeat === true) {
                return;
            }
            if(event.key === " " || event.keyCode === 32) {
                assigner.toggle();
                event.preventDefault();
            } else if(event.key === "ArrowLeft" || event.keyCode === 37) {
                assigner.undoAssignment();
                event.preventDefault();
            } else if(event.key === "ArrowRight" || event.keyCode === 39) {
                // produce next assignment only if assigner is not running.
                if(assigner.timeoutId === null) {
                    assigner.produceAssignment();
                }
                event.preventDefault();
            }
        });
    };
    
    /**
     * Takes a pair of "ExamCourse" and "Doctoral" objects as arguments and
     * makes the assignment. Returns `true` if assignment succeeded or `false`
     * if it failed.
     * 
     * In practice, the result of calling this method is the update of all
     * required structures, values and views. So, after you have decided that
     * a doctoral should be assigned to an examcourse, then simply call this
     * method and let it handle everything.
     */
    Assigner.prototype.makeAssignment = function(examcourse, doctoral)
    {
        if(doctoral.assignTo(examcourse)) {
            if(examcourse.assign(doctoral)) {
                
                // if examcourse just completed the number of required assignments.
                if(examcourse.getNumAssignedSups() === examcourse.getNumRemainingSups()) {
                    examcourse.notifyAvailables();
                }
                // if doctoral just completed the number of required supervisions.
                if(doctoral.getNumAssignedSups() === doctoral.getNumRemainingSups()) {
                    doctoral.notifyAvailabilities();
                }
                
                // only if the assignments on this course are not more than needed
                if(examcourse.getNumAssignedSups() <= examcourse.getNumRemainingSups()) {
                    this.view.setTotalAssignmentsDone(++this.totalAssignmentsDone);
                }
                // add assignment to the "assignments stack".
                this.assignmentsStack.push(examcourse.id + "x" + doctoral.id);
                
                return true;
            } else {
                // Normally it should never enter here. But in case the
                // assignment on the examcourse fails, then also cancel the
                // assignment on the doctoral.
                doctoral.removeFrom(examcourse);
                return false;
            }
        } else {
            // assignment already done.
            return false;
        }
    };
    
    /**
     * Takes a pair of "ExamCourse" and "Doctoral" objects as arguments and
     * removes that assignment. Returns `true` if assignment removal succeeded
     * or `false` if it failed.
     * 
     * In practice, the result of calling this method is the update of all
     * required structures, values and views. So, after you have decided that
     * a doctoral should be removed from an examcourse, then simply call this
     * method and let it handle everything.
     */
    Assigner.prototype.cancelAssignment = function(examcourse, doctoral, isInStack)
    {
        if(doctoral.removeFrom(examcourse)) {
            if(examcourse.remove(doctoral)) {
                
                // if examcourse had filled the number of required assignments, before the removal.
                if(examcourse.getLiveNumRemainingSups() === 1) {
                    examcourse.notifyAvailables();
                }
                // if doctoral had filled the number of required assignments, before the removal.
                if(doctoral.getLiveNumRemainingSups() === 1) {
                    doctoral.notifyAvailabilities();
                }
                
                
                var examcourses = window.Scheduler.Assigner
                            .dates.map[examcourse.dateId]
                            .hours.map[examcourse.hourId]
                            .examcourses.arr,
                i;
                for(i = 0; i < examcourses.length; i++) {
                    examcourses[i].modifyAvailable(doctoral);
                    doctoral.modifyAvailability(examcourses[i]);
                }
                
                
                // only if the assignments on this course are not more than needed
                if(examcourse.getLiveNumRemainingSups() > 0) {
                    this.view.setTotalAssignmentsDone(--this.totalAssignmentsDone);
                }
                // remove the assignment from the "assignments stack".
                if(isInStack === undefined || isInStack === true) {
                    this.assignmentsStack.splice(this.assignmentsStack.indexOf(examcourse.id + "x" + doctoral.id), 1);
                }
                
                return true;
            } else {
                // Normally it should never enter here. But in case the
                // assignment removal on the examcourse fails, then re-add the
                // assignment on the doctoral.
                doctoral.assignTo(examcourse);
                return false;
            }
        } else {
            // assignment does not exist.
            return false;
        }
    };
    
    Assigner.prototype.setTotalAssignmentsTodo = function(value)
    {
        this.totalAssignmentsTodo = value;
        this.view.setTotalAssignmentsTodo(value);
    };
    
    
    
    
    
    
    /**
     * This method selects a pair of "ExamCourse" and "Doctoral" as good
     * candidates for the next assignment.
     * 
     * Basically, this is the "key function" that produces automatic assignments.
     * If you want multiple automatic assignments (such as fill the whole slots)
     * then simply call this method repeatedly until you are satisfied or it
     * failes to produce a pair.
     * 
     * TBD: Either this function returns the pair of "ExamCourse" and "Doctoral"
     *      or it calls "makeAssignment" for that pair.
     * 
     */
    Assigner.prototype.produceAssignment = function()
    {
        var doctorals = window.Scheduler.Assigner.doctorals.arr
        ,   dates = window.Scheduler.Assigner.dates
        ,   doctoral
        ,   examcourses
        ,   examcourse
        ;
        
        // Assigner WILL HAVE TO maintain an internal list with the doctorals that
        // still are valid for assignments (those who still have live availabilities).
        // 
        // This is one method to avoid probable early locking. Lock example:
        // make "Βλάχος Σπύρος" to be the choice for an early assignment...
        // 
        
        //====: SORT DOCTORALS AND CHOOSE BEST NEXT
        
        doctorals.sort(function(doc1, doc2) {
            var remSups
            ,   remAvs
            ,   w1 = 0
            ,   w2 = 0
            ;
            
          // 1st doctoral
            remSups = doc1.getLiveNumRemainingSups();
            remAvs  = doc1.getLiveNumAvailabilities();
            if(remSups <= 0) {
                // if someone has completed her obliged sups, then raise the denominator [if it was zero it must remain zero]
                remAvs *= Math.pow(10, -remSups);
                //remSups -= 1;
            }
            if(remAvs > 0) {
                // the magic formula !!!
                w1 += remSups / remAvs;
            }
            // if someone has not completed her obliged sups and still has some availabilities, add her bonus weight
            if(remSups > 0 && remAvs > 0) {
                w1 += doc1.bonusWeight;
            }
            
          // The same for the 2nd doctoral
            remSups = doc2.getLiveNumRemainingSups();
            remAvs  = doc2.getLiveNumAvailabilities();
            if(remSups <= 0) { remAvs *= Math.pow(10, -remSups); }
            if(remAvs > 0) { w2 += remSups / remAvs; }
            if(remSups > 0 && remAvs > 0) { w2 += doc2.bonusWeight; }
            
          // continue with more calculations for both of them
          
            // if a few hours remaining to finish his doctoral obligations then raise his weight.
            if(doc1.getLiveNumRemainingHours() < 15) {
                w1 *= 10;
            }
            if(doc2.getLiveNumRemainingHours() < 15) {
                w2 *= 10;
            }
            
            // if finished his doctoral obligations simply do not count him.
            if(doc1.getLiveNumRemainingHours() <= 0) {
                w1 = -1000;
            }
            if(doc2.getLiveNumRemainingHours() <= 0) {
                w2 = -1000;
            }
            
            return w2 - w1;
        });
        
        var assignments,
            i, n;
        
        for(i = 0, n = doctorals.length; i < n; i++) {
            doctoral = doctorals[i];
            if(doctoral.getLiveNumRemainingSups() <= 0) {
                continue;
            }
            assignments = {};
            for(var j = 0, m = doctoral.assignments.arr.length; j < m; j++) {
                assignments[doctoral.assignments.arr[j].dateId + 'x' + doctoral.assignments.arr[j].hourId] = null;
            }
            examcourses = doctoral.getLiveAvailabilities().arr.filter(function(examcourse) {
                return !assignments.hasOwnProperty(examcourse.dateId + 'x' + examcourse.hourId);
            });
            if(examcourses.length > 0) {
                break;
            }
        }
        
        // If no doctoral was appropriate then stop assigning.
        if(i === n) {
            this.pause();
            return false;
        }
        
        var getNumSameDayExamcourses = function(dayIndex)
        {
            var examcourses = doctoral.assignments.arr
            ,   i
            ,   num = 0
            ;
            for(i = examcourses.length-1; i >= 0; i--) {
                if(dates.get(examcourses[i].dateId).index === dayIndex) {
                    num += 1;
                }
            }
            return num;
        };
        
        var getSmallestDistance = function(dayIndex)
        {
            var examcourses = doctoral.assignments.arr
            ,   i
            ,   temp
            ,   smallestDistance = 1000
            ;
            for(i = examcourses.length-1; i >= 0; i--) {
                temp = Math.abs(dates.get(examcourses[i].dateId).index - dayIndex);
                if(temp < smallestDistance) {
                    smallestDistance = temp;
                }
            }
            return smallestDistance;
        };
        
        var dd = parseInt(doctoral.densityDay);
        
        var d = doctoral.density;
        
        //====: SORT EXAMCOURSES AND CHOOSE NEXT BEST FOR THE CHOSEN DOCTORAL
        
        examcourses.sort(function(ec1, ec2) {
            var remSups1 = ec1.getLiveNumRemainingSups()
            ,   remAvs1 = ec1.getLiveNumAvailabilities()
            ,   di1 = dates.get(ec1.dateId).index
            ,   w1 = 0
            
            ,   remSups2 = ec2.getLiveNumRemainingSups()
            ,   remAvs2 = ec2.getLiveNumAvailabilities()
            ,   di2 = dates.get(ec2.dateId).index
            ,   w2 = 0
            
            ,   num
            ,   ww // what weight give to a weight 8D
            ;
            
            ww = 1;
            if(remSups1 > 0 && remAvs1 > 0) {
                w1 += ( remSups1 / remAvs1 ) * ww;
            }
            if(remSups2 > 0 && remAvs2 > 0) {
                w2 += ( remSups2 / remAvs2 ) * ww;
            }
            
            if(w1 > 10) console.log(w1);
            if(w2 > 10) console.log(w2);
            
            ww = 10000;
            // if doctoral has declared a densityDay preference
            if(!isNaN(dd) && dd > 0) {
              // for the 1st examcourse:
                // get how many assignments the doctoral has on this examcourse' day
                num = getNumSameDayExamcourses(di1);
                // if the doctoral is already assigned in that day at least once
                if(num >= 1) {
                    // weight is the remaining supervisions based on the doctoral's preference.
                    // if has been assigned so many times as he wanted give a negative weight.
                    w1 += (doctoral.densityDay - num) * ww;
                }
              // the same for 2nd examcourse:
                num = getNumSameDayExamcourses(di2);
                if(num >= 1) {
                    w2 += (doctoral.densityDay - num) * ww;
                }
            }
            
            ww = 10;
            // if doctoral has declared a density preference
            if(d === "sparse") {
              // for the 1st examcourse:
                num = getSmallestDistance(di1);
                // if the doctoral is assigned on at least one other day
                if(num > 0) {
                    w1 += Math.min(num, 4) * 10 * ww;
                }
              // the same for the 2nd examcourse;
                num = getSmallestDistance(di2);
                // if the doctoral is assigned on at least one other day
                if(num > 0) {
                    w2 += Math.min(num, 4) * 10 * ww;
                }
            } else if(d === "dense") {
              // for the 1st examcourse:
                num = getSmallestDistance(di1);
                // if the doctoral is assigned on at least one other day
                if(num > 0) {
                    w1 += Math.round(10 / num) * ww;
                }
              // the same for the 2nd examcourse;
                num = getSmallestDistance(di2);
                // if the doctoral is assigned on at least one other day
                if(num > 0) {
                    w2 += Math.round(10 / num) * ww;
                }
            }
            
            return w2 - w1;
        });
        examcourse = examcourses[0];
        
        if(this.makeAssignment(examcourse, doctoral) === false) {
            this.pause();
            return false;
        }
        
        return true;
    };
    
    
    
    
    
    
    Assigner.prototype.undoAssignment = function()
    {
        var examcourses = window.Scheduler.Assigner.examcourses
        ,   doctorals   = window.Scheduler.Assigner.doctorals
        ;
        var assignment
        ,   examcourse
        ,   doctoral
        ;
        if(this.assignmentsStack.length > 0) {
            assignment = this.assignmentsStack.pop().split("x");
            examcourse = examcourses.get(assignment[0], true);
            doctoral   = doctorals.get(assignment[1], true);
            this.cancelAssignment(examcourse, doctoral, false);
        }
    };
    
    Assigner.prototype.clearAssignments = function()
    {
        var examcourses = window.Scheduler.Assigner.examcourses
        ;
        var examcourse
        ,   length
        ,   i
        ;
        this.assignmentsStack = [];
        for(i = 0; i < examcourses.length; i++) {
            examcourse = examcourses.at(i);
            length = examcourse.assignments.length;
            while(length) {
                this.cancelAssignment(examcourse, examcourse.assignments.at(length-1), false);
                length = examcourse.assignments.length;
            }
        }
    };
    
    Assigner.prototype.resetAssignments = function()
    {
        document.location.reload();
    };
    
    Assigner.prototype.getAssignments = function()
    {
        var examcourses = window.Scheduler.Assigner.examcourses
        ;
        var examcourse
        ,   assignments
        ,   i
        ,   j
        ,   doctorals
        ,   storeData = []
        ;
        for(i = 0; i < examcourses.length; i++) {
            examcourse = examcourses.at(i);
            assignments = examcourse.getAssignments();
            doctorals = [];
            for(j = 0; j < assignments.length; j++) {
                doctorals.push(assignments.at(j).id);
            }
            storeData.push({
                examcourseID: examcourse.id,
                doctoralIDs:  doctorals
            });
        }
        
        return storeData;
    };
    
    Assigner.prototype.closeAssignments = function()
    {
        $('#modalCloseWarning').modal('show');
    };
    
    Assigner.prototype.play = function()
    {
        var assigner = this
        ,   period = 10
        ;
        
        this.timeoutId = setTimeout(function tick() {
            if(assigner.produceAssignment()) {
                assigner.timeoutId = setTimeout(tick, period);
            }
        }, period);
        
        this.view.updateToggleButton(true);
    };
    
    Assigner.prototype.pause = function()
    {
        clearTimeout(this.timeoutId);
        this.timeoutId = null;
        
        this.view.updateToggleButton(false);
    };
    
    Assigner.prototype.toggle = function()
    {
        if(this.timeoutId !== null) {
            this.pause();
        } else {
            this.play();
        }
    };
    
    
    /* *************************************************************************
     * Assigner: View
     * ************************************************************************/
    
    Assigner.View = function(assigner)
    {
        var undoAssignment   = document.getElementById("undoAssignment")
        ,   nextAssignment   = document.getElementById("nextAssignment")
        ,   clearAssignments = document.getElementById("clearAssignments")
        ,   resetAssignments = document.getElementById("resetAssignments")
        ,   selectDoctorals  = document.getElementById("selectDoctoralsToSendEmail")
        ,   selectNone       = document.getElementById("selectDoctoralsNone")
        ,   selectAll        = document.getElementById("selectDoctoralsAllAssigned")
        ,   sendEmails       = document.getElementById("sendEmails")
        ,   downloadExcel    = document.getElementById("downloadExcel")
        ,   saveAssignments  = document.getElementById("saveAssignments")
        ,   closeAssignments = document.getElementById("closeAssignments")
        ;
        
        this.model = assigner;
        this.totalAssignmentsDone = document.getElementById("totalAssignmentsDone");
        this.totalAssignmentsTodo = document.getElementById("totalAssignmentsTodo");
        this.toggleAssigner = document.getElementById("toggleAssigner");
        
        this.toggleAssigner.addEventListener("click", function() {
            assigner.toggle();
        });
        
        undoAssignment.addEventListener("click", function() {
            assigner.undoAssignment();
        });
        
        nextAssignment.addEventListener("click", function() {
            assigner.produceAssignment();
        });
        
        clearAssignments.addEventListener("click", function() {
            if(window.confirm("By clearing assignments all unsaved assignments will be lost. Are you sure?")) {
                assigner.clearAssignments();
            } else {
                return false;
            }
        });
        
        resetAssignments.addEventListener("click", function() {
            if(window.confirm("By reloading assignments all unsaved assignments will be lost. Are you sure?")) {
                assigner.resetAssignments();
            } else {
                return false;
            }
        });

        var $doctorals = $('#sendEmailsModal .doctorals'),
            $template  = $('#sendEmailsModal .doctoral').remove();
        selectDoctorals.addEventListener("click", function() {

            var doctorals = window.Scheduler.Assigner.doctorals.arr.filter(function(doctoral) {
                    return doctoral.getNumAssignedSups() > 0;
                });

            $doctorals.empty();
            doctorals.forEach(function(doctoral) {
                var $doctoral = $template.clone();
                $doctoral.find('.name').text(doctoral.name);
                $doctoral.attr('data-id', doctoral.id);
                $doctoral.addClass('selected');
                $doctorals.append($doctoral);
            });

            $("#sendEmailsModal").modal("show");
        });

        selectNone.addEventListener("click", function(e) {
            $('#sendEmailsModal .doctoral').removeClass('selected');
        });

        selectAll.addEventListener("click", function(e) {
            $('#sendEmailsModal .doctoral').addClass('selected');
        });

$(document).ready(function() {
    // 1. Физически переносим все модалки в корень body. 
    // Это на 100% решает проблему заблокированных кнопок и z-index.
    $('.modal').appendTo('body');
});

// Используем jQuery on('click') вместо addEventListener для совместимости с Bootstrap
$('#sendEmails').off('click').on('click', function(e) {
    e.preventDefault();

    // window.confirm ставит на паузу выполнение скриптов
    if(window.confirm("All unsaved assignments will NOT be taken into account. Are you sure?")) {
        var selectedDoctoralIds = [];

        // Оптимизированный селектор
        $('#sendEmailsModal .doctoral.selected').each(function() {
            selectedDoctoralIds.push($(this).attr('data-id'));
        });

        var url = BASE + "/admin/assignments/emails/send?exid=" + window.Scheduler.Assigner.exid + "&doctorals=" + selectedDoctoralIds.join(',');

        window.open(url, '_blank');
        $('#sendEmailsModal').modal('hide');
    } else {
        e.stopPropagation();
    }
});

// 2. Системная очистка подложки. 
// Срабатывает только когда Bootstrap сам закончит анимацию закрытия окна. Заменяет setTimeout.
$('#sendEmailsModal, #modalCloseWarning').on('hidden.bs.modal', function () {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
});
                
        downloadExcel.addEventListener("click", function() {
            var url = BASE + "/admin/excel/creator/create?exid=" + window.Scheduler.Assigner.exid;

            if(window.confirm("All unsaved assignments will NOT be included in the Excel Sheet. Are you sure?")) {
                window.open(url, '_blank');
            } else {
                return false;
            }
        });
        
        saveAssignments.addEventListener("submit", function(e) {
            this.querySelector('input[name="assignments"]').value = JSON.stringify(assigner.getAssignments());
        });
        
        closeAssignments.addEventListener("click", function() {
            assigner.closeAssignments();
        });
        
        $('#closeAssignmentsSubmission').submit(function(e) {
            $('#modalCloseWarning').modal('hide');
            if($('#closeAssignmentsConfirmation').prop('checked') === false) {
                return false;
            }
        });
    };
    
    Assigner.View.prototype.setTotalAssignmentsDone = function(value)
    {
        this.totalAssignmentsDone.textContent = value;
    };
    
    Assigner.View.prototype.setTotalAssignmentsTodo = function(value)
    {
        this.totalAssignmentsTodo.textContent = value;
    };
    
    Assigner.View.prototype.updateToggleButton = function(isRunning)
    {
        if(isRunning) {
            this.toggleAssigner.classList.remove("glyphicon-play");
            this.toggleAssigner.classList.add("glyphicon-pause", "running");
        } else {
            this.toggleAssigner.classList.remove("glyphicon-pause", "running");
            this.toggleAssigner.classList.add("glyphicon-play");
        }
        
    };
    
    
    /* *************************************************************************
     * namespace definition
     * ************************************************************************/
    
    window.Scheduler = window.Scheduler || {};
    window.Scheduler.Assigner = window.Scheduler.Assigner || {};
    window.Scheduler.Assigner.Assigner = Assigner;
    
});