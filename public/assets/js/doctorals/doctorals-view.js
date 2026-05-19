/**
 * Created by spyros on 12/28/15.
 */
$( document ).ready( function() {

    var uriBase = window.location.protocol + "//" + window.location.host + "/";

    $('tr').dblclick(function(){
        var id = $(this).attr('id');
        if(id !== undefined) {
            window.location.href = uriBase + 'admin/professors/view/' + id;
        }
    })
});
