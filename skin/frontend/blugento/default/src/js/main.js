var Blugento = Blugento || {};

(function($) {

    $.extend(Blugento, {

        e: 'click',
        md: null,
        resizeId: false,
        sticky: false,
        stickyNav: true,

        init: function()
        {
            var _this = this;

            $(document).ready(function() {
                _this.initToTop();
                _this.initDevice();
                _this.initSlick();
                _this.initMagnificPopup();
                _this.initDocks();
                _this.initModalCart();
                _this.initNav();
                _this.initNavPushDown();
                _this.initNavSubmenu();
                _this.initAnchors();
                _this.initTables();
                _this.initSearch();
                _this.initBlocks();
                _this.initReveal();
                _this.initHover();
                _this.initLazyload();
                _this.initInputText();
                _this.initBodyClass();
                _this.initCalcTabs();
                _this.initCalcTabsMobile();
                _this.initShowArticles();
                _this.initShowSearch();
                _this.initMenuFooter();
                _this.initAcountHover();
                _this.initInvitationClick();
                _this.initMenuHover();
                _this.initSortBy();
                _this.initCart();
                _this.initAcountMobile();
                _this.iniStickytMenuMobile();
                _this.initEmailSpace();
                _this.initCartButtonSticky();
                _this.getViewportData();
                _this.initNavNewLayout();
                _this.initResponsiveIframe();
                _this.initTopFilters();
                _this.initMenuDropdown();
                _this.initSwatchSlider();
                _this.initMenuScroll();
                _this.initTierPriceQty();
                _this.initCategoryTruncate();
                _this.initDescriptionTruncate();
                _this.initGDPRCheckbox();
            });

            $(window).on('blugento.window.resize', function() {
                _this.initBodyClass();
                _this.getViewportData();
            });

            $(window).load(function() {
                _this.initPreloader();
                _this.initStickyHeader();
            });
        },

        flex: function(wrap, elements, target)
        {
            var d = document.body || document.documentElement,
                s = d.style;

            $(wrap).each(function() {
                var $elements = $(this).find(elements),
                    perRow = Math.floor($(this).width() / $elements.width());

                if (perRow == null || perRow < 2) {
                    return true;
                }

                for (var i = 0, j = $elements.length; i < j; i += perRow) {
                    var $row = $elements.slice(i, i + perRow);

                    if (typeof target === 'string') {
                        $row = $row.find(target);
                    }

                    Blugento.equalHeight($row);
                }
            });
        },

        jumpTo: function(element, callback)
        {
            $('html, body').animate({
                scrollTop: $(element).offset().top - 40
            }, 700, function() {
                if (typeof callback === 'function') {
                    callback();
                }
            });
        },
	
		    jumpToIfSticky: function(element, elHeight)
		    {
		    	  var topSection = $(element).offset().top;
			      var topHeight  = topSection - elHeight - 10;
			      $('html, body').animate({
				      scrollTop:  topHeight
			      }, 700);
		    },

        isMobile: function()
        {
            return Blugento.md.mobile;
        },

        getViewportData: function()
        {
            var viewportWidth = 320;
            var viewportHeight = 568;

            if (typeof window !== 'undefined') {
                const visualViewport = window.visualViewport || {};

                viewportWidth = visualViewport.width || window.innerWidth;
                viewportHeight = visualViewport.height || window.innerHeight;
            }

            return [viewportWidth, viewportHeight];
        },

        isMobileDesign: function()
        {
            return (window.innerWidth < 996);
        },

        initBodyClass: function() {
            var width = $('.page-container-wrapper').width(),
                pageWidth = $('body').width();

            if (pageWidth - width <= 200) {
                $('body').addClass('page-main-overflow');
            }
        },

        initDevice: function()
        {
            Blugento.md = new MobileDetect(window.navigator.userAgent);
            Blugento.md.mobile = Blugento.md.mobile();

            if (Blugento.isMobile()) {
                Blugento.e = 'touchstart';
                $('html').removeClass('no-touch').addClass('touch');
            }
        },

        initAnchors: function()
        {
            $('a[href="#"]').attr('href', 'javascript:void(0);');
        },

        initTables: function()
        {
            $('table').each(function() {
                var heads = $(this).find('thead th');

                $(this).find('tbody td').each(function() {
                    var dataTitle = heads.eq($(this).index()).text().trim();

                    if (dataTitle) {
                        $(this).attr('data-title', dataTitle);
                    }
                });

                $(this).find('tfoot').before($(this).find('tbody'));
            });
        },

        initDocks: function()
        {
            $(document).on('touchstart click', '.filters-mobile-trigger', function(e) {
                e.preventDefault();
            });

            var html = $('html'),
                body = $('body');

            function closeDocks() {
                html.removeClass('html-dock-open');
                body.removeClass('dock-open')
                    .removeClass('dock-open--top')
                    .removeClass('dock-open--right')
                    .removeClass('dock-open--bottom')
                    .removeClass('dock-open--left')
                    .removeAttr('data-dock');

                $('[data-dock]').removeClass('dock-trigger--active');


                $('.dock').removeClass('dock--active')
                    .removeClass('dock--top')
                    .removeClass('dock--right')
                    .removeClass('dock--bottom')
                    .removeClass('dock--left')
                    .parent().removeClass('wrap-dock--active');

                $('.dock-close-active').remove();

                body.delay(200).queue(function(next){
                    $('body').removeClass('pointer-events-disabled');
                    next();
                });
            }

            function openDock(trigger, dock) {
                var dockSelector = trigger.data('dock'),
                    dockPosition = trigger.data('dock-position') || 'left';

                html.addClass('html-dock-open');
                body.addClass('dock-open')
                    .addClass('dock-open--' + dockPosition)
                    .attr('data-dock', dockSelector);

                trigger.addClass('dock-trigger--active');

                dock.addClass('dock--' + dockPosition)
                    .addClass('dock--active')
                    .parent().addClass('wrap-dock--active');

                dock.parent().append('<span class="dock-close-active"></span>');

                body.delay(200).queue(function(next){
                    $(this).addClass('pointer-events-disabled');
                    next();
                });
            }

            $('#page-overlay').on(Blugento.e, function(e) {
                e.preventDefault();

                if (body.hasClass('dock-open')) {
                    closeDocks();
                }

                $('#nav-mobile').attr('checked', false);
            });

            $(document).on('click', '.dock-close-active', function() {
                if (body.hasClass('dock-open')) {
                    closeDocks();
                }

                $('#nav-mobile').attr('checked', false);
            });

            body.on(Blugento.e, '.dock', function(e) {
                e.stopPropagation();
            });

            body.on(Blugento.e, '[data-dock]', function(e) {
                if(Blugento.isMobileDesign()) {
                    var thiz = $(this),
                        dock = $(thiz.data('dock'));

                    e.preventDefault();

                    if (dock.length) {
                        $('.dock').removeClass('dock--active');

                        if (body.hasClass('dock-open')) {
                            closeDocks();
                        } else {
                            openDock(thiz, dock);
                        }

                        return false;
                    }
                }
            });
        },

        initModalCart: function ()
        {
            var dataModal = $('.block-cart-aside').attr('data-modal');

            if(dataModal == '1') {
                $('.block-cart > a').removeAttr('data-dock');
                var body = $('body');
                cartBlock = $('.block-cart-aside');

                $(window).on('load blugento.window.resize', function() {
                    if(Blugento.isMobileDesign()) {
                        cartWidth = $(window).width();
                        cartBlock.width(cartWidth);
                    } else {
                        cartWidth = '420';
                        cartBlock.width(cartWidth);
                    }
                });

                $('.block-cart > a').each(function (e) {
                    $(this).on('touchstart click', function (e) {
                        e.preventDefault();
                        body.addClass('cart-modal-open');

                        if ($(this).hasClass('cart-active')) {
                            body.animate({
                                left: "0"
                            }, function () {
                                body.removeClass('cart-modal-open');
                            });
                            cartBlock.animate({
                                right: -cartWidth
                            });
                            $(this).removeClass('cart-active');
                        } else {
                            body.animate({
                                left: -cartWidth
                            });
                            cartBlock.animate({
                                right: "0"
                            });
                            $(this).addClass('cart-active');
                        }
                    });
                });

                $('.overlay-modal, .close-modal, .btn-close').on('click', function (e) {
                    e.preventDefault();
                    body.animate({
                        left: "0"
                    }, function() {
                        body.removeClass('cart-modal-open');
                    });
                    cartBlock.animate({
                        right: -cartWidth
                    });
                    $('.block-cart > a').removeClass('cart-active');
                });
            }
        },

        initNav: function()
        {
            var nav = $('.nav'),
                navMobileTrigger = $('.nav-mobile-trigger'),
                dataNewLayout = navMobileTrigger.attr('data-new-layout');
	
		        if (Blugento.isMobile() || Blugento.isMobileDesign()) {
			        $('.on-mobile .nav-mobile-trigger').on('touchstart click', function(e) {
				        e.preventDefault();
			        });
		        } else {
			        $(document).on('touchstart click', '.on-mobile .nav-mobile-trigger', function(e) {
				        e.preventDefault();
			        });
		        }

            function f(e) {
                if (Blugento.isMobile() || Blugento.isMobileDesign()) {
                    e.stopPropagation();

                    if ( (dataNewLayout != 3) && !($(this).parent().hasClass('active') && nav.hasClass('expanded' ))) {
                        e.preventDefault();
                    }

                    if (dataNewLayout != 3) {
                        $(this).closest(nav).addClass('expanded'); 
                        $('.nav-modal-open .nav-container').addClass('expanded-nav');

                        nav.find('li.active').removeClass('active').removeClass('current-exp');
                        $(this).parent().addClass('active').addClass('current-exp').parentsUntil(nav, 'li').addClass('subactive');
                    }
                }
            }

            nav.find('li.parent > a, a.level0.parent').on('mousedown', function() {
                $(this).one('click', f);
            }).on('mousemove mouseout', function() {
                $(this).off('click');
            });

            nav.on(Blugento.e, '[data-action="back"]', function(e) {
                e.preventDefault();
                
                if ($('li.level0').hasClass('current-exp') || $('li.level1').hasClass('current-exp')) {
                    $('.active').removeClass('active');
                    $('.subactive').removeClass('subactive')
                      .addClass('active');
                } else {
                    nav.find('.current-exp').removeClass('active').removeClass('current-exp')
                      .parent().parent().removeClass('subactive').addClass('active').addClass('current-exp')
                      .parentsUntil(nav, 'li').addClass('subactive');
                }
                
                if (nav.find('.active').size() === 0) {
                    nav.removeClass('expanded');
                    $('.nav-modal-open .nav-container').removeClass('expanded-nav');
                }
            });
        },
        
        initNavNewLayout: function()
        {
            var navLayoutBlock = $('.nav-container');

            if (Blugento.isMobileDesign()) {
                var navMobileTrigger = $('.nav-mobile-trigger');
                var dataNewLayout = navMobileTrigger.attr('data-new-layout');
                var body = $('body');
                var dataModal = $('.block-cart-aside').attr('data-modal');
                var navLayoutWidth = 0;

                if ($(window).width() < 480) {
                    navLayoutWidth = $(window).width() - 80;
                    if (dataModal == 1) {
                        navLayoutWidth = $(window).width();
                    }
                    navLayoutBlock.width(navLayoutWidth);
                } else {
                    navLayoutWidth = '420';
                    navLayoutBlock.width(navLayoutWidth);
                }
                
                if (dataNewLayout == 2) {
                    $('.nav-mobile-trigger').removeAttr('data-dock');
                    body.on('touchstart', '.nav-mobile-trigger', function () {
                        body.addClass('nav-modal-open');

                        if ($(this).hasClass('nav-layout-active')) {
                            body.animate({
                                left: "0"
                            }, function () {
                                body.removeClass('nav-modal-open');
                            });
                            navLayoutBlock.animate({
                                left: -navLayoutWidth
                            });
                            $(this).removeClass('nav-layout-active');
                        } else {
                            body.animate({
                                left: navLayoutWidth
                            });
                            navLayoutBlock.animate({
                                left: "0"
                            });
                            $(this).addClass('nav-layout-active');
                        }
                    });
    
                    $('.menu-overlay-modal').on('click', function (e) {
                        body.animate({
                            left: "0"
                        }, function () {
                            body.removeClass('nav-modal-open');
                        });
                        navLayoutBlock.animate({
                            left: -navLayoutWidth
                        });
                        $('.nav-mobile-trigger').removeClass('nav-layout-active');
                    });
                } else if (dataNewLayout == 3) {
                    $('.nav--primary span.has-children').each(function () {
                        $(this).on('click', function (e) {
                            $(this).parent().siblings().removeClass('expanded');
                            $(this).parent().siblings().find('.expanded').removeClass('expanded');
                            $(this).parent().siblings().find('span.has-children').removeClass('minus');

                            if ($(this).parent().hasClass('expanded')) {
                                $(this).removeClass('minus');
                                $(this).parent().removeClass('expanded');
                            } else {
                                $(this).addClass('minus');
                                $(this).parent().addClass('expanded');
                            }
                        });
                    });
                }
            }
        },

        iniStickytMenuMobile: function()
        {
            $(document).on('touchstart click', '.page-container-wrapper--sticky .nav-mobile-trigger', function(e) {
                e.preventDefault();
            });
        },
    
        initNavPushDown: function () {
            var nav = $('#nav'),
              navLayout = parseInt(nav.data('layout'));
        
            if (navLayout === 6) {
                nav.find('li.parent > a').addClass('down').append('<span class="toggle-menu"></span>');
                nav.find('.toggle-menu').click(function(e) {
                  if ( ! Blugento.isMobileDesign()) {
                        var anchor = $(this).parent();
                    
                        e.preventDefault();
                    
                        anchor.stop().next().slideToggle(400, function () {
                            if ($(this).is(':hidden')) {
                                anchor.addClass('down').removeClass('up');
                            } else {
                                anchor.removeClass('down').addClass('up');
                            }
                        });
                    }
                });
            }
        },

        initNavSubmenu: function()
        {
            var nav = $('.nav'),
                navLayout = parseInt(nav.data('layout'));

            function init() {
                var containerWidth = $('.page-header .page-container').width(),
                    nav = $('.nav'),
                    navWidth = nav.width(),
                    button = $('#nav-primary-button');

                $('.submenu', nav).css('width', containerWidth - navWidth);
            }

            if (navLayout === 5 || navLayout === 9) {
                init();

                $(window).on('load blugento.window.resize', function() {
                    init();
                });
            }
        },

        initSearch: function()
        {
            $('#page-overlay').on(Blugento.e, function(e) {
                body.removeClass('search-open');
            });

            var body = $('body'),
                trigger = $('.mobile-trigger--search');

            $('.mini-search').on('click', function(e) {
                e.stopPropagation();
            });

            $(trigger).on('click', function(e) {
                body.addClass('search-open');
                $("#search").focus();
                e.stopPropagation();
            });
        },

        initReveal: function()
        {
            $('.reveal-trigger').on(Blugento.e, function(e) {
                $(this).parent().toggleClass('reveal--active');
                return false;
            });
        },

        initBlocks: function()
        {
            $('.main-aside .block-title').on('click', function() {
                var blockContent = $(this).parent().find('.block-content');

                if (Blugento.isMobileDesign()) {
                    blockContent.toggle();
                } else {
                    blockContent.show();
                }
            });
        },

        initHover: function()
        {
            if (Blugento.isMobile()) {
                var body = $('body');

                body.on('touchstart', function(e) {
                    $('[data-hover]').removeClass('hover');
                });

                $('[data-hover]').on('touchstart', function(e) {
                    if ( ! $(this).hasClass('hover')) {
                        $(this).addClass('hover');
                        return false;
                    }
                    e.stopPropagation();
                });
            }
        },

        equalHeight: function(items)
        {
            $(items).css({
                'min-height': ''
            });

            var maxHeight = 0;

            $(items).each(function(i, e) {
                var itemHeight = $(e).outerHeight();

                if (itemHeight > maxHeight) {
                    maxHeight = itemHeight;
                }
            });

            $(items).css('min-height', maxHeight + 1);
        },

        throwEvent: function(event, element, options)
        {
            element.trigger(event, options);
        },
        removeStickyHeader: function()
        {
            $('#logo-placeholder').replaceWith($('#logo'));
            $('#nav-wrapper-placeholder').replaceWith($('#nav-wrapper'));
            $('#before-links-placeholder').replaceWith($('.links-before'));
            $('#after-links-placeholder').replaceWith($('.links-after'));
            $('#mini-cart-placeholder').replaceWith($('#mini-cart'));
            $('#mini-search-placeholder').replaceWith($('#mini-search'));
            $('#mini-account-placeholder').replaceWith($('.mini-account'));
            $('#mini-wishlist-placeholder').replaceWith($('.page-container-wrapper--sticky .header-wishlist-count'));
        },

        updateStickyHeader: function(container, element, offset, stickyNav)
        {
            var logo = $('#logo'),
                logoWrapperSticky = $('#logo-wrapper-sticky'),
                navWrapper = $('#nav-wrapper'),
                beforeLinks = $('.links-before'),
                afterLink = $('.links-after'),
                navContainerSticky = $('#nav-container-sticky'),
                miniCart = $('#mini-cart'),
                miniCartWrapperSticky = $('#mini-cart-wrapper-sticky'),
                miniSearch = $('#mini-search'),
                miniSearchWrapperSticky = $('#mini-search-wrapper-sticky'),
                miniAccount = $('.mini-account'),
                miniAccountSticky = $('#account-sticky'),
                miniWishlist = $('.desktop .header-wishlist-count'),
                miniWishlistSticky = $('.page-container-wrapper--sticky #wishlist-count-sticky');

            if (this.sticky && ($(window).scrollTop() > offset)) {
                container.addClass('sticky');
                container.css('min-height', offset + 'px');
                const stickyLinks = container.data('sticky-links');
                
                if (logoWrapperSticky.is(':empty')) {
                    logo.before('<div id="logo-placeholder" class="logo"></div>')
                        .appendTo(logoWrapperSticky);
                }

                if (stickyNav && navContainerSticky.is(':empty')) {
                    if (stickyLinks === 1) {
                        beforeLinks.before('<div id="before-links-placeholder" class="no-display"></div>')
                          .appendTo(navContainerSticky);
                    }
                    
                    navWrapper.before('<div id="nav-wrapper-placeholder" class="no-display"></div>')
                        .appendTo(navContainerSticky);
    
                    if (stickyLinks === 1) {
                        afterLink.before('<div id="after-links-placeholder" class="no-display"></div>')
                          .appendTo(navContainerSticky);
                    }
                }

                if (miniCartWrapperSticky.is(':empty')) {
                    miniCart.before('<div id="mini-cart-placeholder" class="no-display"></div>')
                        .appendTo(miniCartWrapperSticky);
                }

                if (miniSearchWrapperSticky.is(':empty')) {
                    miniSearch.before('<div id="mini-search-placeholder" class="no-display"></div>')
                        .appendTo(miniSearchWrapperSticky);
                }

                if (miniAccountSticky.is(':empty')) {
                    miniAccount.before('<div id="mini-account-placeholder" class="no-display"></div>')
                        .appendTo(miniAccountSticky);
                }

                if (miniWishlistSticky.is(':empty')) {
                    miniWishlist.before('<div id="mini-wishlist-placeholder" class="no-display"></div>')
                        .appendTo(miniWishlistSticky);
                }
            } else {
                container.removeClass('sticky');
                container.css('min-height', '');
                this.removeStickyHeader();
            }
        },

        initStickyHeader: function()
        {
            if ($('header.page-header').length) {
                var thiz = this,
                    header = $('header.page-header'),
                    headerHeight = header.height(),
                    headerOffset = header.offset().top,
                    status = parseInt(header.data('sticky')),
                    nav = parseInt(header.data('nav'));
    
                if (status === 1) {
                    var element = $('#header-sticky-content'),
                        elementHeight = element.height();
    
                    thiz.sticky = $(document).width() > 319;
                    thiz.stickyNav = (nav === 2) || (nav === 3) || (nav === 8) || (nav === 9) || (nav === 10) || (nav === 11) || (nav === 12) || (nav === 13);
    
                    $(window).on('blugento.window.resize', function() {
                        thiz.sticky = $(document).width() > 319;
                    });
    
                    Blugento.updateStickyHeader(header, element, headerHeight + headerOffset, thiz.stickyNav);
    
                    $(window).on('scroll blugento.window.resize', function() {
                        Blugento.updateStickyHeader(header, element, headerHeight + headerOffset, thiz.stickyNav);
                    });
                }
            }
        },

        initToTop: function()
        {
            var anchor = $('#to-top'),
                offset = 300,
                duration = 400;

            if (anchor.length) {
                anchor.click(function() {
                    $('html, body').animate({
                        scrollTop: 0
                    }, duration);
                });

                $(window).on('scroll', function() {
                    if ($(this).scrollTop() > offset) {
                        anchor.addClass('visible');
                    } else {
                        anchor.removeClass('visible');
                    }
                });
            }
        },

        initSlick: function()
        {
            try {
                /**
                 * Check if modal related it's enabled
                 * Clone related products to modal
                 */
                if ($('#block-related').length && $('.modal-related').length) {
                    $('#block-related').clone().appendTo('.modal-related');
                }

                /**
                 * Check if modal upsell it's enabled
                 * Clone upsell products to modal
                 */
                if ($('#block-upsell').length && $('.modal-upsell').length) {
                    $('#block-upsell').clone().appendTo('.modal-upsell');
                }

                $('[data-slider]').not(':hidden').each(function(e, i) {
                    $(this).slick({
                        infinite: $(this).data('slider-item-loop') == 1 ? true : false,
                        speed: parseInt($(this).data('slider-animation')) || 300,
                        slidesToShow: parseInt($(this).data('slider-item-row')) || 4,
                        slidesToScroll: parseInt($(this).data('slider-item-scroll')) || 1,
                        dots: $(this).data('dots') == 1 ? true : false,
	                      autoplay: $(this).data('slider-item-autoplay') == 1 ? true : false,
	                      cssEase: $(this).data('slider-item-cssease'),
                        responsive: [
                            {
                                breakpoint: 1170,
                                settings: {
                                    slidesToShow: 4,
                                    slidesToScroll: 2
                                }
                            },
                            {
                                breakpoint: 980,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 1,
                                    centerMode: $(this).data('center') == 1 ? true : false,
                                    centerPadding: $(this).data('center') == 1 ? '50px' : '0',
                                }
                            },
                            {
                                breakpoint: 768,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 2,
                                    centerMode: $(this).data('center') == 1 ? true : false,
                                    centerPadding: $(this).data('center') == 1 ? '40px' : '0',
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: parseInt($(this).data('mobile-items')) || 1,
                                    slidesToScroll: 1,
                                    centerMode: $(this).data('center') == 1 ? true : false,
                                    centerPadding: $(this).data('center') == 1 ? '30px' : '0',
                                }
                            }
                        ]
                    });
                });

                $(window).on('load', function() {
                    $('[data-slider]').each(function(e, i) {
                        var track = $(this).find('.slick-track');

                        track.find('.item-inner').css('min-height', track.height() + 'px');
                    });
                });
            }
            catch(e) {
                // console.log(e);
            }
        },

        initMagnificPopup: function()
        {
            try {
                $('[data-magnificpopup]').each(function(e, i) {
                    $(this).magnificPopup({
                        type: $(this).data('magnificpopup'),
                        tLoading: 'Loading...',
                        gallery: {
                            enabled: true,
                            navigateByImgClick: true,
                            preload: [0, 1]
                        }
                    });
                });
            }
            catch(e) {
                // console.log(e);
            }
        },

        initPreloader: function()
        {
            var loader = $('#page-loader');

            if (loader.length) {
                setTimeout(function () {
                    loader.fadeOut(300, function () {
                        $('body').removeClass('loading');
                    });
                }, 800);
            }
        },

        initLazyload: function()
        {
            $(window).on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function() {
                $(window).lazyLoadXT();
            });
        },

        initInputText: function()
        {
            var inputTextLayout = $('body').data('input-text-layout');

            if (inputTextLayout == 6) {

                function update(inputBox) {
                    var element = inputBox.children('.input-text, select').filter(function() {
                        return $(this).css('display') != 'none';
                    }).eq(0);

                    if (element.length > 0) {
                        var elementTag = element.prop('tagName').toLowerCase(),
                            elementValue = (elementTag == 'input') ? element.val() : element.find('option:selected').text();

                        elementValue = elementValue.trim();

                        if (elementValue.length == 0) {
                            inputBox.addClass('empty')
                                .removeClass('not-empty');
                        } else {
                            inputBox.removeClass('empty')
                                .addClass('not-empty');
                        }
                    }
                }

                $('form .input-box').each(function() {
                    var label = $(this).prev('label');

                    if (label.length != 0) {
                        label.appendTo($(this));

                        $(this).addClass('input-style');

                        update($(this));

                        $(this).on('change input DOMSubtreeModified', function() {
                            update($(this));
                        });
                    }
                });
            }
        },

        initCalcTabs: function()
        {
            $("#carpet-collateral-tabs .tabs-nav li, #carpet-collateral-tabs .tab-nav").click(function(e) {
                e.preventDefault();

                $("#carpet-collateral-tabs .tabs-nav li").removeClass('activeli');
                $("#carpet-collateral-tabs .tab-nav").removeClass('active');

                $(this).addClass("activeli");
                $(this).addClass("active");

                $("#carpet-collateral-tabs .tab-content").hide();
                var selected_tab_mobile = $(this).attr("href");

                var selected_tab = $(this).find("a").attr("href");

                $(selected_tab).show();
            });
        },

        initCalcTabsMobile: function()
        {
            $('#carpet-collateral-tabs .tab-nav').click(function(e) {
                e.preventDefault();

                $('#carpet-collateral-tabs .tab-nav').removeClass('active');

                $(this).addClass("active");

                $('#carpet-collateral-tabs .tab-content').hide();

                var selected_tab_mobile = $(this).attr("href");

                $(selected_tab_mobile).show();
            });
        },

        initShowArticles: function()
        {
            $('.kbase-list .level-1').each(function() {
                $('> li', this).not('.show-more').not('.show-less').each(function(idx, el) {
                    if (idx >= 4) {
                        $(this).parent().find('.show-more').removeClass('no-display');
                        $(this).addClass('hide');
                    }
                });
            });

            $('.kbase-list .show-more').each(function() {
                $(this).click(function(e) {
                    $(this).parent().addClass('show-articles');
                });
            });

            $('.kbase-list .show-less').each(function() {
                $(this).click(function(e) {
                    $(this).parent().removeClass('show-articles');
                });
            });
        },

        initShowSearch: function()
        {
            var btnSearch = $('.form-search button'),
                searchInput = $('.form-search input');

            $(btnSearch).each(function(e) {
                $(this).on( 'click', function(e) {
                    if($(this).siblings('input').width() < 1) {
                        e.preventDefault();
                        $(this).parents('.mini-search').addClass('show-search');
                        $(this).parent().find('input').focus();
                    } else {
                        if ($(this).siblings('input').val().length == false) {
                            e.preventDefault();
                            $(this).parents('.mini-search').removeClass('show-search');
                        }
                    }
                });
            });

            $(document).on('click', function(){
                $('.mini-search').removeClass('show-search');
            });

            $(searchInput, btnSearch).on( 'click', function(e) {
                e.stopPropagation();
            });

            $(searchInput, btnSearch).each(function(e) {
                $(this).on( 'click', function(e) {
                    e.stopPropagation();
                });
            });
        },

        initMenuFooter: function()
        {
            if (Blugento.isMobile()) {
                $('.menu-toggle-1 .footer-links ul li:first-child').click(function (e) {
                    e.preventDefault();
                    if($(this).parent().hasClass('toggle-footer-menu')) {
                        $(this).parent().removeClass('toggle-footer-menu');
                    } else {
                        $(this).parent().addClass('toggle-footer-menu');
                    }
                });
            }
        },

        initAcountHover: function()
        {
            if ($('.page-header .desktop .mini-account > ul').is(':hidden')) {
                $('.page-header .desktop .mini-account').hover(
                    function() {
                        $(this).find('ul').show(0);
                    }, function() {
                        $(this).find('ul').delay(300).hide(0);
                    }
                );
            }
        },

        initAcountMobile: function()
        {
            if (Blugento.isMobile()) {
                $(document).on('touchstart click', '.customer-account .mobile-trigger--profile-login > a', function(e) {
                    e.preventDefault();
                });
            }
        },

        initInvitationClick: function()
        {
            if (Blugento.isMobile()) {
                $('.invitation-box p').click(function () {
                    if($('.invitation-template').is(':hidden')) {
                        $('.invitation-template').fadeIn(313);
                    } else {
                        $('.invitation-template').fadeOut(313);
                    }
                });
            }
        },

        initMenuHover: function()
        {
            var header = $('header.page-header'),
                nav = parseInt(header.data('nav'));

            if (nav === 12) {
                $('.nav--primary > li.parent').hover(
                    function() {
                        $('body').stop(true,true).addClass('grey-overlay');
                    }, function() {
                        $('body').stop(true,true).removeClass('grey-overlay');
                    }
                );
            }
        },

        initSortBy: function()
        {
            var toolbar = $('.toolbar'),
                sortBy = parseInt(toolbar.data('sortBy'));

            if (sortBy === 3) {
                var selectedSort = $('.toolbar .sort-by li.selected a').html();
                $('.selected-sort').html(selectedSort);

                $('.sort-by .selected-sort').click(function () {
                    $(this).parent().toggleClass('show-sort-list');
                });
            }
        },

        initCart: function()
        {
            if (Blugento.isMobile()) {
                $(document).on('touchstart click', '.block-cart > a', function(e) {
                    e.preventDefault();
                });
            }
        },

        initEmailSpace: function()
        {
            $('.validate-email').change(function() {
                $(this).val($(this).val().trim());
            });
        },
        
        initCartButtonSticky: function () {
            if (Blugento.isMobile()) {
                var cartVisibleY = function (elem) {
                    var rect = elem.getBoundingClientRect(),
                      top = rect.top,
                      height = rect.height,
                      el = elem.parentNode;
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
    
                function attachCartEvent(element, event, callbackFunction) {
                    if (element.addEventListener) {
                        element.addEventListener(event, callbackFunction, false);
                    } else if (element.attachEvent) {
                        element.attachEvent('on' + event, callbackFunction);
                    }
                }
    
                var updateCrt = function () {
                    if ($('body').hasClass('checkout-cart-index')) {
                        cartVisibleY(document.getElementById('button_proceed_to_checkout')) ? $('#btn-proceed-checkout-fixed').removeClass('btn-checkout-fixed') : $('#btn-proceed-checkout-fixed').addClass('btn-checkout-fixed');
                    }
                };
                attachCartEvent(window, "scroll", updateCrt);
                attachCartEvent(window, "resize", updateCrt);
                updateCrt();

                $(document).on('click', '#btn-proceed-checkout-fixed', function () {
                    $('#button_proceed_to_checkout').trigger('click');
                });
            } else if (!Blugento.isMobile()) {
                var cartCollaterals = $('#cart-collaterals-section');
                var cartCollateralsIsSticky = cartCollaterals.data('cart-collaterals-fixed');
                if ($('body').hasClass('checkout-cart-index') && cartCollateralsIsSticky == 1) {
                    $('.main-content').css('position', 'inherit');
                    cartCollaterals.scrollToFixed({
                        marginTop: 20,
                        limit: $('#shopping-cart-table').position().top + $('#shopping-cart-table').outerHeight(true) - cartCollaterals.outerHeight(true),
                        zIndex: 999
                    });
                }
            }
        },

        initResponsiveIframe: function()
        {
            $('iframe').each(function() {
                $(this).wrap('<div class="iframe-container"></div>');
            });
        },

        initTopFilters: function()
        {
            $('.block-layered-nav-top .show-all').each(function() {
                $(this).on('click', function() {
                    $(this).parents('li').siblings().removeClass('active');
                    if ($(this).hasClass('active')) {
                        $(this).parents('li').removeClass('active');
                    } else {
                        $(this).parents('li').addClass('active');
                    }
                });
            });

            $(document).mouseup(function(e) {
                var container = $(".block-layered-nav-top .active");
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.removeClass('active');
                }
            });
        },

        initMenuDropdown: function() {
            $('#nav li.parent, #nav-wrapper').each(function () {
                $(this).on('mouseover touchstart', function () {
                    var $this = $(this);

                    if ($this.prop('hoverTimeout')) {
                        $this.prop('hoverTimeout', clearTimeout($this.prop('hoverTimeout')));
                    }

                    $this.prop('hoverIntent', setTimeout(function () {
                        $this.addClass('hover');
                    }, 250));
                })
                .on('mouseleave touchend', function () {
                    var $this = $(this);

                    if ($this.prop('hoverIntent')) {
                        $this.prop('hoverIntent', clearTimeout($this.prop('hoverIntent')));
                    }

                    $this.prop('hoverTimeout', setTimeout(function () {
                        $this.removeClass('hover');
                    }, 250));
                });
            });
        },
        
        initMenuScroll: function()
        {
            if (Blugento.md.tablet) {
                $(window).on('scroll', function() {
                    if ($(this).scrollTop() > 150) {
                        $('#nav li.parent, #nav-wrapper').each(function () {
                            var $this = $(this);
                            if ($this.hasClass('hover')) {
                                $this.removeClass('hover');
                            }
                        });
                    }
                });
            }
        },

        initSwatchSlider: function()
        {
            if ($('.product-images-swatches').length) {
                $('.configurable-swatch-list li a').each(function() {
                    $(this).on('click', function(e) {
                        var dataId = $(this).attr('data-id'),
                            producImgBox = $('#product-img-box'),
                            ajaxLoader = $('.product-img-box .ajax-loader'),
                            gallery = $('#media-carousel'),
                            productImageSwipe = $('#media-swipe');

                        $(gallery).slick('unslick');
                        $(productImageSwipe).slick('unslick');
                        productImageSwipe.css('opacity', '0');
                        gallery.hide();
                        ajaxLoader.show();

                        if ($('#media-swipe li').hasClass(dataId)) {
                            $(producImgBox).hide();
                            $('.product-img-box.cloned').remove();
                            $(producImgBox).clone().addClass('cloned').attr('id','product-img-box-cloned').insertAfter('.product-img-box');
                            $('.cloned #product-image').attr('id','product-image-cloned');
                            $('#product-img-box-cloned').show();
                            $('.cloned #media-swipe').attr('id','media-swipe-cloned');
                            $('.cloned #more-views').attr('id','more-views-cloned');
                            $('.cloned #media-carousel').attr('id','media-carousel-cloned');
                            $('.cloned .item').not('.' + dataId).remove();

                            gallery = $('#media-carousel-cloned');
                            productImageSwipe = $('#media-swipe-cloned');
                        } else {
                            $('.product-img-box.cloned').remove();
                            $(producImgBox).show();
                        }

                        setTimeout(function(){
                            Blugento.Catalog.productView();
                        }, 200);
                    });
                });
            }
        },
        initTierPriceQty: function()
        {
            if ($('.tier-prices').length) {
                $('.tier-price').each(function( ) {
                    $(this).on('click', function(e) {
                        var dataQty = parseInt($(this).attr('data-qty'));

                        $('#product_addtocart_form #qty').val(dataQty);
                    });
                });
            }
        },
        initCategoryTruncate: function()
        {
            if ($('.description-truncated').length) {
                $('.category-description .description-content').each(function(e) {
                    var categoryDescription = $(this).html();
                    var dataTruncateValue = $(this).parent().attr('data-truncated-value');

                    if (categoryDescription.length <= dataTruncateValue) {
                        $(this).html(categoryDescription);
                    } else {
                        $(this).parent().addClass('show-button');
                        $(this).html(categoryDescription.slice(0, dataTruncateValue) + '...');
                    }

                    $(this).parent().find('.show-more').on('click', function(e) {
                        $(this).parent().addClass('show-full-content');
                        $(this).parent().find('.description-content').html(categoryDescription);
                    });

                    $(this).parent().find('.show-less').on('click', function(e) {
                        $(this).parent().removeClass('show-full-content');
                        $(this).parent().find('.description-content').html(categoryDescription.slice(0, dataTruncateValue) + '...');
                    });
                });
            }
        },

        initDescriptionTruncate: function()
        {
            function descriptionToggle() {
                $('#pc-tab-description .std').each(function(i, _) {
                    var description = $(this).html();
                    var dataTruncateValue = $(this).parent().data('truncated-value');

                    if (description.length <= parseInt(dataTruncateValue)) {
                        $(this).html(description);
                    } else {
                        $(this).parent().addClass('toggle-description');
                        $(this).addClass('truncated-description').html(description.slice(0, dataTruncateValue) + '...');
                    }

                    if (description.length > parseInt(dataTruncateValue)) {
                        $(this).parent().find('.show-description').on('click', function () {
                            $('#pc-tab-description .std').removeClass('truncated-description').html(description);
                            $('a.hide-description').show();
                            $(this).hide();
                        });
        
                        $(this).parent().find('.hide-description').on('click', function () {
                            $('#pc-tab-description .std').addClass('truncated-description').html(description.slice(0, dataTruncateValue) + '...');
                            $('a.show-description').show();
                            $(this).hide();
                        });
                    } else {
                        $(this).parent().find('.show-description').hide();
                        $(this).parent().find('.hide-description').hide();
                    }
                });
            }

            if ($('#pc-tab-description').data('toggle') === true) {
                descriptionToggle();
            }
        },
        initGDPRCheckbox: function()
        {
            if ($('.gdpr-conditions').length > 0) {
                $('.gdpr-conditions').each(function() {
                    $(this).find('input').each(function(i, item) {
                        $(item).attr('id', 'gdpr-conditions-' + i);
                        $(item).siblings('label').attr('for', 'gdpr-conditions-' + i);
                    });
                });
            }
        },
    });


    Blugento.init();

    // Generic, efficient window resize handler
    $(window).on('resize', function() {
        function resizeDone() {
            Blugento.throwEvent('blugento.window.resize', $(window));
        }
        clearTimeout(Blugento.resizeId);
        Blugento.resizeId = setTimeout(resizeDone, 200);
    });

    Ajax.Responders.register({
        onCreate: function() {
        },
        onComplete: function() {
            setTimeout(function() {
                Blugento.initInputText();
                $(window).lazyLoadXT();
            }, 50);
        }
    });

})(jQuery);
