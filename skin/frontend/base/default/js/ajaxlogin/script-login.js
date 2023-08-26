(function($) {
    $(document).ready(function() {
        var bodyLogin = $('body');
        $('#ajaxlogin-mask-enabled').show();
        if (!$('.hello-user').length) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'initial';
        }
        if ($(window).width() > 996) {
            $(bodyLogin).addClass('show-login-first');
        } else {
            if (!$('.hello-user').length) {
                $('.page-overlay').hide();
                $(bodyLogin).attr('data-dock','.ajax-login-modal');
                $(bodyLogin).addClass('dock-open dock-open--right wrap-dock--active');
                $('.mobile-trigger--profile').addClass('dock-trigger--active');
                $('.ajax-login-modal').addClass('dock--right dock--active');
            }
        }
    });
})(jQuery);