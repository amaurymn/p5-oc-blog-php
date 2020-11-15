(function() {
    'use strict';
    window.setTimeout(function () {
        $('.autoclose').fadeTo(1000, 0).slideUp(1000, function () {
            $(this).remove();
        });
    }, 10000);
})();
