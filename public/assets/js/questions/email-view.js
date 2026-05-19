/**
 * Created by spyros on 6/4/14.
 */
$( document ).ready( function() {

} );

/**
 * Search functionality
 *
 */



/**
 * Name
 */
$("#searchName").on("keyup", function() {
    var value = $(this).val().toLowerCase();

    $("#sortabletable tr").each(function(index) {
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


/**
 * Surname
 */
$("#searchSurname").on("keyup", function() {
    var value = $(this).val().toLowerCase();

    $("#sortabletable tr").each(function(index) {
        if (index !== 0) {

            $row = $(this);

            var id = $row.find("td:nth-child(3)").text().toLowerCase();

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
 * Email
 */
$("#searchEmail").on("keyup", function() {
    var value = $(this).val().toLowerCase();

    $("#sortabletable tr").each(function(index) {
        if (index !== 0) {

            $row = $(this);

            var id = $row.find("td:nth-child(4)").text().toLowerCase();

            if (id.indexOf(value) < 0) {
                $row.hide();
            }
            else {
                $row.show();
            }
        }
    });
});