(function($) {
    $(document).ready(function() {
        addToBoxOffset = $('.add-to-box').offset();

        $.fn.isOnScreen = function(){

            var win = $(window);
        
            var viewport = {
                top : win.scrollTop(),
                left : win.scrollLeft()
            };
            viewport.right = viewport.left + win.width();
            viewport.bottom = viewport.top + win.height();
        
            var bounds = this.offset();
            bounds.right = bounds.left + this.outerWidth();
            bounds.bottom = bounds.top + this.outerHeight();
        
            return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
        
        };

        $(window).scroll(function() {
            if ($('.grouped-box').isOnScreen() == true) {
                $('.blugento-grouped').addClass('add-to-box-fixed');
                $('.add-to-box-fixed .add-to-box').css('left', addToBoxOffset.left);
                $('.add-to-box-fixed .add-to-box').css('right', addToBoxOffset.left);
            } else {
                $('.add-to-box-fixed .add-to-box').removeAttr('style');
                $('.blugento-grouped').removeClass('add-to-box-fixed');
            }

            if ($('#tab-product-collateral').isOnScreen() == true) {
                $('.add-to-box-fixed .add-to-box').removeAttr('style');
                $('.blugento-grouped').removeClass('add-to-box-fixed');
            }
        });

        // Add custom html for variations selection
        $('<div class="variation-selections"><span class="count-variations"></span><span class="label-variations"></span></div>').prependTo('.product-view .product-shop .add-to-box');

        /**
         *  Animate content
         */
        $('.btn-variations').on('click', function() {
            setTimeout(function(){
                $('html, body').animate({
                    scrollTop: $('.grouped-box').offset().top - 50
                }, 200);
            }, 400);
        });

        /**
         * Add qty button functionality
         */
        $('body').on('click', '.button-counter-qty span', function() {
            const dataIncrement = $('.product-view .product-shop .add-to-cart input').attr('data-increment') ||
              $(this).parent().parent().find('.qty-input').data('increment');
              const increment = parseFloat(/(,)/g.test(dataIncrement) ? dataIncrement.replace(/,/g, '') : dataIncrement);
              var button = $(this),
                input = button.parent().parent().find('input'),
                oldValue = parseFloat(input.val().replace(/,/g, '')),
                newValue = parseFloat(oldValue);

            if (button.hasClass('plus')) {
                newValue += increment;
                input.val(newValue);
            } else {
                if (oldValue > 0) {
                    newValue -= increment;
                    input.val(newValue);
                } else {
                    newValue = 0;
                }
            }

            if(newValue > 0) {
                $(this).parents('.item').addClass('selected');
            } else {
                $(this).parents('.item').removeClass('selected');
            }

            countVariations();
        });

        /**
         * Count variations selected
         */
        $('.add-to-box .btn-cart').prop('disabled', true);
        function countVariations() {
            const countSelection = $('.blugento-grouped-items .selected').length;
            $('.count-variations').html(countSelection );
            $('.label-variations').html('');

            if (countSelection === 0) {
                $('.count-variations').html('');
                $('.add-to-box .btn-cart').prop('disabled', true);
            } else if (countSelection > 0 && countSelection < 2) {
                $('.add-to-box .btn-cart').prop('disabled', false);
                $('.label-variations').append(Translator.translate('selection'))
            } else if (countSelection > 1) {
                $('.add-to-box .btn-cart').prop('disabled', false);
                $('.label-variations').append(Translator.translate('selections'))
            }
        }

        /**
         * Check if input has quantity
         */
        $('.blugento-grouped-items .qty input').on('input', function() {
            const qtyVal = $(this).val();

            if(qtyVal > 0) {
                $(this).parents('.item').addClass('selected');
            } else {
                $(this).parents('.item').removeClass('selected');
            }

            countVariations();
        });

        /**
         * Count grouped products
         */
        const countProducts = $('.blugento-grouped-items ul li').length;
         $('.variations').append('(' + countProducts + ")");
    });
})(jQuery);
