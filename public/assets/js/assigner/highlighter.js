$(function() {
    
    /**
     * Highlighting on examcourses can happen only when a doctoral is selected.
     * 
     * Similary, highlighting on doctorals can happen only when an examcourse
     * is selected.
     */
    
    var Highlighter = function()
    {
        var highlighter = this
        ;
        
        this.selectedDoctoral = null;
        this.selectedExamcourse = null;
        
        document.addEventListener("keydown", function(event) {
            if((event.key === "Escape" || event.keyCode === 27) && event.repeat === false) {
                highlighter.deselectDoctoral();
                highlighter.deselectExamcourse();
            }
        });
        
        document.getElementById("removeSelections").addEventListener("click", function() {
            highlighter.deselectDoctoral();
            highlighter.deselectExamcourse();
        });
    };
    
    
    Highlighter.prototype.clearExamcourse = function(examcourse)
    {
        examcourse.view.course.classList.remove(
            "assigned-available",
            "assigned-unavailable",
            "unassigned-available",
            "unassigned-unavailable"
        );
    };
    
    Highlighter.prototype.deselectDoctoral = function()
    {
        var examcourses = window.Scheduler.Assigner.examcourses
        ,   i
        ;
        if(this.selectedDoctoral !== null) {
            for(i = 0; i < examcourses.length; i++) {
                this.clearExamcourse(examcourses.at(i));
            }
            this.selectedDoctoral.view.name.parentNode.classList.remove("selected");
            this.selectedDoctoral = null;
        }
    };
    
    Highlighter.prototype.highlightExamcourseForDoctoral = function(examcourse, doctoral)
    {
        this.clearExamcourse(examcourse);
        
        if(examcourse.assignments.contains(doctoral)) {
            if(examcourse.hasAvailable(doctoral)) {
                examcourse.view.course.classList.add("assigned-available");
            } else {
                examcourse.view.course.classList.add("assigned-unavailable");
            }
        } else {
            if(examcourse.hasAvailable(doctoral)) {
                examcourse.view.course.classList.add("unassigned-available");
            } else {
                examcourse.view.course.classList.add("unassigned-unavailable");
            }
        }
    };
    
    Highlighter.prototype.selectDoctoral = function(doctoral)
    {
        var examcourses = window.Scheduler.Assigner.examcourses
        ,   i
        ;
        this.deselectDoctoral();
        this.deselectExamcourse();
        
        for(i = 0; i < examcourses.length; i++) {
            this.highlightExamcourseForDoctoral(examcourses.at(i), doctoral);
        }
        this.selectedDoctoral = doctoral;
        this.selectedDoctoral.view.name.parentNode.classList.add("selected");
    };
    
    
    Highlighter.prototype.clearDoctoral = function(doctoral)
    {
        doctoral.view.name.parentNode.classList.remove(
            "assigned-available",
            "assigned-unavailable",
            "unassigned-available",
            "unassigned-unavailable"
        );
    };
    
    Highlighter.prototype.deselectExamcourse = function()
    {
        var doctorals = window.Scheduler.Assigner.doctorals
        ,   i
        ;
        if(this.selectedExamcourse !== null) {
            for(i = 0; i < doctorals.length; i++) {
                this.clearDoctoral(doctorals.at(i));
            }
            this.selectedExamcourse.view.course.classList.remove("selected");
            this.selectedExamcourse = null;
        }
    };
    
    Highlighter.prototype.highlightDoctoralForExamcourse = function(doctoral, examcourse)
    {
        this.clearDoctoral(doctoral);
        
        if(doctoral.assignments.contains(examcourse)) {
            if(doctoral.isAvailable(examcourse)) {
                doctoral.view.name.parentNode.classList.add("assigned-available");
            } else {
                doctoral.view.name.parentNode.classList.add("assigned-unavailable");
            }
        } else {
            if(doctoral.isAvailable(examcourse)) {
                doctoral.view.name.parentNode.classList.add("unassigned-available");
            } else {
                doctoral.view.name.parentNode.classList.add("unassigned-unavailable");
            }
        }
    };
    
    Highlighter.prototype.selectExamcourse = function(examcourse)
    {
        var doctorals = window.Scheduler.Assigner.doctorals
        ,   i
        ;
        this.deselectExamcourse();
        this.deselectDoctoral();
        
        for(i = 0; i < doctorals.length; i++) {
            this.highlightDoctoralForExamcourse(doctorals.at(i), examcourse);
        }
        this.selectedExamcourse = examcourse;
        this.selectedExamcourse.view.course.classList.add("selected");
    };
    
    
    /* *************************************************************************
     * namespace definition
     * ************************************************************************/
    
    window.Scheduler = window.Scheduler || {};
    window.Scheduler.Assigner = window.Scheduler.Assigner || {};
    window.Scheduler.Assigner.Highlighter = Highlighter;
    
});