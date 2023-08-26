jQuery(function($) {
    $('.link-logout').clone().appendTo('.nav-bar');
    $(".skip-nav").click(function() {
        $('.mm-slideout-overlay').hide();
        $("#nav-box").mmenu({
            "offCanvas": {
                "position": "left",
                "zposition": "front",
            },
        });
        var mmenu = $("#nav-box").data( "mmenu" );

        mmenu.open();

        $('.close-btn').remove();
        $('body').prepend('<span class="close-menu close-btn">x</span>');
    });

    $("div.side-col h3:not(.head-categories)").click(function() {
        $('.mm-slideout-overlay').hide();
        $("#system_config_tabs, #sales_order_view_tabs, #product_info_tabs, #page_tabs, #blog_tabs, #designcustomiser_tabs, #promo_catalog_edit_tabs, #customer_info_tabs").mmenu({
            "offCanvas": {
                "position": "left",
                "zposition": "front",
            },
        });
        var mmenu = $("#system_config_tabs, #sales_order_view_tabs, #product_info_tabs, #page_tabs, #blog_tabs, #designcustomiser_tabs, #promo_catalog_edit_tabs, #customer_info_tabs").data( "mmenu" );

        mmenu.open();

        $('.close-btn').remove();
        $('body').prepend('<span class="close-menu close-btn">x</span>');
    });

    $("#product_info_tabs li a, #page_tabs li a, #blog_tabs li a, #designcustomiser_tabs li a, #promo_catalog_edit_tabs li a, #customer_info_tabs li a").click(function() {
        var mmenu = $("#product_info_tabs, #page_tabs, #blog_tabs, #designcustomiser_tabs, #promo_catalog_edit_tabs, #customer_info_tabs").data( "mmenu" );

        mmenu.close();
    });
});