/*! blugento-theme v1.0.0 - 2023-01-24 20:07:47 */
;var ProductMediaManager={swapImage:function(a){var b=a.attr("src");"undefined"!=typeof b&&setTimeout(function(){jQuery("#product-image .slick-current a source").length?(jQuery("#product-image .slick-current a img, #product-image .slick-current a source").attr("data-zoom-image",b+".webp").attr("data-image",b+".webp").attr("src",b+".webp").attr("srcset",b+".webp 1x, "+b+".webp 2x"),jQuery("#product-image .slick-current a").attr("data-mfp-src",b+".webp")):(jQuery("#product-image .slick-current a img").attr("data-zoom-image",b).attr("data-image",b).attr("src",b).attr("srcset",b+" 1x, "+b+" 2x"),jQuery("#product-image .slick-current a").attr("data-mfp-src",b))},100)},init:function(){jQuery(document).trigger("product-media-loaded",ProductMediaManager)}},Blugento=Blugento||{};Blugento.Catalog={},function(a){a.extend(Blugento.Catalog,{manufacturer:"",availability:1/0,productView:function(){function b(a,b,c){a.addEventListener?a.addEventListener(b,c,!1):a.attachEvent&&a.attachEvent("on"+b,c)}try{if(Blugento.isMobileDesign()){var c=function(a){var b=a.getBoundingClientRect(),c=b.top,d=b.height,a=a.parentNode;do{if(b=a.getBoundingClientRect(),c<=b.bottom==!1)return!1;if(c+d<=b.top)return!1;a=a.parentNode}while(a!=document.body);return c<=document.documentElement.clientHeight&&c>=0},d=function(){a("body").hasClass("catalog-product-view")&&(c(document.getElementById("product-addtocart-button"))?a("#product-addtocart-button-fixed").removeClass("btn-cart-fixed"):a("#product-addtocart-button-fixed").addClass("btn-cart-fixed"))};b(window,"scroll",d),b(window,"resize",d),d(),a("#product-addtocart-button-fixed").on("click",function(){a("#product-addtocart-button").trigger("click"),a("#product-options-wrapper").length&&a("html, body").animate({scrollTop:a("#product-options-wrapper").offset().top-50},1e3)})}else;}catch(a){}try{var e="tab-product-collateral",f=a("#"+e),g=a("#"+e+"-nav"),h=a(".tab-nav",f),i=f.data("active"),j=g.children(".tab-"+i).index(),k=f.attr("data-style"),l=0;if(g.children().each(function(){l++}),a("#tab-product-collateral-nav li").each(function(){var b=a(this).data("order");a(this).css("order",b)}),(Blugento.isMobileDesign()||3==f.data("style")&&1==f.data("orientation"))&&a("#tab-product-collateral .tabs-container span").each(function(){var b=a(this).data("order");a(this).css("order",b)}),l>=4&&1==f.data("style")&&1==f.data("orientation")&&f.addClass("exceeded-tabs-limit"),j<0&&(j=0),"3"!=k){new Yetii({id:e,active:j+1})}h.eq(j).addClass("active"),h.on("click",function(b){var c=a(this).data("rel");if(a('a[href="'+c+'"]',f).trigger("click"),a(this).parent().siblings().find("a.tab-nav").removeClass("active"),a(this).addClass("active"),Blugento.isMobileDesign()){var d=0;1===a(".page-header").data("sticky")&&(d=a("#header-sticky-content").height()),a("html, body").animate({scrollTop:a(this).offset().top-(d||5)},300)}return!1})}catch(a){}try{a("#top-reviews").on("click",function(){var b;b=a(a("#tab-product-collateral-nav").is(":visible")?'a[href="#pc-tab-reviews"]':'a[data-rel="#pc-tab-reviews"]'),0===b.length&&(b=a(".yotpo-main-widget").eq(0)),0!==b.length&&(b.trigger("click"),a("html, body").animate({scrollTop:b.offset().top},400))})}catch(a){}try{var m=a("#product-image").data("action");if(Blugento.isMobileDesign()){var n=a("#product-image").data("action-mobile");switch(n){case 1:a(".media-swipe").lightGallery({thumbnail:!0})}}else switch(m){case 9:a(".media-swipe").lightGallery({thumbnail:!0});break;case 8:a(".product-img-box").magnificPopup({delegate:".product-gallery[data-mfp-src]",type:"image",tLoading:"Loading image #%curr%...",mainClass:"mfp-img-mobile",gallery:{enabled:!0,navigateByImgClick:!0,preload:[0,1]},callbacks:{open:function(){var b="",c=a(a("#product-img-box-cloned").length?"#product-img-box-cloned":"#product-img-box"),d=c.find("a.product-gallery:not(.item-image)");if(d.length>0){b='<div class="mfp-gallery"><ul id="mfp-slider">';for(var e=0;e<d.length;e++){var f=d.eq(e).find("img").attr("src");b+="<li><button type=\"button\" onclick=\"javascript: jQuery('.product-img-box').magnificPopup('goTo', "+e+'); return false;"><img src="'+f+'" alt=""></button></li>'}b+="</ul></div>"}a(".mfp-bottom-bar").append(b)},imageLoadComplete:function(){a("#mfp-slider").hasClass("slick-initialized")||a("#mfp-slider").slick({infinite:!1,slidesToShow:4,slidesToScroll:1})}}});break;case 7:a(".media-swipe").on("init",function(){var b=a(this).find("li.item.slick-current"),c=b.find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(c).elevateZoom({zoomType:"lens",lensSize:300,scrollZoom:!0})}),a(".media-swipe").on("beforeChange",function(b,c,d,e){var f=a(c.$slides[e]).find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(f).elevateZoom({zoomType:"lens",lensSize:300,scrollZoom:!0}),a("#product-image-img").attr("id",""),a(f).attr("id","product-image-img")});break;case 6:a(".media-swipe").on("init",function(){var b=a(this).find("li.item.slick-current"),c=b.find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(c).click(function(a){a.preventDefault()}),a(c).elevateZoom()}),a(".media-swipe").on("beforeChange",function(b,c,d,e){var f=a(c.$slides[e]).find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(f).click(function(a){a.preventDefault()}),a(f).elevateZoom(),a("#product-image-img").attr("id",""),a(f).attr("id","product-image-img")});break;case 5:a(".media-swipe").on("init",function(){var b=a(this).find("li.item.slick-current"),c=b.find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(c).click(function(a){a.preventDefault()}),a(c).elevateZoom({zoomType:"inner",cursor:"crosshair"})}),a(".media-swipe").on("beforeChange",function(b,c,d,e){var f=a(c.$slides[e]).find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(f).click(function(a){a.preventDefault()}),a(f).elevateZoom({zoomType:"inner",cursor:"crosshair"}),a("#product-image-img").attr("id",""),a(f).attr("id","product-image-img")});break;case 4:a(".media-swipe").on("init",function(){var b=a(this).find("li.item.slick-current"),c=b.find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(c).elevateZoom({zoomType:"lens",lensShape:"round",lensSize:200,scrollZoom:!0})}),a(".media-swipe").on("beforeChange",function(b,c,d,e){var f=a(c.$slides[e]).find("img");a(".zoomWindowContainer, .zoomContainer").remove(),a(f).elevateZoom({zoomType:"lens",lensShape:"round",lensSize:200,scrollZoom:!0}),a("#product-image-img").attr("id",""),a(f).attr("id","product-image-img")});break;case 3:a("#product-image").magnificPopup({delegate:".product-gallery[data-mfp-src]",type:"image",tLoading:"Loading image #%curr%...",mainClass:"mfp-img-mobile",gallery:{enabled:!0,navigateByImgClick:!0,preload:[0,1]}});break;case 2:a(".media-swipe").on("init",function(){var b=a(this).find("li.item.slick-current"),c=b.find("img"),d=b.find("a");a("#product-image").on("click",d,function(){return popWin(a(c).attr("data-zoom-image"),"gallery","left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes"),!1})}),a(".media-swipe").on("beforeChange",function(b,c,d,e){var f=a(c.$slides[e]).find("img"),g=a(c.$slides[e]).find("a");a("#product-image").on("click",g,function(){return popWin(a(f).attr("data-zoom-image"),"gallery","left=0,top=0,location=no,status=yes,scrollbars=yes,resizable=yes"),!1}),a("#product-image-img").attr("id",""),a(f).attr("id","product-image-img")});break;case 1:a(".media-swipe").on("init",function(){var b=a(this).find("li.item.slick-current"),c=b.find("a");a("#product-image").on("click",c,function(a){a.preventDefault()})});break;default:a(".media-swipe").lightGallery({thumbnail:!0})}}catch(a){}try{var o=a("#media-carousel"),p=a("#media-swipe");p.on("init",function(){p.css("opacity","1"),o.show(),a(".product-img-box .discount-percentage").show(),a(".product-img-box .product-badges").show(),a(".product-img-box .ajax-loader").hide()});var o=a(".media-carousel"),p=a(".media-swipe");if(p.css("opacity","1"),o.show(),a(".product-img-box .product-badges").show(),a(".product-img-box .ajax-loader").hide(),p.slick({slidesToShow:1,slidesToScroll:1,arrows:!1,fade:!0,asNavFor:o,autoplay:!1}),o.slick({slidesToShow:o.data("gallery-images-count"),slidesToScroll:1,asNavFor:p,dots:!1,focusOnSelect:!0,infinite:!0,vertical:o.data("vertical"),responsive:[{breakpoint:768,settings:{slidesToShow:o.data("gallery-images-count-mobile")}},{breakpoint:767,settings:{dots:!a("#product-img-box-cloned").length,appendDots:p}},{breakpoint:480,settings:{vertical:o.data("mobile-vertical"),dots:!a("#product-img-box-cloned").length,appendDots:p}}]}),a(p).on("beforeChange",function(b,c,d,e){var f=a(c.$slides[e]).find("img");a("#product-image-img").attr("id",""),a(f).attr("id","product-image-img")}),!a("#product-img-box-cloned").length){var q=a("#media-swipe li.selected-image"),r=q.attr("data-slick-index");p.slick("slickGoTo",parseInt(r)),o.slick("slickGoTo",parseInt(r)),a("#media-carousel li")&&a("#media-carousel li").length>=2&&(a("#media-carousel li").each(function(b,c){a(c).removeClass("slick-current").removeClass("slick-active")}),a('#media-carousel li[data-slick-index="'+parseInt(r)+'"]').addClass("slick-current").addClass("slick-active"))}}catch(a){console.log(a)}try{var s=[];a("#media-carousel .item-image img").each(function(){""!==a(this).attr("alt")&&s.push(a(this).attr("alt"))}),a(".configurable-swatch-list li a").on("click",function(b){var c=a(this).attr("title");a.each(s,function(b,d){if(d===c){var e=a('#media-carousel .item-image img[alt="'+d+'"]').parent()[0],f=a(e).data("images");"object"==typeof f&&e.click()}})})}catch(a){}try{a(".shipping-cost-details a").magnificPopup({type:"inline"})}catch(a){}try{var t=a("#block-related [data-slider-related]").not(":hidden");t.slick({infinite:1==a("[data-slider-related]").data("slider-item-loop"),speed:parseInt(a("[data-slider-related]").data("slider-animation"))||300,slidesToShow:parseInt(a("[data-slider-related]").data("slider-item-row"))||4,slidesToScroll:parseInt(a("[data-slider-related]").data("slider-item-scroll"))||1,dots:1==a("[data-slider-related]").data("dots"),responsive:[{breakpoint:1024,settings:{slidesToShow:3,slidesToScroll:1,infinite:1==a("[data-slider-related]").data("slider-item-loop"),centerMode:1==a("[data-slider-related]").data("center"),centerPadding:1==a("[data-slider-related]").data("center")?"50px":"0"}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2,infinite:1==a("[data-slider-related]").data("slider-item-loop"),centerMode:1==a(this).data("center"),centerPadding:1==a(this).data("center")?"40px":"0"}},{breakpoint:480,settings:{slidesToShow:parseInt(a("#block-related [data-slider-related]").data("mobile-items"))||1,slidesToScroll:1,infinite:1==a("[data-slider-related]").data("slider-item-loop"),centerMode:1==a("[data-slider-related]").data("center"),centerPadding:1==a("[data-slider-related]").data("center")?"30px":"0"}}]}),a(window).load(function(){t.each(function(b,c){var d=a(this).find(".slick-track");d.find(".item-inner").css("min-height",d.height()+"px")})})}catch(a){}try{var u=a("#block-upsell [data-slider-upsell]").not(":hidden");u.slick({infinite:1==a("[data-slider-upsell]").data("slider-item-loop"),speed:parseInt(a("[data-slider-upsell]").data("slider-animation"))||300,slidesToShow:parseInt(a("[data-slider-upsell]").data("slider-item-row"))||4,slidesToScroll:parseInt(a("[data-slider-upsell]").data("slider-item-scroll"))||1,dots:1==a("[data-slider-upsell]").data("dots"),responsive:[{breakpoint:1024,settings:{slidesToShow:3,slidesToScroll:1,infinite:1==a("[data-slider-upsell]").data("slider-item-loop"),centerMode:1==a("[data-slider-upsell]").data("center"),centerPadding:1==a("[data-slider-upsell]").data("center")?"50px":"0"}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:1,infinite:1==a("[data-slider-upsell]").data("slider-item-loop"),centerMode:1==a("[data-slider-upsell]").data("center"),centerPadding:1==a("[data-slider-upsell]").data("center")?"40px":"0"}},{breakpoint:480,settings:{slidesToShow:parseInt(a("#block-upsell [data-slider-upsell]").data("mobile-items"))||1,slidesToScroll:1,infinite:1==a("[data-slider-upsell]").data("slider-item-loop"),centerMode:1==a("[data-slider-upsell]").data("center"),centerPadding:1==a("[data-slider-upsell]").data("center")?"30px":"0"}},{breakpoint:320,settings:{slidesToShow:parseInt(parseInt(a("#block-upsell [data-slider-upsell]").data("mobile-items")))||1,slidesToScroll:1,infinite:1==a("[data-slider-upsell]").data("slider-item-loop"),centerMode:1==a("[data-slider-upsell]").data("center"),centerPadding:1==a("[data-slider-upsell]").data("center")?"20px":"0"}}]}),a(window).load(function(){u.each(function(b,c){var d=a(this).find(".slick-track");d.find(".item-inner").css("min-height",d.height()+"px")})})}catch(a){}try{var v=a("#block-crosssale [data-slider]").not(":hidden"),w=v.find("li.item").length;w>4&&(v.slick({dots:!1,infinite:!0,speed:300,slidesToShow:4,slidesToScroll:1,responsive:[{breakpoint:1024,settings:{slidesToShow:3,slidesToScroll:1,infinite:!0,dots:!1}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:1,infinite:!0,dots:!1}},{breakpoint:480,settings:{slidesToShow:1,slidesToScroll:1,infinite:!0,dots:!1}},{breakpoint:320,settings:{slidesToShow:2,slidesToScroll:1,infinite:!0,dots:!1}}]}),a(window).load(function(){v.each(function(b,c){var d=a(this).find(".slick-track");d.find(".item-inner").css("min-height",d.height()+"px")})}))}catch(a){}try{a(".baseprice-box").appendTo("#product-baseprice")}catch(a){}},setProductManufacturer:function(a){this.manufacturer=a},getProductManufacturer:function(){return this.manufacturer},setProductAvailability:function(a){this.availability=a},getProductAvailability:function(){return this.availability},customRender:function(){this.getProductManufacturer().length&&isFinite(this.getProductAvailability())&&(a(".price-box, .tax-details, #product-options-wrapper, .add-to-cart, .availability").remove(),a('<div class="custom-availability-message">'+Translator.translate("This product is no longer available")+"</div>").insertAfter(".product-sku"))},productList:function(){try{var b=a("#category-banner-group");b&&b.insertBefore(".page-main .page-container"),a(".minimize-filters").length&&a(".minimize-filters").each(function(b){var c=a(this).find("li").length;c>6&&a(this).addClass("active-minimize-filters")})}catch(a){console.log(a)}Blugento.flex(".product-shop-row","td",".product-info"),Blugento.flex(".products-grid","> li",".short-info"),Blugento.flex(".products-grid","> li",".product-info"),Blugento.flex(".products-grid","> li",".yotpo"),Blugento.flex(".products-grid","> li",".desc"),Blugento.flex(".products-grid","> li",".configurable-swatch-list"),Blugento.flex(".products-grid","> li",".qty-wrapper"),Blugento.flex(".products-grid","> li",".content-blog-box"),Blugento.flex(".slick-track","> .slick-slide",".short-info"),Blugento.flex(".slick-track","> .slick-slide",".product-info"),Blugento.flex(".slick-track","> .slick-slide",".configurable-swatch-list"),Blugento.flex(".slick-track","> .slick-slide",".qty-wrapper"),Blugento.flex(".slick-track","> .slick-slide",".desc"),Blugento.flex(".slick-track","> .slick-slide",".yotpo"),Blugento.flex(".slick-track","> .slick-slide",".content-blog-box"),Blugento.flex(".blog-wrap",".postWrapper")}}),a(document).ready(function(){ProductMediaManager.init(),Blugento.Catalog.productView(),Blugento.Catalog.productList(),Blugento.Catalog.customRender()}),a(document).ajaxSuccess(function(){Blugento.Catalog.productList()}),a(window).on("blugento.window.resize",function(){Blugento.Catalog.productList()}),a(window).load(function(){Blugento.Catalog.productList()})}(jQuery);;
