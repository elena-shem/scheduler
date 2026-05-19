$(function() {
    
    /* *************************************************************************
     * Doctoral: Model
     * ************************************************************************/
    
    var Doctoral = function(doc)
    {
        var ArrMap = Scheduler.ArrMap
        ;
        
        this.id = parseInt(doc.id);
        this.name = (doc.surname.trim() + " " + doc.name.trim()).trim();
        this.professors = doc.professors;
        this.remainingHours = parseInt(doc.remainingHours);
        this.comment = doc.comment;
        this.density = doc.density;
        this.densityDay = doc.densityDay;
        this.supsObliged = Math.min(parseInt(doc.supsObliged), Math.ceil(this.remainingHours/3));
        this.bonusWeight = parseInt(doc.bonusWeight);
        this.email = doc.email.trim();

        // contains the examcourses that:
        // * the doctoral has declared availability for.
        // * the doctoral is not assigned to.
        // * have NOT YET filled their required number of assignments.
        this.availabilities1 = new ArrMap();
        
        // contains the examcourses that:
        // * the doctoral has declared availability for.
        // * the doctoral is assigned to.
        //   OR have FILLED their required number of assignments.
        this.availabilities2 = new ArrMap();
        
        // contains the examcourses that the doctoral is assigned to.
        this.assignments = new ArrMap();
        
        this.nAssignments = new ArrMap();
        
        // doctoral's view.
        this.view = new Doctoral.View(this);
    };
    
    Doctoral.prototype.toString = function()
    {
        return this.id.toString();
    };
    
    
    /**
     * "get/read" methods without the word "Live" in their name, that return
     * values unaffected by any assignments.
     */
    
    Doctoral.prototype.getNumCompletedHours = function()
    {
        // `90` should be fetched from the server instead of hardcoded
        return 90 - this.remainingHours;
    };
    
    Doctoral.prototype.getNumRemainingHours = function()
    {
        return this.remainingHours;
    };
    
    // returns the needed supervisions in total.
    Doctoral.prototype.getNumRemainingSups = function()
    {
        return this.supsObliged;
    };
    
    Doctoral.prototype.getNumAvailabilities = function()
    {
        return this.availabilities1.length + this.availabilities2.length;
    };
    
    Doctoral.prototype.getNumHourAvailabilities = function()
    {
        var num = 0,
            dayHours = {},
            key;
        var callback = function(examcourse) {
            key = examcourse.dateId + '.' + examcourse.hourId;
            if(!examcourse.isFull() && !dayHours.hasOwnProperty(key)) {
                dayHours[key] = null;
                num += 1;
            }
        };
        this.availabilities1.arr.forEach(callback);
        this.availabilities2.arr.forEach(callback);
        return num;
    };
    
    // slow function, avoid using it often if not necessary.
    Doctoral.prototype.getAvailabilities = function()
    {
        return this.availabilities1.concat(this.availabilities2);
    };
    
    Doctoral.prototype.isAvailable = function(examcourse)
    {
        return this.availabilities1.contains(examcourse) || this.availabilities2.contains(examcourse);
    };
    
    
    /**
     * "get/read" methods with the word "Live" in their name, that return values
     * affected by the current state of all the assignments done up till now.
     */
    
    Doctoral.prototype.getLiveNumCompletedHours = function()
    {
        return this.getNumCompletedHours() + this.getNumAssignedSups() * 3;
    };
    
    Doctoral.prototype.getLiveNumRemainingHours = function()
    {
        return this.getNumRemainingHours() - this.getNumAssignedSups() * 3;
    };
    
    Doctoral.prototype.getLiveNumRemainingSups = function()
    {
        return this.getNumRemainingSups() - this.getNumAssignedSups();
    };
    
    Doctoral.prototype.getLiveNumAvailabilities = function()
    {
        return this.availabilities1.length;
    };
    
    Doctoral.prototype.getLiveNumHourAvailabilities = function()
    {
        var num = 0,
            dayHours = {},
            key;
        this.availabilities1.arr.forEach(function(examcourse) {
            key = examcourse.dateId + '.' + examcourse.hourId;
            if(!dayHours.hasOwnProperty(key)) {
                dayHours[key] = null;
                num += 1;
            }
        });
        return num;
    };
    
    Doctoral.prototype.getLiveAvailabilities = function()
    {
        return this.availabilities1;
    };
    
    Doctoral.prototype.isLiveAvailable = function(examcourse)
    {
        return this.availabilities1.contains(examcourse);
    };
    
    
    /**
     * rest of the "get/read" methods.
     */
    
    Doctoral.prototype.getNumAssignedSups = function()
    {
        return this.assignments.length;
    };
    
    Doctoral.prototype.getAssignments = function()
    {
        return this.assignments;
    };
    
    Doctoral.prototype.isAssignedTo = function(examcourse)
    {
        return this.assignments.contains(examcourse);
    };
    
    Doctoral.prototype.isAssignedToNeighbourOf = function(examcourse)
    {
        return this.nAssignments.contains(examcourse);
    };
    
    Doctoral.prototype.isFull = function()
    {
        return this.getLiveNumRemainingSups() <= 0;
    };
    
    Doctoral.prototype.getDomNode = function()
    {
        return this.view.node;
    };
    
    
    /**
     * "set/write" methods.
     */
    
    Doctoral.prototype.addAvailability = function(examcourse)
    {
        if(this.isAssignedTo(examcourse) || this.isAssignedToNeighbourOf(examcourse) || examcourse.isFull()) {
            this.availabilities2.push(examcourse);
        } else {
            this.availabilities1.push(examcourse);
            this.view.updateAvailabilities();
        }
    };
    
    /**
     * Moves, if needed, an examcourse from "availabilities1" to "availabilities2"
     * and vice-versa.
     */
    Doctoral.prototype.modifyAvailability = function(examcourse)
    {
        if(this.availabilities1.contains(examcourse) && (this.isAssignedTo(examcourse) || this.isAssignedToNeighbourOf(examcourse) || examcourse.isFull())) {
            this.availabilities1.remove(examcourse);
            this.availabilities2.push(examcourse);
            this.view.updateAvailabilities();
        } else if(this.availabilities2.contains(examcourse) && !this.isAssignedTo(examcourse) && !this.isAssignedToNeighbourOf(examcourse) && !examcourse.isFull()) {
            this.availabilities2.remove(examcourse);
            this.availabilities1.push(examcourse);
            this.view.updateAvailabilities();
        }
    };
    
    /**
     * Just notify the doctoral's availibilities that her state of
     * "completed required number of supervisions" changed. The examcourses can
     * understand by themselves what's the new state.
     */
    Doctoral.prototype.notifyAvailabilities = function()
    {
        var availabilities = this.availabilities1.arr
        ,   i
        ;
        for(i = availabilities.length - 1; i >= 0; i--) {
            availabilities[i].modifyAvailable(this);
        }
        availabilities = this.availabilities2.arr;
        for(i = availabilities.length - 1; i >= 0; i--) {
            availabilities[i].modifyAvailable(this);
        }
    };
    
    /**
     * Because an assignment has many side effects, this method should not be
     * used without "full knowledge" of what an assignment means.
     * 
     * If you want to make an assignment, simply call "Assigner.makeAssignment"
     * which knows how to handle all the affected pieces.
     */
    Doctoral.prototype.assignTo = function(examcourse)
    {
        // Contains the examcourses with the same date and hour as the given argument `examcourse`.
        var examcourses = window.Scheduler.Assigner
                            .dates.map[examcourse.dateId]
                            .hours.map[examcourse.hourId]
                            .examcourses.arr,
            i;
        
        if(this.isAssignedTo(examcourse) || this.isAssignedToNeighbourOf(examcourse)) {
            return false;
        }
        
        this.assignments.push(examcourse);
        for(i = 0; i < examcourses.length; i++) {
            if(examcourses[i] !== examcourse) {
                this.nAssignments.push(examcourses[i]);
            }
            if(this.availabilities1.contains(examcourses[i])) {
                this.availabilities1.remove(examcourses[i]);
                this.availabilities2.push(examcourses[i]);
            }
        }
        this.view.updateAvailabilities();
        this.view.updateRemainingHours();
        this.view.updateAssignedSups();
        
        return true;
    };
    
    /**
     * Because an assignment removal has many side effects, this method should
     * not be used without "full knowledge" of what an assignment does.
     * 
     * If you want to remove an assignment, simply call "Assigner.cancelAssignment"
     * which knows how to handle all the affected pieces.
     */
    Doctoral.prototype.removeFrom = function(examcourse)
    {
        // Contains the examcourses with the same date and hour as the given argument `examcourse`.
        var examcourses = window.Scheduler.Assigner
                            .dates.map[examcourse.dateId]
                            .hours.map[examcourse.hourId]
                            .examcourses.arr,
            i;
        
        if(!this.isAssignedTo(examcourse)) {
            return false;
        }
        
        this.assignments.remove(examcourse);
        for(i = 0; i < examcourses.length; i++) {
            if(examcourses[i] !== examcourse) {
                this.nAssignments.remove(examcourses[i]);
            }
            //if(this.availabilities2.contains(examcourse) && !examcourse.isFull()) {
            //    this.availabilities2.remove(examcourse);
            //    this.availabilities1.push(examcourse);
            //}
        }
        //this.view.updateAvailabilities();
        this.view.updateRemainingHours();
        this.view.updateAssignedSups();
        
        return true;
    };
    
    
    /* *************************************************************************
     * Doctoral: View
     * ************************************************************************/
    
    Doctoral.View = function(doc)
    {
        var node        = Doctoral.View.template.cloneNode(true)
        ,   showInfo    = node.getElementsByClassName("showInfo")[0]
        ,   searchSlot  = node.getElementsByClassName("searchSlot")[0]
        ,   density     = node.getElementsByClassName("density")[0]
        ,   densityDay  = node.getElementsByClassName("densityDay")[0]
        ,   supsObliged = node.getElementsByClassName("supsObliged")[0]
        ;
        
        this.model = doc;
        
        // Set id.
        node.setAttribute("id", "doc-" + doc.id);
        
        // Set values for static fields.
        
        density.textContent = doc.density;
        densityDay.textContent = (doc.densityDay == 0 ? 1 : doc.densityDay);
        supsObliged.textContent = doc.supsObliged;
        
        // Set values for dynamic fields. Because the fields are dynamic,
        // also store them in variables for faster future access.
        
        this.name = node.getElementsByClassName("name")[0];
        this.name.textContent = doc.name;
        
        this.remHours = node.getElementsByClassName("remHours")[0];
        this.updateRemainingHours();
        
        this.avCounter = node.getElementsByClassName("avCounter")[0];
        this.avSlash = node.getElementsByClassName("avSlash")[0];
        this.avInitNum = node.getElementsByClassName("avInitNum")[0];
        this.updateAvailabilities();
        
        this.supsAssigned = node.getElementsByClassName("supsAssigned")[0];
        this.updateAssignedSups();
        
        // the node itself
        this.node = node;
        
        // Set event listeners and functionality
        
        showInfo.addEventListener("click", function() {
            Doctoral.showInfo(doc);
        }, false);
        
        searchSlot.addEventListener("click", function() {
            // ...
        }, false);
        
        this.name.parentNode.addEventListener("click", function() {
            var highlighter = window.Scheduler.Assigner.highlighter
            ;
            highlighter.selectDoctoral(doc);
        }, false);
        
        // Drag doctoral by her name
        
        if(window.Scheduler.Assigner.active === '1') {
            this.name.setAttribute("draggable", true);
            this.name.addEventListener("dragstart", function(e) {
                e.dataTransfer.setData("text/plain", doc.id);
                window.Scheduler.Assigner.draggedDocId = doc.id;
            });
            this.name.addEventListener("dragend", function(e) {
                window.Scheduler.Assigner.draggedDocId = null;
            });
        }
    };
    
    Doctoral.View.template = (function() {
        var node = document.getElementById("template-doctoral")
        ;
        node.remove();
        node.removeAttribute("id");
        node.classList.remove("hide");
        return node;
    })();
    
    Doctoral.View.prototype.hide = function()
    {
        this.node.hidden = true;
    };
    
    Doctoral.View.prototype.show = function()
    {
        this.node.hidden = false;
    };
    
    Doctoral.View.prototype.toggle = function()
    {
        this.node.hidden = !this.node.hidden;
    };
    
    Doctoral.View.prototype.updateRemainingHours = function(value)
    {
        value = (value === undefined ? this.model.getLiveNumRemainingHours() : value);
        
        this.remHours.textContent = value;
        if(value > 0) {
            this.remHours.classList.remove("completed", "overflowed");
        } else if(value === 0) {
            this.remHours.classList.remove("overflowed");
            this.remHours.classList.add("completed");
        } else {
            this.remHours.classList.remove("completed");
            this.remHours.classList.add("overflowed");
        }
    };
    
    Doctoral.View.prototype.updateAvailabilities = function(value)
    {
        value = (value === undefined ? this.model.getLiveNumHourAvailabilities() : value);
        
        this.avCounter.textContent = value;
        if(value >= this.model.getLiveNumRemainingSups()) {
            this.avCounter.classList.remove("onlimit");
            this.avSlash.classList.remove("onlimit");
            this.avInitNum.classList.remove("onlimit");
        } else {
            this.avCounter.classList.add("onlimit");
            this.avSlash.classList.add("onlimit");
            this.avInitNum.classList.add("onlimit");
        }
    };
    
    Doctoral.View.prototype.updateInitAvailabilities = function()
    {
        var value = this.model.getNumHourAvailabilities();
        
        this.avInitNum.textContent = value;
        if(value < this.model.getNumRemainingSups()) {
            this.avCounter.classList.add("notenough");
            this.avSlash.classList.add("notenough");
            this.avInitNum.classList.add("notenough");
        }
    };
    
    Doctoral.View.prototype.updateAssignedSups = function(value)
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
        
        if(Scheduler.Assigner.active !== '1') {
            this.supsAssigned.classList.remove("completed", "overflowed");
        }
    };
    
    
    /* *************************************************************************
     * Doctoral: Info modal
     * ************************************************************************/
    
    Doctoral.showInfo = (function() {
        
        var modal        = document.getElementById("modal-doctoral-info")
        ,   name         = modal.getElementsByClassName("name")[0]
        ,   professors   = modal.getElementsByClassName("professors")[0]
        ,   remHours     = modal.getElementsByClassName("remHours")[0]
        ,   density      = modal.getElementsByClassName("density")[0]
        ,   densityDay   = modal.getElementsByClassName("densityDay")[0]
        ,   avCounter    = modal.getElementsByClassName("avCounter")[0]
        ,   supsAssigned = modal.getElementsByClassName("supsAssigned")[0]
        ,   supsObliged  = modal.getElementsByClassName("supsObliged")[0]
        ,   comment      = modal.getElementsByClassName("comment")[0]
        ,   email      = modal.getElementsByClassName("email")[0]
        ,   $modal       = $(modal)
        ;
        
        return function(doc) {
            name.textContent         = doc.name;
            professors.innerHTML     = doc.professors.join('<br>');
            remHours.textContent     = doc.getLiveNumRemainingHours();
            density.textContent      = doc.density;
            densityDay.textContent   = doc.densityDay;
            avCounter.textContent    = doc.getLiveNumAvailabilities();
            supsAssigned.textContent = doc.getNumAssignedSups();
            supsObliged.textContent  = doc.getNumRemainingSups();
            comment.textContent      = doc.comment;
            email.innerHTML        = '<a href="mailto:' + doc.email + '">' + doc.email + '</a>';

            $modal.modal("show");
        };
        
    })();
    
    
    /* *************************************************************************
     * namespace definition
     * ************************************************************************/
    
    window.Scheduler = window.Scheduler || {};
    window.Scheduler.Assigner = window.Scheduler.Assigner || {};
    window.Scheduler.Assigner.Doctoral = Doctoral;
    
});