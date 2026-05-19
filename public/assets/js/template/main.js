/**
 * Created by spyros on 4/15/16.
 */

$( document ).ready( function() {

    // highlight active sidebar link based on current URL
    (function () {
        var path = window.location.pathname.toLowerCase();

        var best = null;
        var bestLen = 0;

        $('.admin-sidebar a.sidebar-link').each(function () {
            var href = ($(this).attr('href') || '').toLowerCase();
            if (!href) return;

            var a = document.createElement('a');
            a.href = href;

            var hrefPath = (a.pathname || href).toLowerCase();

            if (path.indexOf(hrefPath) === 0 && hrefPath.length > bestLen) {
                best = this;
                bestLen = hrefPath.length;
            }
        });

        if (best) {
            $(best).closest('li').addClass('active');
        }
    })();

    // restore state
    if (localStorage.getItem('sidebarCollapsed') === '1') {
        $('body').addClass('sidebar-collapsed');
    }

    $('.sidebar-toggle').on('click', function (e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-collapsed');

        localStorage.setItem(
            'sidebarCollapsed',
            $('body').hasClass('sidebar-collapsed') ? '1' : '0'
        );
    });


    var uriBase = (window.APP_BASE ? window.APP_BASE : (window.location.protocol + "//" + window.location.host));
    var uriJsonTime = uriBase + "/rest/time/list.json";


    $.ajax({url: uriJsonTime, success: function(serverTime){

        /**
         * Get local client time in seconds!
         */
        var localTime = +Date.now()/1000;

        /**
         * Calculate server client diff.
         */
        var timeDiff = serverTime - localTime;

        console.log('serverTime= ' + serverTime);
        console.log('localtime= ' + localTime);

        setInterval(function () {

            var timeWithDiff = (+Date.now() /1000 + timeDiff);

            /**
             * Set new date to epoch
             */
            var dateToFormat = new Date(0);

            /**
             * Set time to the calculated one
             */
            dateToFormat.setUTCSeconds(timeWithDiff);

            $("#serverTime").html(dateToFormat);

        }, 10000);
    }});

});
