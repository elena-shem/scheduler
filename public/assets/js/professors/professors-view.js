/**
 * Created by spyros on 12/28/15.
 */
$( document ).ready( function() {

    var uriBase = window.location.protocol + "//" + window.location.host + "/";

    $('.doctoral-row').dblclick(function(){
        var id = $(this).attr("data-doctoralId");
        if(id !== undefined) {
            window.location.href = uriBase + 'admin/doctorals/view/' + id;
        }
    });

    $('.course-row').dblclick(function(){
        var id = $(this).attr('data-courseId');
        if(id !== undefined) {
            window.location.href = uriBase + 'admin/courses/view/' + id;
        }
    });
});