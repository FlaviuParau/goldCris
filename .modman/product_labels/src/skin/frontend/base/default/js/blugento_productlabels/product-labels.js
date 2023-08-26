(function($) {
    $(document).ready(function() {
        $('.products-grid .item, .products-list .item').each(function(){
            if ($(this).find('.category-label-top-left').length === 1 && $(this).find('.product-badges').length === 1) {
                var productImage = $(this).find('.product-badges').eq(0);

                $(this).find('.label-image').appendTo(productImage);
            } else {
                var productImage = $(this).find('.product-image').eq(0);

                $(this).find('.label-image').appendTo(productImage);
            }
        });
    });
})(jQuery);