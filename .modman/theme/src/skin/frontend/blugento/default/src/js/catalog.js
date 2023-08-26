var ProductMediaManager = {
    swapImage: function(targetImage)
    {
        var path = targetImage.attr('src');
        
        if (typeof path !== 'undefined') {
            setTimeout(function () {
                if (jQuery('#product-image .slick-current a source').length) {
                    jQuery('#product-image .slick-current a img, #product-image .slick-current a source').attr('data-zoom-image', path + '.webp')
                        .attr('data-image', path + '.webp')
                        .attr('src', path + '.webp')
                        .attr('srcset', path + '.webp' + ' 1x, ' + path + '.webp' + ' 2x');
                    jQuery('#product-image .slick-current a').attr('data-mfp-src', path + '.webp');
                } else {
                    jQuery('#product-image .slick-current a img').attr('data-zoom-image', path)
                    .attr('data-image', path)
                    .attr('src', path)
                    .attr('srcset', path + ' 1x, ' + path + ' 2x');
                  jQuery('#product-image .slick-current a').attr('data-mfp-src', path);
                }
            }, 100);
        }
    },

    init: function()
    {
        jQuery(document).trigger('product-media-loaded', ProductMediaManager);
    }
};

// ----------------------------------------------------------------------------

var Blugento = Blugento || {};

Blugento.Catalog = {};

