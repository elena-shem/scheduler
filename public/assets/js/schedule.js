$(function() {
    
    /* *************************************************************************
     * Page setup (temporary modifications)
     * ************************************************************************/
    
    $("#page-title").remove();
    $("#push").remove();
    $("#footer").remove();
    $("#wrap > .container-fluid > div > hr").remove();
    $("#wrap > .container-fluid > hr").remove();
    
    $("#schedule").height($(document).height() - 75);
    
    $(window).resize(function() {
        $("#schedule").height($(document).height() - 75);
    });
    
    /* ********************************************************************** */
    
    // Move dummy course at the top of the list.
    var $ed1 = $('#courses-unassigned .dummy')[0];
    $ed1 && $('#courses-only-dummy').prepend($ed1.closest('tr'));
    var $ed2 = $('#courses-assigned .dummy')[0];
    $ed2 && $('#courses-only-dummy').prepend($ed2.closest('tr'));
    

    /* ********************************************************************** */
    
    var $coursesUnassigned = $('#courses-unassigned'),
        $coursesAssigned = $('#courses-assigned');
    
    var rd = REDIPS.drag;
    rd.init();
    //rd.scroll.enable = false;
    rd.dropMode = 'multiple';
    rd.hover.colorTd = '#d8ffd8';
    
    rd.event.cloned = function() {
        var $clone = $(REDIPS.drag.obj);
        $clone.append($('<div class="glyphicon glyphicon-remove remove"></div>')
            .mousedown(function(e) {
                e.stopPropagation();
            })
            .click(function(e) {
                deleteDragElement($clone[0]);
            })
        );
    };
    
    rd.event.clonedDropped = function() {
        var $clone = $(REDIPS.drag.objOld);
        
        // Never move dummy course to the "assigned courses" list.
        if(!$clone.hasClass('dummy')) {
            $clone.data('assignmentsnum', $clone.data('assignmentsnum') + 1);
            moveInSortedList($clone.closest('tr'), $coursesAssigned);
        }
    };
    
    var moveInSortedList = function($entry, $sortedContainer) {
        var title = $entry.text().trim(),
            $trPrev = null;
        
        // Find the highest entry with smaller text content.
        $sortedContainer.children().each(function() {
            var $child = $(this);
            if(title.localeCompare($child.text().trim()) <= 0) {
                return false;
            }
            $trPrev = $child;
        });
        
        if($trPrev) {
            $entry.insertAfter($trPrev);
        } else {
            $sortedContainer.prepend($entry);
        }
    };
    
    var deleteDragElement = function(element) {
        var $original = $('#' + element.id.match(/(c[0-9]*)[sc][0-9]*/)[1]),
            newValue = $original.data('assignmentsnum') - 1;
        
        rd.deleteObject(element);
        
        if(newValue >= 0) {
            $original.data('assignmentsnum', newValue);
            if(newValue === 0) {
                moveInSortedList($original.closest('tr'), $coursesUnassigned);
            }
        }
    };
    
    
    // Take server-side-assigned-courses into cosideration.
    $('#schedule-contents .drag').each(function() {
        var $course = $(this),
            $original = $('#' + this.id.match(/(c[0-9]*)[sc][0-9]*/)[1]);
        
        if(!$course.hasClass("dummy")) {
            $original.data('assignmentsnum', $original.data('assignmentsnum') + 1);
            moveInSortedList($original.closest('tr'), $coursesAssigned);
        }
        
        $course.find('.remove').mousedown(function(e) {
            e.stopPropagation();
        }).click(function() {
            deleteDragElement($course[0]);
        });
    });
    
    
    $('form').submit(function(event) {
        $('#form_schedule').val(saveContent());
    });
    
    /**
     * Scans table content and represents it in JSON format for submitting to the
     * server. This function is a modified version of `REDIPS.drag.saveContent`.
     * 
     * @return {String} Returns table content as in JSON format. Example:
     *  [[examday.id, examhour.id, course.id], ...]
     */
    var saveContent = function()
    {
        var tbl = document.getElementById('schedule-contents');
        var JSONobj = [];

        // tbl should be reference to the TABLE object
        if(tbl !== undefined && typeof(tbl) === 'object' && tbl.nodeName === 'TABLE') {
            // define number of table rows and columns
            var row_num = tbl.rows.length;
            var col_num = tbl.rows[0].cells.length;
            for(var r = 1; r < row_num; r++) { // iterate through each examday-row
                for(var c = 1; c < col_num; c++) { // iterate through each examhour-column
                    var cell = tbl.rows[r].cells[c];
                    // if cells isn't empty (no matter is it allowed or denied table cell) 
                    if(cell.childNodes.length > 0) {
                        // cell can contain many DIV elements
                        for(var d = 0; d < cell.childNodes.length; d++) {
                            // set reference to the child node
                            var cn = cell.childNodes[d];
                            // childNode should be DIV with containing "drag" class name
                            if(cn.nodeName === 'DIV' && cn.className.indexOf('drag') > -1) { // and yes, it should be uppercase
                                // For each id, remove the first letter {c,d,h}.
                                // For course ids, also remove the trailing identifier:
                                // - "s*" if added by server side.
                                // - "c*" if added by REDIPS.
                                var examday_id  = tbl.rows[r].id.substr(1);
                                var examhour_id = tbl.rows[0].cells[c].id.substr(1);
                                var course_id   = cn.id.substr(1).replace(/[sc][0-9]+/, '');
                                // push a sequence of [examday.id, examhour.id, course.id]
                                JSONobj.push([examday_id, examhour_id, course_id]);
                            }
                        }
                    }
                }
            }
        }
        // return prepared parameters (if tables are empty, returned value could be empty too) 
        return JSONobj.length > 0 ? JSON.stringify(JSONobj) : '';
    };

        // --- Select2 course search/filter ---
    if ($.fn.select2 && $('#courseSearch').length) {

        $('#courseSearch').select2({
            width: '100%',
            placeholder: 'Search course...',
            allowClear: true
        });

        $('#courseSearch').on('change', function () {
            var selectedId = $(this).val(); // "c123"

            var $rowsUnassigned = $('#courses-unassigned tr');
            var $rowsAssigned   = $('#courses-assigned tr');
            var $rowsDummy      = $('#courses-only-dummy tr');

            if (!selectedId) {
                $rowsUnassigned.show();
                $rowsAssigned.show();
                $rowsDummy.show();
                return;
            }

            var filterRows = function ($rows) {
                $rows.each(function () {
                    var has = $(this).find('#' + selectedId).length > 0;
                    $(this).toggle(has);
                });
            };

            filterRows($rowsUnassigned);
            filterRows($rowsAssigned);

            $rowsDummy.show();
        });
    }
});

