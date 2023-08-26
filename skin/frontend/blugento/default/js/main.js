/*! blugento-theme v1.0.0 - 2023-01-24 20:07:47 */
;var Blugento=Blugento||{};!function(a){a.extend(Blugento,{e:"click",md:null,resizeId:!1,sticky:!1,stickyNav:!0,init:function(){var b=this;a(document).ready(function(){b.initToTop(),b.initDevice(),b.initSlick(),b.initMagnificPopup(),b.initDocks(),b.initModalCart(),b.initNav(),b.initNavPushDown(),b.initNavSubmenu(),b.initAnchors(),b.initTables(),b.initSearch(),b.initBlocks(),b.initReveal(),b.initHover(),b.initLazyload(),b.initInputText(),b.initBodyClass(),b.initCalcTabs(),b.initCalcTabsMobile(),b.initShowArticles(),b.initShowSearch(),b.initMenuFooter(),b.initAcountHover(),b.initInvitationClick(),b.initMenuHover(),b.initSortBy(),b.initCart(),b.initAcountMobile(),b.iniStickytMenuMobile(),b.initEmailSpace(),b.initCartButtonSticky(),b.getViewportData(),b.initNavNewLayout(),b.initResponsiveIframe(),b.initTopFilters(),b.initMenuDropdown(),b.initSwatchSlider(),b.initMenuScroll(),b.initTierPriceQty(),b.initCategoryTruncate(),b.initDescriptionTruncate(),b.initGDPRCheckbox()}),a(window).on("blugento.window.resize",function(){b.initBodyClass(),b.getViewportData()}),a(window).load(function(){b.initPreloader(),b.initStickyHeader()})},flex:function(b,c,d){var e=document.body||document.documentElement;e.style;a(b).each(function(){var b=a(this).find(c),e=Math.floor(a(this).width()/b.width());if(null==e||e<2)return!0;for(var f=0,g=b.length;f<g;f+=e){var h=b.slice(f,f+e);"string"==typeof d&&(h=h.find(d)),Blugento.equalHeight(h)}})},jumpTo:function(b,c){a("html, body").animate({scrollTop:a(b).offset().top-40},700,function(){"function"==typeof c&&c()})},jumpToIfSticky:function(b,c){var d=a(b).offset().top,e=d-c-10;a("html, body").animate({scrollTop:e},700)},isMobile:function(){return Blugento.md.mobile},getViewportData:function(){var a=320,b=568;if("undefined"!=typeof window){const c=window.visualViewport||{};a=c.width||window.innerWidth,b=c.height||window.innerHeight}return[a,b]},isMobileDesign:function(){return window.innerWidth<996},initBodyClass:function(){var b=a(".page-container-wrapper").width(),c=a("body").width();c-b<=200&&a("body").addClass("page-main-overflow")},initDevice:function(){Blugento.md=new MobileDetect(window.navigator.userAgent),Blugento.md.mobile=Blugento.md.mobile(),Blugento.isMobile()&&(Blugento.e="touchstart",a("html").removeClass("no-touch").addClass("touch"))},initAnchors:function(){a('a[href="#"]').attr("href","javascript:void(0);")},initTables:function(){a("table").each(function(){var b=a(this).find("thead th");a(this).find("tbody td").each(function(){var c=b.eq(a(this).index()).text().trim();c&&a(this).attr("data-title",c)}),a(this).find("tfoot").before(a(this).find("tbody"))})},initDocks:function(){function b(){d.removeClass("html-dock-open"),e.removeClass("dock-open").removeClass("dock-open--top").removeClass("dock-open--right").removeClass("dock-open--bottom").removeClass("dock-open--left").removeAttr("data-dock"),a("[data-dock]").removeClass("dock-trigger--active"),a(".dock").removeClass("dock--active").removeClass("dock--top").removeClass("dock--right").removeClass("dock--bottom").removeClass("dock--left").parent().removeClass("wrap-dock--active"),a(".dock-close-active").remove(),e.delay(200).queue(function(b){a("body").removeClass("pointer-events-disabled"),b()})}function c(b,c){var f=b.data("dock"),g=b.data("dock-position")||"left";d.addClass("html-dock-open"),e.addClass("dock-open").addClass("dock-open--"+g).attr("data-dock",f),b.addClass("dock-trigger--active"),c.addClass("dock--"+g).addClass("dock--active").parent().addClass("wrap-dock--active"),c.parent().append('<span class="dock-close-active"></span>'),e.delay(200).queue(function(b){a(this).addClass("pointer-events-disabled"),b()})}a(document).on("touchstart click",".filters-mobile-trigger",function(a){a.preventDefault()});var d=a("html"),e=a("body");a("#page-overlay").on(Blugento.e,function(c){c.preventDefault(),e.hasClass("dock-open")&&b(),a("#nav-mobile").attr("checked",!1)}),a(document).on("click",".dock-close-active",function(){e.hasClass("dock-open")&&b(),a("#nav-mobile").attr("checked",!1)}),e.on(Blugento.e,".dock",function(a){a.stopPropagation()}),e.on(Blugento.e,"[data-dock]",function(d){if(Blugento.isMobileDesign()){var f=a(this),g=a(f.data("dock"));if(d.preventDefault(),g.length)return a(".dock").removeClass("dock--active"),e.hasClass("dock-open")?b():c(f,g),!1}})},initModalCart:function(){var b=a(".block-cart-aside").attr("data-modal");if("1"==b){a(".block-cart > a").removeAttr("data-dock");var c=a("body");cartBlock=a(".block-cart-aside"),a(window).on("load blugento.window.resize",function(){Blugento.isMobileDesign()?(cartWidth=a(window).width(),cartBlock.width(cartWidth)):(cartWidth="420",cartBlock.width(cartWidth))}),a(".block-cart > a").each(function(b){a(this).on("touchstart click",function(b){b.preventDefault(),c.addClass("cart-modal-open"),a(this).hasClass("cart-active")?(c.animate({left:"0"},function(){c.removeClass("cart-modal-open")}),cartBlock.animate({right:-cartWidth}),a(this).removeClass("cart-active")):(c.animate({left:-cartWidth}),cartBlock.animate({right:"0"}),a(this).addClass("cart-active"))})}),a(".overlay-modal, .close-modal, .btn-close").on("click",function(b){b.preventDefault(),c.animate({left:"0"},function(){c.removeClass("cart-modal-open")}),cartBlock.animate({right:-cartWidth}),a(".block-cart > a").removeClass("cart-active")})}},initNav:function(){function b(b){(Blugento.isMobile()||Blugento.isMobileDesign())&&(b.stopPropagation(),3==e||a(this).parent().hasClass("active")&&c.hasClass("expanded")||b.preventDefault(),3!=e&&(a(this).closest(c).addClass("expanded"),a(".nav-modal-open .nav-container").addClass("expanded-nav"),c.find("li.active").removeClass("active").removeClass("current-exp"),a(this).parent().addClass("active").addClass("current-exp").parentsUntil(c,"li").addClass("subactive")))}var c=a(".nav"),d=a(".nav-mobile-trigger"),e=d.attr("data-new-layout");Blugento.isMobile()||Blugento.isMobileDesign()?a(".on-mobile .nav-mobile-trigger").on("touchstart click",function(a){a.preventDefault()}):a(document).on("touchstart click",".on-mobile .nav-mobile-trigger",function(a){a.preventDefault()}),c.find("li.parent > a, a.level0.parent").on("mousedown",function(){a(this).one("click",b)}).on("mousemove mouseout",function(){a(this).off("click")}),c.on(Blugento.e,'[data-action="back"]',function(b){b.preventDefault(),a("li.level0").hasClass("current-exp")||a("li.level1").hasClass("current-exp")?(a(".active").removeClass("active"),a(".subactive").removeClass("subactive").addClass("active")):c.find(".current-exp").removeClass("active").removeClass("current-exp").parent().parent().removeClass("subactive").addClass("active").addClass("current-exp").parentsUntil(c,"li").addClass("subactive"),0===c.find(".active").size()&&(c.removeClass("expanded"),a(".nav-modal-open .nav-container").removeClass("expanded-nav"))})},initNavNewLayout:function(){var b=a(".nav-container");if(Blugento.isMobileDesign()){var c=a(".nav-mobile-trigger"),d=c.attr("data-new-layout"),e=a("body"),f=a(".block-cart-aside").attr("data-modal"),g=0;a(window).width()<480?(g=a(window).width()-80,1==f&&(g=a(window).width()),b.width(g)):(g="420",b.width(g)),2==d?(a(".nav-mobile-trigger").removeAttr("data-dock"),e.on("touchstart",".nav-mobile-trigger",function(){e.addClass("nav-modal-open"),a(this).hasClass("nav-layout-active")?(e.animate({left:"0"},function(){e.removeClass("nav-modal-open")}),b.animate({left:-g}),a(this).removeClass("nav-layout-active")):(e.animate({left:g}),b.animate({left:"0"}),a(this).addClass("nav-layout-active"))}),a(".menu-overlay-modal").on("click",function(c){e.animate({left:"0"},function(){e.removeClass("nav-modal-open")}),b.animate({left:-g}),a(".nav-mobile-trigger").removeClass("nav-layout-active")})):3==d&&a(".nav--primary span.has-children").each(function(){a(this).on("click",function(b){a(this).parent().siblings().removeClass("expanded"),a(this).parent().siblings().find(".expanded").removeClass("expanded"),a(this).parent().siblings().find("span.has-children").removeClass("minus"),a(this).parent().hasClass("expanded")?(a(this).removeClass("minus"),a(this).parent().removeClass("expanded")):(a(this).addClass("minus"),a(this).parent().addClass("expanded"))})})}},iniStickytMenuMobile:function(){a(document).on("touchstart click",".page-container-wrapper--sticky .nav-mobile-trigger",function(a){a.preventDefault()})},initNavPushDown:function(){var b=a("#nav"),c=parseInt(b.data("layout"));6===c&&(b.find("li.parent > a").addClass("down").append('<span class="toggle-menu"></span>'),b.find(".toggle-menu").click(function(b){if(!Blugento.isMobileDesign()){var c=a(this).parent();b.preventDefault(),c.stop().next().slideToggle(400,function(){a(this).is(":hidden")?c.addClass("down").removeClass("up"):c.removeClass("down").addClass("up")})}}))},initNavSubmenu:function(){function b(){var b=a(".page-header .page-container").width(),c=a(".nav"),d=c.width();a("#nav-primary-button");a(".submenu",c).css("width",b-d)}var c=a(".nav"),d=parseInt(c.data("layout"));5!==d&&9!==d||(b(),a(window).on("load blugento.window.resize",function(){b()}))},initSearch:function(){a("#page-overlay").on(Blugento.e,function(a){b.removeClass("search-open")});var b=a("body"),c=a(".mobile-trigger--search");a(".mini-search").on("click",function(a){a.stopPropagation()}),a(c).on("click",function(c){b.addClass("search-open"),a("#search").focus(),c.stopPropagation()})},initReveal:function(){a(".reveal-trigger").on(Blugento.e,function(b){return a(this).parent().toggleClass("reveal--active"),!1})},initBlocks:function(){a(".main-aside .block-title").on("click",function(){var b=a(this).parent().find(".block-content");Blugento.isMobileDesign()?b.toggle():b.show()})},initHover:function(){if(Blugento.isMobile()){var b=a("body");b.on("touchstart",function(b){a("[data-hover]").removeClass("hover")}),a("[data-hover]").on("touchstart",function(b){return a(this).hasClass("hover")?void b.stopPropagation():(a(this).addClass("hover"),!1)})}},equalHeight:function(b){a(b).css({"min-height":""});var c=0;a(b).each(function(b,d){var e=a(d).outerHeight();e>c&&(c=e)}),a(b).css("min-height",c+1)},throwEvent:function(a,b,c){b.trigger(a,c)},removeStickyHeader:function(){a("#logo-placeholder").replaceWith(a("#logo")),a("#nav-wrapper-placeholder").replaceWith(a("#nav-wrapper")),a("#before-links-placeholder").replaceWith(a(".links-before")),a("#after-links-placeholder").replaceWith(a(".links-after")),a("#mini-cart-placeholder").replaceWith(a("#mini-cart")),a("#mini-search-placeholder").replaceWith(a("#mini-search")),a("#mini-account-placeholder").replaceWith(a(".mini-account")),a("#mini-wishlist-placeholder").replaceWith(a(".page-container-wrapper--sticky .header-wishlist-count"))},updateStickyHeader:function(b,c,d,e){var f=a("#logo"),g=a("#logo-wrapper-sticky"),h=a("#nav-wrapper"),i=a(".links-before"),j=a(".links-after"),k=a("#nav-container-sticky"),l=a("#mini-cart"),m=a("#mini-cart-wrapper-sticky"),n=a("#mini-search"),o=a("#mini-search-wrapper-sticky"),p=a(".mini-account"),q=a("#account-sticky"),r=a(".desktop .header-wishlist-count"),s=a(".page-container-wrapper--sticky #wishlist-count-sticky");if(this.sticky&&a(window).scrollTop()>d){b.addClass("sticky"),b.css("min-height",d+"px");const t=b.data("sticky-links");g.is(":empty")&&f.before('<div id="logo-placeholder" class="logo"></div>').appendTo(g),e&&k.is(":empty")&&(1===t&&i.before('<div id="before-links-placeholder" class="no-display"></div>').appendTo(k),h.before('<div id="nav-wrapper-placeholder" class="no-display"></div>').appendTo(k),1===t&&j.before('<div id="after-links-placeholder" class="no-display"></div>').appendTo(k)),m.is(":empty")&&l.before('<div id="mini-cart-placeholder" class="no-display"></div>').appendTo(m),o.is(":empty")&&n.before('<div id="mini-search-placeholder" class="no-display"></div>').appendTo(o),q.is(":empty")&&p.before('<div id="mini-account-placeholder" class="no-display"></div>').appendTo(q),s.is(":empty")&&r.before('<div id="mini-wishlist-placeholder" class="no-display"></div>').appendTo(s)}else b.removeClass("sticky"),b.css("min-height",""),this.removeStickyHeader()},initStickyHeader:function(){if(a("header.page-header").length){var b=this,c=a("header.page-header"),d=c.height(),e=c.offset().top,f=parseInt(c.data("sticky")),g=parseInt(c.data("nav"));if(1===f){var h=a("#header-sticky-content");h.height();b.sticky=a(document).width()>319,b.stickyNav=2===g||3===g||8===g||9===g||10===g||11===g||12===g||13===g,a(window).on("blugento.window.resize",function(){b.sticky=a(document).width()>319}),Blugento.updateStickyHeader(c,h,d+e,b.stickyNav),a(window).on("scroll blugento.window.resize",function(){Blugento.updateStickyHeader(c,h,d+e,b.stickyNav)})}}},initToTop:function(){var b=a("#to-top"),c=300,d=400;b.length&&(b.click(function(){a("html, body").animate({scrollTop:0},d)}),a(window).on("scroll",function(){a(this).scrollTop()>c?b.addClass("visible"):b.removeClass("visible")}))},initSlick:function(){try{a("#block-related").length&&a(".modal-related").length&&a("#block-related").clone().appendTo(".modal-related"),a("#block-upsell").length&&a(".modal-upsell").length&&a("#block-upsell").clone().appendTo(".modal-upsell"),a("[data-slider]").not(":hidden").each(function(b,c){a(this).slick({infinite:1==a(this).data("slider-item-loop"),speed:parseInt(a(this).data("slider-animation"))||300,slidesToShow:parseInt(a(this).data("slider-item-row"))||4,slidesToScroll:parseInt(a(this).data("slider-item-scroll"))||1,dots:1==a(this).data("dots"),autoplay:1==a(this).data("slider-item-autoplay"),cssEase:a(this).data("slider-item-cssease"),responsive:[{breakpoint:1170,settings:{slidesToShow:4,slidesToScroll:2}},{breakpoint:980,settings:{slidesToShow:3,slidesToScroll:1,centerMode:1==a(this).data("center"),centerPadding:1==a(this).data("center")?"50px":"0"}},{breakpoint:768,settings:{slidesToShow:2,slidesToScroll:2,centerMode:1==a(this).data("center"),centerPadding:1==a(this).data("center")?"40px":"0"}},{breakpoint:480,settings:{slidesToShow:parseInt(a(this).data("mobile-items"))||1,slidesToScroll:1,centerMode:1==a(this).data("center"),centerPadding:1==a(this).data("center")?"30px":"0"}}]})}),a(window).on("load",function(){a("[data-slider]").each(function(b,c){var d=a(this).find(".slick-track");d.find(".item-inner").css("min-height",d.height()+"px")})})}catch(a){}},initMagnificPopup:function(){try{a("[data-magnificpopup]").each(function(b,c){a(this).magnificPopup({type:a(this).data("magnificpopup"),tLoading:"Loading...",gallery:{enabled:!0,navigateByImgClick:!0,preload:[0,1]}})})}catch(a){}},initPreloader:function(){var b=a("#page-loader");b.length&&setTimeout(function(){b.fadeOut(300,function(){a("body").removeClass("loading")})},800)},initLazyload:function(){a(window).on("webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend",function(){a(window).lazyLoadXT()})},initInputText:function(){function b(b){var c=b.children(".input-text, select").filter(function(){return"none"!=a(this).css("display")}).eq(0);if(c.length>0){var d=c.prop("tagName").toLowerCase(),e="input"==d?c.val():c.find("option:selected").text();e=e.trim(),0==e.length?b.addClass("empty").removeClass("not-empty"):b.removeClass("empty").addClass("not-empty")}}var c=a("body").data("input-text-layout");6==c&&a("form .input-box").each(function(){var c=a(this).prev("label");0!=c.length&&(c.appendTo(a(this)),a(this).addClass("input-style"),b(a(this)),a(this).on("change input DOMSubtreeModified",function(){b(a(this))}))})},initCalcTabs:function(){a("#carpet-collateral-tabs .tabs-nav li, #carpet-collateral-tabs .tab-nav").click(function(b){b.preventDefault(),a("#carpet-collateral-tabs .tabs-nav li").removeClass("activeli"),a("#carpet-collateral-tabs .tab-nav").removeClass("active"),a(this).addClass("activeli"),a(this).addClass("active"),a("#carpet-collateral-tabs .tab-content").hide();var c=(a(this).attr("href"),a(this).find("a").attr("href"));a(c).show()})},initCalcTabsMobile:function(){a("#carpet-collateral-tabs .tab-nav").click(function(b){b.preventDefault(),a("#carpet-collateral-tabs .tab-nav").removeClass("active"),a(this).addClass("active"),a("#carpet-collateral-tabs .tab-content").hide();var c=a(this).attr("href");a(c).show()})},initShowArticles:function(){a(".kbase-list .level-1").each(function(){a("> li",this).not(".show-more").not(".show-less").each(function(b,c){b>=4&&(a(this).parent().find(".show-more").removeClass("no-display"),a(this).addClass("hide"))})}),a(".kbase-list .show-more").each(function(){a(this).click(function(b){a(this).parent().addClass("show-articles")})}),a(".kbase-list .show-less").each(function(){a(this).click(function(b){a(this).parent().removeClass("show-articles")})})},initShowSearch:function(){var b=a(".form-search button"),c=a(".form-search input");a(b).each(function(b){a(this).on("click",function(b){a(this).siblings("input").width()<1?(b.preventDefault(),a(this).parents(".mini-search").addClass("show-search"),a(this).parent().find("input").focus()):0==a(this).siblings("input").val().length&&(b.preventDefault(),a(this).parents(".mini-search").removeClass("show-search"))})}),a(document).on("click",function(){a(".mini-search").removeClass("show-search")}),a(c,b).on("click",function(a){a.stopPropagation()}),a(c,b).each(function(b){a(this).on("click",function(a){a.stopPropagation()})})},initMenuFooter:function(){Blugento.isMobile()&&a(".menu-toggle-1 .footer-links ul li:first-child").click(function(b){b.preventDefault(),a(this).parent().hasClass("toggle-footer-menu")?a(this).parent().removeClass("toggle-footer-menu"):a(this).parent().addClass("toggle-footer-menu")})},initAcountHover:function(){a(".page-header .desktop .mini-account > ul").is(":hidden")&&a(".page-header .desktop .mini-account").hover(function(){a(this).find("ul").show(0)},function(){a(this).find("ul").delay(300).hide(0)})},initAcountMobile:function(){Blugento.isMobile()&&a(document).on("touchstart click",".customer-account .mobile-trigger--profile-login > a",function(a){a.preventDefault()})},initInvitationClick:function(){Blugento.isMobile()&&a(".invitation-box p").click(function(){a(".invitation-template").is(":hidden")?a(".invitation-template").fadeIn(313):a(".invitation-template").fadeOut(313)})},initMenuHover:function(){var b=a("header.page-header"),c=parseInt(b.data("nav"));12===c&&a(".nav--primary > li.parent").hover(function(){a("body").stop(!0,!0).addClass("grey-overlay")},function(){a("body").stop(!0,!0).removeClass("grey-overlay")})},initSortBy:function(){var b=a(".toolbar"),c=parseInt(b.data("sortBy"));if(3===c){var d=a(".toolbar .sort-by li.selected a").html();a(".selected-sort").html(d),a(".sort-by .selected-sort").click(function(){a(this).parent().toggleClass("show-sort-list")})}},initCart:function(){Blugento.isMobile()&&a(document).on("touchstart click",".block-cart > a",function(a){a.preventDefault()})},initEmailSpace:function(){a(".validate-email").change(function(){a(this).val(a(this).val().trim())})},initCartButtonSticky:function(){function b(a,b,c){a.addEventListener?a.addEventListener(b,c,!1):a.attachEvent&&a.attachEvent("on"+b,c)}if(Blugento.isMobile()){var c=function(a){var b=a.getBoundingClientRect(),c=b.top,d=b.height,e=a.parentNode;do{if(b=e.getBoundingClientRect(),c<=b.bottom==!1)return!1;if(c+d<=b.top)return!1;e=e.parentNode}while(e!=document.body);return c<=document.documentElement.clientHeight&&c>=0},d=function(){a("body").hasClass("checkout-cart-index")&&(c(document.getElementById("button_proceed_to_checkout"))?a("#btn-proceed-checkout-fixed").removeClass("btn-checkout-fixed"):a("#btn-proceed-checkout-fixed").addClass("btn-checkout-fixed"))};b(window,"scroll",d),b(window,"resize",d),d(),a(document).on("click","#btn-proceed-checkout-fixed",function(){a("#button_proceed_to_checkout").trigger("click")})}else if(!Blugento.isMobile()){var e=a("#cart-collaterals-section"),f=e.data("cart-collaterals-fixed");a("body").hasClass("checkout-cart-index")&&1==f&&(a(".main-content").css("position","inherit"),e.scrollToFixed({marginTop:20,limit:a("#shopping-cart-table").position().top+a("#shopping-cart-table").outerHeight(!0)-e.outerHeight(!0),zIndex:999}))}},initResponsiveIframe:function(){a("iframe").each(function(){a(this).wrap('<div class="iframe-container"></div>')})},initTopFilters:function(){a(".block-layered-nav-top .show-all").each(function(){a(this).on("click",function(){a(this).parents("li").siblings().removeClass("active"),a(this).hasClass("active")?a(this).parents("li").removeClass("active"):a(this).parents("li").addClass("active")})}),a(document).mouseup(function(b){var c=a(".block-layered-nav-top .active");c.is(b.target)||0!==c.has(b.target).length||c.removeClass("active")})},initMenuDropdown:function(){a("#nav li.parent, #nav-wrapper").each(function(){a(this).on("mouseover touchstart",function(){var b=a(this);b.prop("hoverTimeout")&&b.prop("hoverTimeout",clearTimeout(b.prop("hoverTimeout"))),b.prop("hoverIntent",setTimeout(function(){b.addClass("hover")},250))}).on("mouseleave touchend",function(){var b=a(this);b.prop("hoverIntent")&&b.prop("hoverIntent",clearTimeout(b.prop("hoverIntent"))),b.prop("hoverTimeout",setTimeout(function(){b.removeClass("hover")},250))})})},initMenuScroll:function(){Blugento.md.tablet&&a(window).on("scroll",function(){a(this).scrollTop()>150&&a("#nav li.parent, #nav-wrapper").each(function(){var b=a(this);b.hasClass("hover")&&b.removeClass("hover")})})},initSwatchSlider:function(){a(".product-images-swatches").length&&a(".configurable-swatch-list li a").each(function(){a(this).on("click",function(b){var c=a(this).attr("data-id"),d=a("#product-img-box"),e=a(".product-img-box .ajax-loader"),f=a("#media-carousel"),g=a("#media-swipe");a(f).slick("unslick"),a(g).slick("unslick"),g.css("opacity","0"),f.hide(),e.show(),a("#media-swipe li").hasClass(c)?(a(d).hide(),a(".product-img-box.cloned").remove(),a(d).clone().addClass("cloned").attr("id","product-img-box-cloned").insertAfter(".product-img-box"),a(".cloned #product-image").attr("id","product-image-cloned"),a("#product-img-box-cloned").show(),a(".cloned #media-swipe").attr("id","media-swipe-cloned"),a(".cloned #more-views").attr("id","more-views-cloned"),a(".cloned #media-carousel").attr("id","media-carousel-cloned"),a(".cloned .item").not("."+c).remove(),f=a("#media-carousel-cloned"),g=a("#media-swipe-cloned")):(a(".product-img-box.cloned").remove(),a(d).show()),setTimeout(function(){Blugento.Catalog.productView()},200)})})},initTierPriceQty:function(){a(".tier-prices").length&&a(".tier-price").each(function(){a(this).on("click",function(b){var c=parseInt(a(this).attr("data-qty"));a("#product_addtocart_form #qty").val(c)})})},initCategoryTruncate:function(){a(".description-truncated").length&&a(".category-description .description-content").each(function(b){var c=a(this).html(),d=a(this).parent().attr("data-truncated-value");c.length<=d?a(this).html(c):(a(this).parent().addClass("show-button"),a(this).html(c.slice(0,d)+"...")),a(this).parent().find(".show-more").on("click",function(b){a(this).parent().addClass("show-full-content"),a(this).parent().find(".description-content").html(c)}),a(this).parent().find(".show-less").on("click",function(b){a(this).parent().removeClass("show-full-content"),a(this).parent().find(".description-content").html(c.slice(0,d)+"...")})})},initDescriptionTruncate:function(){function b(){a("#pc-tab-description .std").each(function(b,c){var d=a(this).html(),e=a(this).parent().data("truncated-value");d.length<=parseInt(e)?a(this).html(d):(a(this).parent().addClass("toggle-description"),a(this).addClass("truncated-description").html(d.slice(0,e)+"...")),d.length>parseInt(e)?(a(this).parent().find(".show-description").on("click",function(){a("#pc-tab-description .std").removeClass("truncated-description").html(d),a("a.hide-description").show(),a(this).hide()}),a(this).parent().find(".hide-description").on("click",function(){a("#pc-tab-description .std").addClass("truncated-description").html(d.slice(0,e)+"..."),a("a.show-description").show(),a(this).hide()})):(a(this).parent().find(".show-description").hide(),a(this).parent().find(".hide-description").hide())})}a("#pc-tab-description").data("toggle")===!0&&b()},initGDPRCheckbox:function(){a(".gdpr-conditions").length>0&&a(".gdpr-conditions").each(function(){a(this).find("input").each(function(b,c){a(c).attr("id","gdpr-conditions-"+b),a(c).siblings("label").attr("for","gdpr-conditions-"+b)})})}}),Blugento.init(),a(window).on("resize",function(){function b(){Blugento.throwEvent("blugento.window.resize",a(window))}clearTimeout(Blugento.resizeId),Blugento.resizeId=setTimeout(b,200)}),Ajax.Responders.register({onCreate:function(){},onComplete:function(){setTimeout(function(){Blugento.initInputText(),a(window).lazyLoadXT()},50)}})}(jQuery);;
