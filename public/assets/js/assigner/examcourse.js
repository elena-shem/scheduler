$(function() {
    
    /* *************************************************************************
     * ExamCourse: Model
     * ************************************************************************/
    
    var ExamCourse = function(ec)
    {
        var ArrMap = Scheduler.ArrMap
        ;
        
        this.id = parseInt(ec.id);
        this.dateId = parseInt(ec.dateId);
        this.hourId = parseInt(ec.hourId);
        this.courseId = parseInt(ec.courseId);
        this.courseName = ec.courseName.trim();
        this.courseNameMin = ec.courseNameMin.trim();
        this.courseCode = ec.courseCode;
        this.professors = ec.professors;
        this.supsNeeded = parseInt(ec.supsNeeded);
        
        // contains the doctorals that:
        // * have declared availability for this examcourse.
        // * are not assigned to this examcourse nor to neighbour examcourses.
        // * have NOT YET filled their required number of assignments.
        this.availabilities1 = new ArrMap();
        
        // contains the doctorals that:
        // * have declared availability for this examcourse.
        // * are assigned to this examcourse.
        //   OR are assigned to a neighbour examcourse.
        //   OR have FILLED their required number of assignments.
        this.availabilities2 = new ArrMap();
        
        // contains the doctorals assigned to this examcourse.
        this.assignments = new ArrMap();
        
        // contains the doctorals assigned to neighbour examcourses.
        this.nAssignments = new ArrMap();
        
        // examcourse's view.
        this.view = new ExamCourse.View(this);
    };
    
    ExamCourse.prototype.toString = function()
    {
        return this.id.toString();
    };
    
    
    /**
     * "get/read" methods without the word "Live" in their name, that return
     * values unaffected by any assignments.
     */
    
    // returns the needed supervisions in total.
    ExamCourse.prototype.getNumRemainingSups = function()
    {
        return this.supsNeeded;
    };
    
    ExamCourse.prototype.getNumAvailabilities = function()
    {
        return this.availabilities1.length + this.availabilities2.length;
    };
    
    // slow function, avoid using it often if not necessary.
    ExamCourse.prototype.getAvailabilities = function()
    {
        return this.availabilities1.concat(this.availabilities2);
    };
    
    ExamCourse.prototype.hasAvailable = function(doctoral)
    {
        return this.availabilities1.contains(doctoral) || this.availabilities2.contains(doctoral);
    };
    
    
    /**
     * "get/read" methods with the word "Live" in their name, that return values
     * affected by the current state of all the assignments done up till now.
     */
    
    ExamCourse.prototype.getLiveNumRemainingSups = function()
    {
        return this.getNumRemainingSups() - this.getNumAssignedSups();
    };
    
    ExamCourse.prototype.getLiveNumAvailabilities = function()
    {
        return this.availabilities1.length;
    };
    
    ExamCourse.prototype.getLiveAvailabilities = function()
    {
        return this.availabilities1;
    };
    
    ExamCourse.prototype.hasLiveAvailable = function(doctoral)
    {
        return this.availabilities1.contains(doctoral);
    };
    
    
    /**
     * rest of the "get/read" methods.
     */
    
    ExamCourse.prototype.getNumAssignedSups = function()
    {
        return this.assignments.length;
    };
    
    ExamCourse.prototype.getAssignments = function()
    {
        return this.assignments;
    };
    
    ExamCourse.prototype.hasAssigned = function(doctoral)
    {
        return this.assignments.contains(doctoral);
    };
    
    ExamCourse.prototype.neighbourHasAssigned = function(doctoral)
    {
        return this.nAssignments.contains(doctoral);
    };
    
    ExamCourse.prototype.isFull = function()
    {
        return this.getLiveNumRemainingSups() <= 0;
    };
    
    ExamCourse.prototype.getDomNode = function()
    {
        return this.view.node;
    };
    
    
    /**
     * "set/write" methods.
     */
    
    ExamCourse.prototype.addAvailable = function(doctoral)
    {
        if(this.hasAssigned(doctoral) || this.neighbourHasAssigned(doctoral) || doctoral.isFull()) {
            this.availabilities2.push(doctoral);
        } else {
            this.availabilities1.push(doctoral);
            this.view.updateAvailabilities();
        }
    };
    
    /**
     * Moves, if needed, a doctoral from "availabilities1" to "availabilities2"
     * and vice-versa.
     */
    ExamCourse.prototype.modifyAvailable = function(doctoral)
    {
        if(this.availabilities1.contains(doctoral) && (this.hasAssigned(doctoral) || this.neighbourHasAssigned(doctoral) || doctoral.isFull())) {
            this.availabilities1.remove(doctoral);
            this.availabilities2.push(doctoral);
            this.view.updateAvailabilities();
        } else if(this.availabilities2.contains(doctoral) && !this.hasAssigned(doctoral) && !this.neighbourHasAssigned(doctoral) && !doctoral.isFull()) {
            this.availabilities2.remove(doctoral);
            this.availabilities1.push(doctoral);
            this.view.updateAvailabilities();
        }
    };
    
    /**
     * Just notify the examcourse's available doctorals that its state of
     * "filled required number of assignments" changed. The doctorals can
     * understand by themselves what's the new state.
     */
    ExamCourse.prototype.notifyAvailables = function()
    {
        var availabilities = this.availabilities1.arr
        ,   i
        ;
        for(i = availabilities.length - 1; i >= 0; i--) {
            availabilities[i].modifyAvailability(this);
        }
        availabilities = this.availabilities2.arr;
        for(i = availabilities.length - 1; i >= 0; i--) {
            availabilities[i].modifyAvailability(this);
        }
    };
    
    /**
     * Because an assignment has many side effects, this method should not be
     * used without "full knowledge" of what an assignment means.
     * 
     * If you want to make an assignment, simply call "Assigner.makeAssignment"
     * which knows how to handle all the affected pieces.
     */
    ExamCourse.prototype.assign = function(doctoral)
    {
        // Contains the examcourses with the same date and hour as the given argument `examcourse`.
        var examcourses = window.Scheduler.Assigner
                            .dates.map[this.dateId]
                            .hours.map[this.hourId]
                            .examcourses.arr,
            i;
        
        if(this.hasAssigned(doctoral) || this.neighbourHasAssigned(doctoral)) {
            return false;
        }
        
        this.assignments.push(doctoral);
        for(i = 0; i < examcourses.length; i++) {
            if(examcourses[i] !== this) {
                examcourses[i].nAssignments.push(doctoral);
            }
            if(examcourses[i].availabilities1.contains(doctoral)) {
                examcourses[i].availabilities1.remove(doctoral);
                examcourses[i].availabilities2.push(doctoral);
                examcourses[i].view.updateAvailabilities();
            }
        }
        this.view.updateAssignedSups();
        this.view.assign(doctoral);
        
        return true;
    };
    
    /**
     * Because an assignment removal has many side effects, this method should
     * not be used without "full knowledge" of what an assignment does.
     * 
     * If you want to remove an assignment, simply call "Assigner.cancelAssignment"
     * which knows how to handle all the affected pieces.
     */
    ExamCourse.prototype.remove = function(doctoral)
    {
        // Contains the examcourses with the same date and hour as the given argument `examcourse`.
        var examcourses = window.Scheduler.Assigner
                            .dates.map[this.dateId]
                            .hours.map[this.hourId]
                            .examcourses.arr,
            i;
        
        if(!this.hasAssigned(doctoral)) {
            return false;
        }
        
        this.assignments.remove(doctoral);
        for(i = 0; i < examcourses.length; i++) {
            if(examcourses[i] !== this) {
                examcourses[i].nAssignments.remove(doctoral);
            }
            //if(examcourse.availabilities2.contains(doctoral) && !doctoral.isFull()) {
            //    examcourse.availabilities2.remove(doctoral);
            //    examcourse.availabilities1.push(doctoral);
            //    examcourse.view.updateAvailabilities();
            //}
        }
        this.view.updateAssignedSups();
        this.view.remove(doctoral);
        
        return true;
    };
    
    
    /* *************************************************************************
     * ExamCourse: View
     * ************************************************************************/
    
    ExamCourse.View = function(ec)
    {
        var dates      = window.Scheduler.Assigner.dates
        ;
        var node       = ExamCourse.View.template.cloneNode(true)
        ,   date       = node.getElementsByClassName("date")[0]
        ,   hour       = node.getElementsByClassName("hour")[0]
        ,   showInfo   = node.getElementsByClassName("showInfo")[0]
        ,   searchDoc  = node.getElementsByClassName("searchDoc")[0]
        ,   supsNeeded = node.getElementsByClassName("supsNeeded")[0]
        ;
        
        this.model = ec;
        
        // Set id.
        node.setAttribute("id", "ec-" + ec.id);
        
        // Set values for static fields.
        
        supsNeeded.textContent = ec.getNumRemainingSups();
        
        date.innerHTML = (function() {
            var date = dates.get(ec.dateId,true)
            ;
            return date.day + "<br>" + date.date;
        })();
        
        hour.textContent = (function() {
            var hour = dates.get(ec.dateId,true).hours.get(ec.hourId,true)
            ;
            return hour.start + " - " + hour.end;
        })();
        
        // Set values for dynamic fields. Because the fields are dynamic,
        // also store them in variables for faster future access.
        
        this.course = node.getElementsByClassName("course")[0];
        this.course.textContent = ec.courseNameMin;
        this.course.setAttribute("title", ec.courseName);
        if(ec.courseCode === 'DUM') {
            this.course.classList.add('dummy');
        }
        
        this.avCounter = node.getElementsByClassName("avCounter")[0];
        this.updateAvailabilities();
        
        this.supsAssigned = node.getElementsByClassName("supsAssigned")[0];
        this.updateAssignedSups();
        
        this.doctorals  = node.getElementsByClassName("doctorals")[0];
        
        // the node itself
        this.node = node;
        
        // Set event listeners and functionality
        
        showInfo.addEventListener("click", function() {
            ExamCourse.showInfo(ec);
        }, false);
        
        searchDoc.addEventListener("click", function() {
            console.log("searchDoc clicked");
        }, false);
        
        this.course.addEventListener("click", function() {
            var highlighter = window.Scheduler.Assigner.highlighter
            ;
            highlighter.selectExamcourse(ec);
        }, false);
        
        // Accept dragged doctorals
        this.doctorals.addEventListener("dragenter", function(e) {
            // e.dataTransfer.getData("text/plain") does not work at least in Chrome.
            var docId = window.Scheduler.Assigner.draggedDocId
            ;
            if(ec.hasAssigned(docId) || ec.neighbourHasAssigned(docId)) {
                this.classList.add('drag-target-invalid');
            } else if(!ec.hasAvailable(docId)) {
                this.classList.add('drag-target-warning');
            } else {
                this.classList.add('drag-target-valid');
            }
            e.preventDefault();
        });
        this.doctorals.addEventListener("dragleave", function(e) {
            var docId = parseInt(e.dataTransfer.getData("text/plain"))
            ;
            this.classList.remove('drag-target-valid', 'drag-target-invalid', 'drag-target-warning');
            e.preventDefault();
        });
        this.doctorals.addEventListener("dragover", function(e) {
            var docId = parseInt(e.dataTransfer.getData("text/plain"))
            ;
            e.preventDefault();
        });
        this.doctorals.addEventListener("drop", function(e) {
            var assigner = window.Scheduler.Assigner.assigner
            ,   docId    = parseInt(e.dataTransfer.getData("text/plain"))
            ,   doctoral = window.Scheduler.Assigner.doctorals.get(docId, true)
            ;
            this.classList.remove('drag-target-valid', 'drag-target-invalid', 'drag-target-warning');
            assigner.makeAssignment(ec, doctoral);
            e.preventDefault();
        });
    };
    
    ExamCourse.View.template = (function() {
        var node = document.getElementById("template-examcourse")
        ;
        node.remove();
        node.removeAttribute("id");
        node.classList.remove("hide");
        return node;
    })();
    
    ExamCourse.View.templateDoc = (function() {
        var node = document.getElementById("template-examcourse-doctoral")
        ;
        node.remove();
        node.removeAttribute("id");
        node.classList.remove("hide");
        return node;
    })();
    
    ExamCourse.View.prototype.hideDate = function()
    {
        var date = this.node.getElementsByClassName("date")[0];
        date.innerHTML = "&nbsp;<br>&nbsp;";
        date.classList.add("merge-with-above");
    };
    
    ExamCourse.View.prototype.hideHour = function()
    {
        var date = this.node.getElementsByClassName("date")[0];
        var hour = this.node.getElementsByClassName("hour")[0];
        date.innerHTML = "&nbsp;<br>&nbsp;";
        hour.innerHTML = "&nbsp;<br>&nbsp;";
        date.classList.add("merge-with-above");
        hour.classList.add("merge-with-above");
    };
    
    ExamCourse.View.prototype.updateAvailabilities = function(value)
    {
        value = (value === undefined ? this.model.getLiveNumAvailabilities() : value);
        
        this.avCounter.textContent = value;
        if(parseInt(value) > 0) {
            this.avCounter.classList.remove("onlimit");
        } else {
            this.avCounter.classList.add("onlimit");
        }
    };
    
    ExamCourse.View.prototype.updateAssignedSups = function(value)
    {
        var limit = this.model.getNumRemainingSups()
        ,   node1 = this.supsAssigned.nextElementSibling    // <td>: "/"
        ,   node2 = node1.nextElementSibling                // <td>: "supsObliged"
        ;
        value = (value === undefined ? this.model.getNumAssignedSups() : value);
        
        this.supsAssigned.textContent = value;
        if(parseInt(value) < limit) {
            this.supsAssigned.classList.remove("completed", "overflowed");
            node1.classList.remove("completed", "overflowed");
            node2.classList.remove("completed", "overflowed");
        } else if(parseInt(value) === limit) {
            this.supsAssigned.classList.remove("overflowed");
            node1.classList.remove("overflowed");
            node2.classList.remove("overflowed");
            this.supsAssigned.classList.add("completed");
            node1.classList.add("completed");
            node2.classList.add("completed");
        } else {
            this.supsAssigned.classList.remove("completed");
            node1.classList.remove("completed");
            node2.classList.remove("completed");
            this.supsAssigned.classList.add("overflowed");
            node1.classList.add("overflowed");
            node2.classList.add("overflowed");
        }
    };
    
    ExamCourse.View.prototype.assign = function(doctoral)
    {
        var highlighter = window.Scheduler.Assigner.highlighter
        ;
        var node = ExamCourse.View.templateDoc.cloneNode(true)
        ,   ec   = this.model
        ;
        
        node.textContent = doctoral.name;
        node.classList.add("doc-" + doctoral.id);
        if(!doctoral.isAvailable(ec)) {
            node.classList.add("notAvailable");
        }
        
        if(window.Scheduler.Assigner.active === '1') {
            node.addEventListener("click", function() {
                var assigner = window.Scheduler.Assigner.assigner
                ;
                assigner.cancelAssignment(ec, doctoral);
            });
        }
        this.doctorals.appendChild(node);
        
        if(highlighter.selectedDoctoral === doctoral) {
            highlighter.highlightExamcourseForDoctoral(this.model, doctoral);
        }
        if(highlighter.selectedExamcourse === this.model) {
            highlighter.highlightDoctoralForExamcourse(doctoral, this.model);
        }
    };
    
    ExamCourse.View.prototype.remove = function(doctoral)
    {
        var highlighter = window.Scheduler.Assigner.highlighter
        ,   node = this.doctorals.getElementsByClassName("doc-" + doctoral.id)[0]
        ;
        
        // event listeners will be removed as well by GC.
        this.doctorals.removeChild(node);
        
        if(highlighter.selectedDoctoral === doctoral) {
            highlighter.highlightExamcourseForDoctoral(this.model, doctoral);
        }
        if(highlighter.selectedExamcourse === this.model) {
            highlighter.highlightDoctoralForExamcourse(doctoral, this.model);
        }
    };
    
    
    /* *************************************************************************
     * ExamCourse: Info modal
     * ************************************************************************/
    
    ExamCourse.showInfo = (function() {
        
        var modal        = document.getElementById("modal-examcourse-info")
        ,   date         = modal.getElementsByClassName("date")[0]
        ,   day          = modal.getElementsByClassName("day")[0]
        ,   hour         = modal.getElementsByClassName("hour")[0]
        ,   course       = modal.getElementsByClassName("course")[0]
        ,   professors   = modal.getElementsByClassName("professors")[0]
        ,   avCounter    = modal.getElementsByClassName("avCounter")[0]
        ,   supsAssigned = modal.getElementsByClassName("supsAssigned")[0]
        ,   supsNeeded   = modal.getElementsByClassName("supsNeeded")[0]
        ,   doctorals    = modal.getElementsByClassName("doctorals")[0]
        ,   doctoralsEmails = modal.getElementsByClassName("doctoralsEmails")[0].getElementsByTagName('a')[0]
        ,   $modal       = $(modal)
        ;
        
        return function(ec) {
            var d = window.Scheduler.Assigner.dates.get(ec.dateId,true)
            ,   h = window.Scheduler.Assigner.dates.get(ec.dateId,true).hours.get(ec.hourId,true)
            ;
            
            date.textContent         = d.date;
            day.textContent          = d.day;
            hour.textContent         = h.start + " - " + h.end;
            course.textContent       = ec.courseName;
            professors.innerHTML     = ec.professors.join('<br>');
            avCounter.textContent    = ec.getLiveNumAvailabilities();
            supsAssigned.textContent = ec.getNumAssignedSups();
            supsNeeded.textContent   = ec.getNumRemainingSups();
            doctorals.innerHTML      = ec.assignments.arr.map(function(doctoral, index) {
                return (index > 0 ? '<br>' : '') + doctoral.name;
            }).join('');
            doctoralsEmails.textContent = ec.assignments.arr.map(function(doctoral) {
                return doctoral.email;
            }).join(',');
            doctoralsEmails.setAttribute('href', 'mailto:' + doctoralsEmails.textContent);
            
            course.classList[ec.courseCode === 'DUM' ? 'add' : 'remove']('dummy');
            
            $modal.modal("show");
        };
        
    })();
    
    
    /* *************************************************************************
     * namespace definition
     * ************************************************************************/
    
    window.Scheduler = window.Scheduler || {};
    window.Scheduler.Assigner = window.Scheduler.Assigner || {};
    window.Scheduler.Assigner.ExamCourse = ExamCourse;
    
});