(function($) {
    $.extend(Blugento.Catalog, {
        manufacturer: '',
        availability: Infinity,

        productView: function()
        {
            try {
                if (!Blugento.isMobileDesign()) {} else {
                    var visibleY = function (el) {
                        var rect = el.getBoundingClientRect(), top = rect.top, height = rect.height,
                          el = el.parentNode;
                        do {
                            rect = el.getBoundingClientRect();
                            if (top <= rect.bottom === false) return false;
                            // Check if the element is out of view due to a container scrolling
                            if ((top + height) <= rect.top) return false;
                            el = el.parentNode;
                        } while (el != document.body);
                        // Check its within the document viewport
                        return top <= document.documentElement.clientHeight && top >= 0;
                    };

                    function attachEvent(element, event, callbackFunction) {
                        if (element.addEventListener) {
                            element.addEventListener(event, callbackFunction, false);
                        } else if (element.attachEvent) {
                            element.attachEvent('on' + event, callbackFunction);
                        }
                    };

                    var update = function () {
                        if ($('body').hasClass('catalog-product-view')) {
                            visibleY(document.getElementById('product-addtocart-button')) ? $('#product-addtocart-button-fixed').removeClass('btn-cart-fixed') : $('#product-addtocart-button-fixed').addClass('btn-cart-fixed');
                        }
                    };

                    attachEvent(window, "scroll", update);
                    attachEvent(window, "resize", update);
                    update();

                    $('#product-addtocart-button-fixed').on('click', function () {
                        $('#product-addtocart-button').trigger('click');
                        if($('#product-options-wrapper').length) {
                            $('html, body').animate({
                                scrollTop: $("#product-options-wrapper").offset().top - 50
                            }, 1000);
                        }
                    });
                }
            }
            catch(e) {}
            
            try {
                var tabsContainerId = 'tab-product-collateral',
                  tabsContainer = $('#' + tabsContainerId),
                  tabsContainerNav = $('#' + tabsContainerId + '-nav'),
                  inlineTabsNav = $('.tab-nav', tabsContainer),
                  active = tabsContainer.data('active'),
                  activeIndex = tabsContainerNav.children('.tab-' + active).index(),
                  tabsStyle = tabsContainer.attr('data-style');
                
                var count = 0;
                tabsContainerNav.children().each(function () {
                    count++;
                });
                
                $('#tab-product-collateral-nav li').each(function(){
                    var order = $(this).data('order');
                    $(this).css('order', order);
                });

                if (Blugento.isMobileDesign() || (tabsContainer.data('style') == 3 && tabsContainer.data('orientation') == 1)) {
                    $('#tab-product-collateral .tabs-container span').each(function() {
                        var order = $(this).data('order');
                        $(this).css('order', order);
                    });
                }
                
                if (count >= 4 && tabsContainer.data('style') == 1 && tabsContainer.data('orientation') == 1) {
                    tabsContainer.addClass('exceeded-tabs-limit')
                }
                
                if (activeIndex < 0) {
                    activeIndex = 0;
                }
                
                if (tabsStyle != '3') {
                    var yetti = new Yetii({
                        id: tabsContainerId,
                        active: activeIndex + 1
                    });
                }
                
                inlineTabsNav.eq(activeIndex).addClass('active');
                
                inlineTabsNav.on('click', function(e) {
                    var rel = $(this).data('rel');
                    
                    $('a[href="' + rel + '"]', tabsContainer).trigger('click');
                    
                    $(this).parent().siblings().find('a.tab-nav').removeClass('active');
                    $(this).addClass('active');
    
                    if (Blugento.isMobileDesign()) {
                        var stickyHeaderHeight = 0;
                        
                        if ($('.page-header').data('sticky') === 1) {
                            stickyHeaderHeight = $('#header-sticky-content').height();
                        }
                        
                        $('html, body').animate({
                            scrollTop: $(this).offset().top - (stickyHeaderHeight || 5)
                        }, 300);
                    }
                    
                    return false;
                });
            }
            catch(e) {}
            
            try {
                $('#top-reviews').on('click', function() {
                    var target;
                    
                    if ($('#tab-product-collateral-nav').is(':visible')) {
                        target = $('a[href="#pc-tab-reviews"]');
                    } else {
                        target = $('a[data-rel="#pc-tab-reviews"]');
                    }
                    
                    if (target.length === 0) {
                        target = $('.yotpo-main-widget').eq(0);
                    }
                    
                    if (target.length === 0) {
                        return;
                    }
                    
                    target.trigger('click');
                    
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 400);
                });
            }
            catch(e) {}
            
            try {
                var productImageAction = $('#product-image').data('action');
                
                if (!Blugento.isMobileDesign()) {
                    switch (productImageAction) {
                        case 9:
                            $('.media-swipe').lightGallery({
                                thumbnail: true
                            });
                            break;
                        case 8:
                            $('.product-img-box').magnificPopup({
                                delegate: '.product-gallery[data-mfp-src]',
                                type: 'image',
                                tLoading: 'Loading image #%curr%...',
                                mainClass: 'mfp-img-mobile',
                                gallery: {
                                    enabled: true,
                                    navigateByImgClick: true,
                                    preload: [0, 1]
                                },
                                callbacks: {
                                    open: function() {
                                        var result = '',
                                          gallery = $('#product-img-box-cloned').length ? $('#product-img-box-cloned') : $('#product-img-box'),
                                          productGallery = gallery.find('a.product-gallery:not(.item-image)');
                                        
                                        if (productGallery.length > 0) {
                                            result = '<div class="mfp-gallery"><ul id="mfp-slider">';
                                            for (var i = 0; i < productGallery.length; i++) {
                                                var thumb = productGallery.eq(i).find('img').attr('src');
                                                result += '<li><button type="button" onclick="javascript: jQuery(\'.product-img-box\').magnificPopup(\'goTo\', ' + i + '); return false;"><img src="' + thumb + '" alt=""></button></li>';
                                            }
                                            result += '</ul></div>';
                                        }
                                        $('.mfp-bottom-bar').append(result);
                                    },
                                    imageLoadComplete: function() {
                                        if (!$('#mfp-slider').hasClass('slick-initialized')) {
                                            $("#mfp-slider").slick({
                                                infinite: false,
                                                slidesToShow: 4,
                                                slidesToScroll: 1
                                            });
                                        }
                                    }
                                }
                            });
                            break;
                        case 7:
                            $('.media-swipe').on('init', function() {
                                var currentSlide = $(this).find('li.item.slick-current');
                                var currentImg = currentSlide.find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(currentImg).elevateZoom({
                                    zoomType: 'lens',
                                    lensSize: 300,
                                    scrollZoom: true
                                });
                            });
                            $('.media-swipe').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                                var img = $(slick.$slides[nextSlide]).find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(img).elevateZoom({
                                    zoomType: 'lens',
                                    lensSize: 300,
                                    scrollZoom: true
                                });

                                // Remove id and add it for selected slide
                                $('#product-image-img').attr('id','');
                                $(img).attr('id','product-image-img');
                            });
                            break;
                        case 6:
                            $('.media-swipe').on('init', function() {
                                var currentSlide = $(this).find('li.item.slick-current');
                                var currentImg = currentSlide.find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(currentImg).click(function (e) {
                                    e.preventDefault();
                                });
                                $(currentImg).elevateZoom();
                            });
                            $('.media-swipe').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                                var img = $(slick.$slides[nextSlide]).find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(img).click(function (e) {
                                    e.preventDefault();
                                });
                                $(img).elevateZoom();

                                // Remove id and add it for selected slide
                                $('#product-image-img').attr('id','');
                                $(img).attr('id','product-image-img');
                            });
                            break;
                        case 5:
                            $('.media-swipe').on('init', function() {
                                var currentSlide = $(this).find('li.item.slick-current');
                                var currentImg = currentSlide.find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(currentImg).click(function (e) {
                                    e.preventDefault();
                                });
                                $(currentImg).elevateZoom({
                                    zoomType: 'inner',
                                    cursor: 'crosshair'
                                });
                            });
                            $('.media-swipe').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                                var img = $(slick.$slides[nextSlide]).find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(img).click(function (e) {
                                    e.preventDefault();
                                });
                                $(img).elevateZoom({
                                    zoomType: 'inner',
                                    cursor: 'crosshair'
                                });

                                // Remove id and add it for selected slide
                                $('#product-image-img').attr('id','');
                                $(img).attr('id','product-image-img');
                            });
                            break;
                        case 4:
                            $('.media-swipe').on('init', function() {
                                var currentSlide = $(this).find('li.item.slick-current');
                                var currentImg = currentSlide.find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(currentImg).elevateZoom({
                                    zoomType: 'lens',
                                    lensShape: 'round',
                                    lensSize: 200,
                                    scrollZoom: true
                                });
                            });
                            $('.media-swipe').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                                var img = $(slick.$slides[nextSlide]).find('img');
                                $('.zoomWindowContainer, .zoomContainer').remove();
                                $(img).elevateZoom({
                                    zoomType: 'lens',
                                    lensShape: 'round',
                                    lensSize: 200,
                                    scrollZoom: true
                                });

                                // Remove id and add it for selected slide
                                $('#product-image-img').attr('id','');
                                $(img).attr('id','product-image-img');
                            });
                            break;
                        case 3:
                            $('#product-image').magnificPopup({
                                delegate: '.product-gallery[data-mfp-src]',
                                type: 'image',
                                tLoading: 'Loading image #%curr%...',
                                mainClass: 'mfp-img-mobile',
                                gallery: {
                                    enabled: true,
                                    navigateByImgClick: true,
                                    preload: [0, 1]
                                }
                            });
                            break;
                        case 2:
                            $('.media-swipe').on('init', function() {
                                var currentSlide = $(this).find('li.item.slick-current');
                                var currentImg = currentSlide.find('img');
                                var currentLink = currentSlide.find('a');
                                $('#product-image').on('click', currentLink, function() {
                                    popWin($(currentImg).attr('data-zoom-image'), 'gallery', 'left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes');
                                    return false;
                                });
                            });
                            $('.media-swipe').on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                                var img = $(slick.$slides[nextSlide]).find('img');
                                var link = $(slick.$slides[nextSlide]).find('a');
                                $('#product-image').on('click', link, function() {
                                    popWin($(img).attr('data-zoom-image'), 'gallery', 'left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes');
                                    return false;
                                });

                                // Remove id and add it for selected slide
                                $('#product-image-img').attr('id','');
                                $(img).attr('id','product-image-img');
                            });
                            break;
                        case 1:
                            $('.media-swipe').on('init', function() {
                                var currentSlide = $(this).find('li.item.slick-current');
                                var currentLink  = currentSlide.find('a');
                                $('#product-image').on('click', currentLink, function(e) {
                                    e.preventDefault();
                                });
                            });
                            break;
                        default:
                            $('.media-swipe').lightGallery({
                                thumbnail: true
                            });
                            break;
                    }
                } else {
                    var productImageActionMobile = $('#product-image').data('action-mobile');
                    switch (productImageActionMobile) {
                        case 1:
                            $('.media-swipe').lightGallery({
                                thumbnail: true
                            });
                            break;
                    }
                }
            }
            catch(e) {}
            
            try {
                var gallery = $('#media-carousel');
                var productImageSwipe = $('#media-swipe');

                productImageSwipe.on('init', function() {
                    productImageSwipe.css('opacity', '1');
                    gallery.show();
                    $('.product-img-box .discount-percentage').show();
                    $('.product-img-box .product-badges').show();
                    $('.product-img-box .ajax-loader').hide();
                });

                var gallery = $('.media-carousel');
                var productImageSwipe = $('.media-swipe');

                productImageSwipe.css('opacity', '1');
                gallery.show();
                $('.product-img-box .product-badges').show();
                $('.product-img-box .ajax-loader').hide();

                productImageSwipe.slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    fade: true,
                    asNavFor: gallery,
                    autoplay: false
                });
                gallery.slick({
                    slidesToShow: gallery.data('gallery-images-count'),
                    slidesToScroll: 1,
                    asNavFor: productImageSwipe,
                    dots: false,
                    focusOnSelect: true,
                    infinite: true,
                    vertical: gallery.data('vertical'),
                    responsive: [
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: gallery.data('gallery-images-count-mobile')
                            }
                        },
                        {
                            breakpoint: 767,
                            settings: {
                                dots: $('#product-img-box-cloned').length ? false : true,
                                appendDots: productImageSwipe
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                vertical: gallery.data('mobile-vertical'),
                                dots: $('#product-img-box-cloned').length ? false : true,
                                appendDots: productImageSwipe
                            }
                        }
                    ]
                });

                $(productImageSwipe).on('beforeChange', function(event, slick, currentSlide, nextSlide) {
                    var img = $(slick.$slides[nextSlide]).find('img');

                    // Remove id and add it for selected slide
                    $('#product-image-img').attr('id','');
                    $(img).attr('id','product-image-img');
                });

                if (!$('#product-img-box-cloned').length) {
                    var selectedImage = $('#media-swipe li.selected-image');
                    var index = selectedImage.attr('data-slick-index');
                    productImageSwipe.slick('slickGoTo', parseInt(index));

                    // Workaround for task BCBS-3720
                    gallery.slick('slickGoTo', parseInt(index));

                    if ($('#media-carousel li') && $('#media-carousel li').length >= 2) {
                        $('#media-carousel li').each(function(_, e) {
                            $(e).removeClass('slick-current').removeClass('slick-active');
                        });

                        $('#media-carousel li[data-slick-index="' + parseInt(index) + '"]').addClass('slick-current').addClass('slick-active');
                    }
                }
            }
            catch(e) {
                console.log(e);
            }
            
            try {
                var $images = [];
                
                $('#media-carousel .item-image img').each(function() {
                    if ($(this).attr('alt') !== '') {
                        $images.push($(this).attr('alt'));
                    }
                });
                
                $('.configurable-swatch-list li a').on('click', function(e) {
                    var $title = $(this).attr('title');
                    
                    $.each($images, function(index, value) {
                        if (value === $title) {
                            var a = $('#media-carousel .item-image img[alt="' + value + '"]').parent()[0],
                              dataImages = $(a).data('images');
                            
                            if (typeof dataImages === 'object') {
                                a.click();
                            }
                        }
                    });
                });
            }
            catch(e) {}

            try {
                $('.shipping-cost-details a').magnificPopup({
                    type: 'inline'
                });
            }
            catch(e) {}
            
            try {
                var related = $('#block-related [data-slider-related]').not(':hidden');

                related.slick({
                    infinite: $('[data-slider-related]').data('slider-item-loop') == 1 ? true : false,
                    speed: parseInt($('[data-slider-related]').data('slider-animation')) || 300,
                    slidesToShow: parseInt($('[data-slider-related]').data('slider-item-row')) || 4,
                    slidesToScroll: parseInt($('[data-slider-related]').data('slider-item-scroll')) || 1,
                    dots: $('[data-slider-related]').data('dots') == 1 ? true : false,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                infinite: $('[data-slider-related]').data('slider-item-loop') == 1 ? true : false,
                                centerMode: $('[data-slider-related]').data('center') == 1 ? true : false,
                                centerPadding: $('[data-slider-related]').data('center') == 1 ? '50px' : '0'
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2,
                                infinite: $('[data-slider-related]').data('slider-item-loop') == 1 ? true : false,
                                centerMode: $(this).data('center') == 1 ? true : false,
                                centerPadding: $(this).data('center') == 1 ? '40px' : '0'
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: parseInt($('#block-related [data-slider-related]').data('mobile-items')) || 1,
                                slidesToScroll: 1,
                                infinite: $('[data-slider-related]').data('slider-item-loop') == 1 ? true : false,
                                centerMode: $('[data-slider-related]').data('center') == 1 ? true : false,
                                centerPadding: $('[data-slider-related]').data('center') == 1 ? '30px' : '0'
                            }
                        }
                    ]
                });

                $(window).load(function () {
                    related.each(function (e, i) {
                        var track = $(this).find('.slick-track');
                        track.find('.item-inner').css('min-height', track.height() + 'px');
                    });
                });
            }
            catch(e) {}
            
            try {
                var upsell = $('#block-upsell [data-slider-upsell]').not(':hidden');
                
                upsell.slick({
                    infinite: $('[data-slider-upsell]').data('slider-item-loop') == 1 ? true : false,
                    speed: parseInt($('[data-slider-upsell]').data('slider-animation')) || 300,
                    slidesToShow: parseInt($('[data-slider-upsell]').data('slider-item-row')) || 4,
                    slidesToScroll: parseInt($('[data-slider-upsell]').data('slider-item-scroll')) || 1,
                    dots: $('[data-slider-upsell]').data('dots') == 1 ? true : false,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                infinite: $('[data-slider-upsell]').data('slider-item-loop') == 1 ? true : false,
                                centerMode: $('[data-slider-upsell]').data('center') == 1 ? true : false,
                                centerPadding: $('[data-slider-upsell]').data('center') == 1 ? '50px' : '0'
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1,
                                infinite: $('[data-slider-upsell]').data('slider-item-loop') == 1 ? true : false,
                                centerMode: $('[data-slider-upsell]').data('center') == 1 ? true : false,
                                centerPadding: $('[data-slider-upsell]').data('center') == 1 ? '40px' : '0'
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: parseInt($('#block-upsell [data-slider-upsell]').data('mobile-items')) || 1,
                                slidesToScroll: 1,
                                infinite: $('[data-slider-upsell]').data('slider-item-loop') == 1 ? true : false,
                                centerMode: $('[data-slider-upsell]').data('center') == 1 ? true : false,
                                centerPadding: $('[data-slider-upsell]').data('center') == 1 ? '30px' : '0',
                            }
                        },
                        {
                            breakpoint: 320,
                            settings: {
                                slidesToShow: parseInt(parseInt($('#block-upsell [data-slider-upsell]').data('mobile-items'))) || 1,
                                slidesToScroll: 1,
                                infinite: $('[data-slider-upsell]').data('slider-item-loop') == 1 ? true : false,
                                centerMode: $('[data-slider-upsell]').data('center') == 1 ? true : false,
                                centerPadding: $('[data-slider-upsell]').data('center') == 1 ? '20px' : '0',
                            }
                        }
                    ]
                });

                $(window).load(function() {
                    upsell.each(function(e, i) {
                        var track = $(this).find('.slick-track');
                        track.find('.item-inner').css('min-height', track.height() + 'px');
                    });
                });
            }
            catch(e) {}

            try {
                var crosssell = $('#block-crosssale [data-slider]').not(':hidden'),
                  crosssellCount = crosssell.find('li.item').length;
                
                if (crosssellCount > 4) {
                    crosssell.slick({
                        dots: false,
                        infinite: true,
                        speed: 300,
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        responsive: [
                            {
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 1,
                                    infinite: true,
                                    dots: false
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1,
                                    infinite: true,
                                    dots: false
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    infinite: true,
                                    dots: false
                                }
                            },
                            {
                                breakpoint: 320,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1,
                                    infinite: true,
                                    dots: false
                                }
                            }
                        ]
                    });
                    
                    $(window).load(function() {
                        crosssell.each(function(e, i) {
                            var track = $(this).find('.slick-track');
                            track.find('.item-inner').css('min-height', track.height() + 'px');
                        });
                    });
                }
            }
            catch(e) {}

            try {
                $('.baseprice-box').appendTo('#product-baseprice');
            }
            catch(e) {}
        },

        setProductManufacturer: function(m) {
            this.manufacturer = m;
        },

        getProductManufacturer: function() {
            return this.manufacturer;
        },

        setProductAvailability: function(a) {
            this.availability = a;
        },

        getProductAvailability: function() {
            return this.availability;
        },

        customRender: function() {
            if (this.getProductManufacturer().length && isFinite(this.getProductAvailability())) {
                $('.price-box, .tax-details, #product-options-wrapper, .add-to-cart, .availability').remove();
                $('<div class="custom-availability-message">' + Translator.translate('This product is no longer available') + '</div>').insertAfter('.product-sku');
            }
        },

        productList: function()
        {
    
            try {
                var catBanner = $('#category-banner-group');
                if (catBanner) {
                    catBanner.insertBefore('.page-main .page-container');
                }

                // Set class if filters have more than 6 options visible
                if ($('.minimize-filters').length) {
                    $('.minimize-filters').each(function (e) {
                        var filtersLength = $(this).find('li').length;

                        if (filtersLength > 6) {
                            $(this).addClass('active-minimize-filters');
                        }
                    });
                }
            } catch(e) {
                console.log(e);
            }
            
            Blugento.flex('.product-shop-row', 'td', '.product-info');
            Blugento.flex('.products-grid', '> li', '.short-info');
            Blugento.flex('.products-grid', '> li', '.product-info');
            Blugento.flex('.products-grid', '> li', '.yotpo');
            Blugento.flex('.products-grid', '> li', '.desc');
            Blugento.flex('.products-grid', '> li', '.configurable-swatch-list');
            Blugento.flex('.products-grid', '> li', '.qty-wrapper');
            Blugento.flex('.products-grid', '> li', '.content-blog-box');
            Blugento.flex('.slick-track', '> .slick-slide', '.short-info');
            Blugento.flex('.slick-track', '> .slick-slide', '.product-info');
            Blugento.flex('.slick-track', '> .slick-slide', '.configurable-swatch-list');
            Blugento.flex('.slick-track', '> .slick-slide', '.qty-wrapper');
            Blugento.flex('.slick-track', '> .slick-slide', '.desc');
            Blugento.flex('.slick-track', '> .slick-slide', '.yotpo');
            Blugento.flex('.slick-track', '> .slick-slide', '.content-blog-box');
            Blugento.flex('.blog-wrap', '.postWrapper');
        }
    });

    $(document).ready(function() {
        ProductMediaManager.init();
        Blugento.Catalog.productView();
        Blugento.Catalog.productList();
        Blugento.Catalog.customRender();
    });

    $(document).ajaxSuccess(function() {
        Blugento.Catalog.productList();
    });
    
    $(window).on('blugento.window.resize', function() {
        Blugento.Catalog.productList();
    });
    
    $(window).load(function() {
        Blugento.Catalog.productList();
    });
})(jQuery);