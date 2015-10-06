(function($) {

    $(document).ready(function() {

        $('.tg_bookatable_toggle .toggle-btn').click(function (e) {
            e.preventDefault();
            $(this).parent().find('.toggle-widget').css('max-height', '100%');
            if ($(this).hasClass('auto-hide')) {
            	$(this).hide();
            }
        });

    });

})(jQuery);