(function($) {
    $(document).ready(function() {
        $('.ajaxcart-modal-wishlist .button').each(function () {
            $(this).click(function (e) {
                $('.ajaxcart-modal-wishlist').removeClass('ajaxcart-open-wishlist');
            });
        });

        $('.link-compare').each(function () {
            $(this).click(function (e) {
                e.preventDefault();

                if(!$(this).hasClass('active-compare')) {
                    var url = $(this).attr('href');

                    url = url.replace("catalog/product_compare/add", "ajaxwishlist/index/compare");
                    url += 'isAjax/1/'

                    // Check compared products length
                    var compareProducts = $('#compare-items li:not(.placeholder)').length;
                    if (compareProducts != 4) {
                        $('.info-message').remove();
                        $('.item-inner').removeClass('ajax-loading-compare');
                        if ($('#product_addtocart_form').length) {
                            $(this).addClass('ajax-loading-compare');
                        } else {
                            $(this).parents('.item-inner').addClass('ajax-loading-compare');
                        }
                        
                        $.ajax({
                            url: url,
                            dataType: 'json',
                            success: function (data) {
                                if (data.status == 'ERROR') {
                                    $('<div class="info-message">' + data.message + '</div>').appendTo('.ajax-loading-compare .product-image');
                                    setTimeout(function(){
                                        $('.info-message').addClass('visible');
                                    }, 100);
                                } else {
                                    // Show response message next to clicked link
                                    if ($('#product_addtocart_form').length) {
                                        // Add active class for added product in compare list
                                        $('.ajax-loading-compare').addClass('active-compare');
                                        $('.ajax-loading-compare.active-compare span').html(Translator.translate('Added'));
                                        $('<div class="info-message">' + data.message + '</div>').appendTo('.ajax-loading-compare');
                                    } else {
                                        // Add active class for added product in compare list
                                        $('.ajax-loading-compare .link-compare').addClass('active-compare');
                                        $('.ajax-loading-compare .active-compare span').html(Translator.translate('Added'));
                                        $('<div class="info-message">' + data.message + '</div>').appendTo('.ajax-loading-compare .product-image');
                                        setTimeout(function(){
                                            $('.info-message').addClass('visible');
                                        }, 100);
                                    }
                                    $('.link-compare').removeClass('ajax-loading-compare');
    
                                    // Update dropdown wishlist with correct html
                                    if ($('.page-product-compare').length) {
                                        $('.block-compare').replaceWith(data.sidebar);
                                        $('.page-product-compare').addClass('reveal--active');
                                        $('.reveal-trigger').addClass('reveal--active');
                                    } else {
                                        $('.page-main').append('<div class="page-product-compare reveal" data-reveal="bottom"><a href="javascript:void(0);" class="reveal-trigger"></a>' + data.sidebar + '</div>');
                                        $('.page-product-compare').addClass('reveal--active');
                                        $('.reveal-trigger').addClass('reveal--active');
                                        $('.reveal-trigger').on('click', function (e) {
                                            $(this).parent().toggleClass('reveal--active');
                                            return false;
                                        });
                                    }
                                }
    
                                // Remove info message once it's shown
                                setTimeout(function(){
                                    $('.info-message').remove();
                                }, 3100);
                            }
                        });
                    } else {
                        $('.page-product-compare').addClass('reveal--active');
                        $('.reveal-trigger').addClass('reveal--active');
                        $('.block-compare .validation-advice').remove();
                        $('<div class="validation-advice">' + Translator.translate('Unfortunately you are not allowed to add more than 4 products on the comparison list.') + '</div>').insertAfter('.block-compare .actions');
                    }
                }
            });
        });

        $('.link-wishlist:not(.link-wishlist-product)').each(function () {
            $(this).click(function (e) {
                e.preventDefault();

                if(!$(this).hasClass('active-wishlist')) {
                    var url = $(this).attr('href');

                    url = url.replace("wishlist/index","ajaxwishlist/index");
                    url += 'isAjax/1/';

                    if(!$(this).parents('.customer-logged-in').length) {
                        $(".ajaxcart-overlay").show();
                        $(".ajaxcart-modal-wishlist h4").text();
                    }

                    $('.info-message').remove();
                    $('.link-wishlist').removeClass('ajax-loading-wishlist');
                    $(this).parents('.item-inner').addClass('ajax-loading-wishlist');

                    $.ajax( {
                        url : url,
                        dataType : 'json',
                        success : function(data) {
                            if (data.status == 'ERROR'){
                                $(".ajaxcart-modal-wishlist").addClass("ajaxcart-open-wishlist");
                                $(".ajaxcart-modal-wishlist h4").text('');
                                $(".ajaxcart-modal-wishlist h4").text(data.message);
                            } else{
                                // Add active class for added product in wishlist
                                $('.ajax-loading-wishlist .link-wishlist').addClass('active-wishlist');
                                $('.ajax-loading-wishlist .active-wishlist span').html(Translator.translate('Added'));

                                // Show response message next to image product
                                $('<div class="info-message">' + data.message + '</div>').appendTo('.ajax-loading-wishlist .product-image');
                                setTimeout(function(){
                                    $('.info-message').addClass('visible');
                                }, 100);
                                $('.item-inner').removeClass('ajax-loading-wishlist');

                                // Update dropdown wishlist with correct html
                                if ($('.header-wishlist-count .block-wishlist').length){
                                    $('.header-wishlist-count .block-wishlist').replaceWith(data.sidebar);
                                    var wishlistCount = $('.header-wishlist-count .block-title small').html(),
                                        wishlistCountReg = wishlistCount.replace(/[{()}]/g, '');
                                    $('.count-wish span').text(wishlistCountReg);
                                } else{
                                    if ($('.header-wishlist-count .block-title small').length) {
                                        $('.header-wishlist-count').prepend(data.sidebar);
                                        var wishlistCount = $('.header-wishlist-count .block-title small').html(),
                                            wishlistCountReg = wishlistCount.replace(/[{()}]/g, '');
                                        $('.count-wish span').text(wishlistCountReg);
                                    }
                                }

                                // Remove info message once it's shown
                                setTimeout(function(){
                                    $('.info-message').remove();
                                }, 3100);
                            }

                            $(".ajaxcart-overlay").hide();
                        }
                    });
                }
            });
        });
    });
})(jQuery);

function hideModalWishlist(){
    jQuery(".ajaxcart-modal-wishlist").removeClass("ajaxcart-open-wishlist");
}