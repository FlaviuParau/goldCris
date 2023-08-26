var Blugento = Blugento || {};

Blugento.Review = {};

(function($) {
    $.extend(Blugento.Review, {
        productList: function()
        {
            $('.jq-ratings').rating();
        }
    });
})(jQuery);

jQuery(document).ready(function($) {
    Blugento.Review.productList();

    $('.button-review').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#form-add-review').offset().top - $('#mini-search-wrapper-sticky').outerHeight()
        }, 700);
    });

    $('.stars .star').on('click', function() {
        var titleStar = $(this).attr('title');
        $('.stars-selected').addClass('active');
        $('.stars-count').text(titleStar);
    });
});
