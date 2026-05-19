$("#form_examperiod").change(function() {

    var exid = $("#form_examperiod").val();

    var baseUrl = [
        location.protocol,
        '//',
        location.host,
        '/admin/availabilitycalendar/index'
    ].join('');

    var url = exid ? baseUrl + '/' + exid : baseUrl;

    window.location.href = url;
});
