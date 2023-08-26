var Blugento = Blugento || {};

Blugento.Modal = {

    trigger: null,
    element: null,
    body: null,
    footer: null,

    initialize: function()
    {
        var _this = this;

        this.trigger = $('ajaxcart-modal-trigger');
        this.element = $('ajaxcart-modal');
        this.body = $('ajaxcart-modal-body');
        this.footer = $('ajaxcart-modal-footer');

        this.element.on('click', '.ajaxcart-modal-close', function(event, element) {
            _this.hide();
        });

        return this;
    },

    hide: function()
    {
        this.trigger.checked = false;

        return this;
    },

    show: function(showFooter)
    {
        this.trigger.checked = true;

        if (showFooter) {
            this.footer.show();
        } else {
            this.footer.hide();
        }

        if (jQuery('.ajaxcart-modal .products-grid').length) {
            jQuery('.ajaxcart-modal-box').addClass('ajaxcart-modal-products');
        }
        
        jQuery('.ajaxcart-modal .products-grid').each(function(e, i) {
            jQuery(this).slick({
                infinite: jQuery(this).data('slider-item-loop') == 1 ? true : false,
                speed: parseInt(jQuery(this).data('slider-animation')) || 300,
                slidesToShow: 3,
                slidesToScroll: parseInt(jQuery(this).data('slider-item-scroll')) || 1,
                dots: jQuery(this).data('dots') == 1 ? true : false,
                  autoplay: jQuery(this).data('slider-item-autoplay') == 1 ? true : false,
                  cssEase: jQuery(this).data('slider-item-cssease'),
                responsive: [
                    {
                        breakpoint: 1170,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 980,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            centerMode: jQuery(this).data('center') == 1 ? true : false,
                            centerPadding: jQuery(this).data('center') == 1 ? '50px' : '0',
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            centerMode: jQuery(this).data('center') == 1 ? true : false,
                            centerPadding: jQuery(this).data('center') == 1 ? '40px' : '0',
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: parseInt(jQuery(this).data('mobile-items')) || 1,
                            slidesToScroll: 1,
                            centerMode: jQuery(this).data('center') == 1 ? true : false,
                            centerPadding: jQuery(this).data('center') == 1 ? '30px' : '0',
                        }
                    }
                ]
            });
        });

        return this;
    },

    setBody: function(html)
    {
        this.body.update(html);

        return this;
    }
};

Blugento.AjaxCart = {

    modal: null,

    initialize: function()
    {
        this.modal = Blugento.Modal.initialize();
        this.bindEvents();
    },

    bindEvents: function()
    {
        this.addProductEvent();
        this.deleteProductEvent();
        this.updateEvent();
        this.couponEvent();
    },

    showProgress: function()
    {
        $('ajaxcart-overlay').addClassName('visible');
    },

    hideProgress: function()
    {
        $('ajaxcart-overlay').removeClassName('visible');
    },

    showSuccess: function(message)
    {
        this.hideProgress();

        var messagesList = new Element('ul', {
            class: 'ajaxcart-modal-messages'
        });

        if (typeof message == 'string') {
            messagesList.insert('<li>' + message + '</li>');
        }

        if (!messagesList.empty()) {
            this.modal.setBody(messagesList).show(true);
        }
    },

    showError: function(message)
    {
        this.hideProgress();

        var messagesList = new Element('ul', {
            class: 'ajaxcart-modal-messages'
        });

        if (typeof message == 'string') {
            messagesList.insert('<li>' + message + '</li>');
        } else if(message == message) {
            messagesList.insert('<li>' + message + '</li>');
        } else {
            message.each(function() {
                if (typeof this == 'string') {
                    messagesList.insert('<li>' + this + '</li>');
                }
            });
        }

        if (!messagesList.empty()) {
            this.modal.setBody(messagesList).show();
        }
    },

    ajaxCartSubmit: function(obj, empty)
    {
        var _this = this;

        this.hideProgress();
        this.modal.hide();

        try {
            if (typeof obj == 'string') {
                var url = obj;

                new Ajax.Request(url, {
                    onCreate: function() {
                        _this.showProgress();
                    },
                    onSuccess: function(response) {
                        try {
                            var res = response.responseText.evalJSON();

                            if (res) {
                                // check for group product's option
                                if (res.configurable_options_block) {
                                    if (res.r == 'success') {
                                        // show group product options block
                                        _this.showPopup(res.configurable_options_block);
                                    } else {
                                        if (typeof res.messages != 'undefined') {
                                            _this.showError(res.messages);
                                        } else {
                                            console.log('Error log #0003', res);
                                            // _this.showError('Something bad happened (Error log #0003).');
                                        }
                                    }
                                } else {
                                    if (res.r == 'success') {
                                        if (res.message) {
                                            // Get data attr
                                            var dataImage = jQuery('#ajaxcart-modal').attr('data-image');

                                            if(dataImage == '1') {
                                                if (typeof res.name === 'undefined') {
                                                    _this.showSuccess(res.message);
                                                } else {
                                                    _this.showSuccess(
                                                        '<div class="top-content-ajax">' +
                                                        '<h2>' + Translator.translate('The product has been added to your shopping cart') + '</h2>' +
                                                        '<figure><img title="' + res.name + '" alt="' + res.name + '" src="' + res.image + '" /></figure>' +
                                                        '<h3>' + res.name + '</h3>' +
                                                        '<p class="price">' + res.price + '</p>' +
                                                        '</div>'
                                                    );
                                                }
                                            } else {
                                                _this.showSuccess(res.message);
                                            }
                                        } else {
                                            _this.hideProgress();
                                        }

                                        _this.updateBlocks(res.update_blocks);

                                        //  Show dropdown cart in modal
                                        var dataModal = jQuery('.block-cart-aside').attr('data-modal');

                                        if(dataModal == '1') {
                                            jQuery('.block-cart > a').removeAttr('data-dock');
                                            var body = jQuery('body');
                                            var cartBlock = jQuery('.block-cart-aside');

                                            if(Blugento.isMobileDesign()) {
                                                var cartWidth = jQuery(window).width();
                                                cartBlock.width(cartWidth);
                                            } else {
                                                var cartWidth = '420';
                                                cartBlock.width(cartWidth);
                                            }

                                            body.addClass('cart-modal-open');

                                            function openCart() {
                                                if (jQuery(this).hasClass('cart-active')) {
                                                    body.animate({
                                                        left: "0"
                                                    }, function () {
                                                        body.removeClass('cart-modal-open');
                                                    });
                                                    cartBlock.animate({
                                                        right: -cartWidth
                                                    });
                                                    jQuery(this).removeClass('cart-active');
                                                } else {
                                                    body.animate({
                                                        left: -cartWidth
                                                    });
                                                    cartBlock.animate({
                                                        right: "0"
                                                    });
                                                    jQuery(this).addClass('cart-active');
                                                }
                                            }

                                            openCart();

                                            jQuery('.block-cart > a').each(function (e) {
                                                jQuery(this).on('touchstart click', function (e) {
                                                    e.preventDefault();
                                                    body.addClass('cart-modal-open');
                                                    openCart();
                                                });
                                            });

                                            jQuery('.overlay-modal, .close-modal, .btn-close').on('click', function (e) {
                                                e.preventDefault();
                                                body.animate({
                                                    left: "0"
                                                }, function () {
                                                    body.removeClass('cart-modal-open');
                                                });
                                                cartBlock.animate({
                                                    right: -cartWidth
                                                });
                                                jQuery('.block-cart > a').removeClass('cart-active');
                                            });
                                        }

                                        // Push data variables to GTM
                                        var productAdd    = res.product_quote_add_item;
                                        var productRemove = res.product_quote_remove_item;

                                        if (productAdd) {
                                            window.dataLayer = window.dataLayer || [];
                                            dataLayer.push({
                                                'event': 'addToCart',
                                                'ecommerce': {
                                                    'currencyCode': res.currency_code,
                                                    'add': {
                                                        'products': [productAdd]
                                                    }
                                                }
                                            });
                                        }
    
                                        if (productRemove) {
                                            window.dataLayer = window.dataLayer || [];
                                            dataLayer.push({
                                                'event': 'removeFromCart',
                                                'ecommerce': {
                                                    'remove': {
                                                        'products': [productRemove]
                                                    }
                                                }
                                            });
                                        }

                                        // Push data variables to TTQ
                                        var ttqProductAdd = res.ttq_product_quote_add_item;

                                        if (ttqProductAdd) {
                                            window.ttq = window.ttq || [];

                                            if (ttq && ttq.length) {
                                                ttq.track('AddToCart', {
                                                    'contents': [ttqProductAdd],
                                                    'value': ttqProductAdd.quantity * ttqProductAdd.price,
                                                    'currency': res.currency_code,
                                                });
                                            }
                                        }

                                        // Push data variables to Gtag
                                        var gtagProductAdd = res.gtag_product_quote_add_item;

                                        if (gtagProductAdd) {
                                            gtag('event', 'add_to_cart', { ...gtagProductAdd });
                                        }

                                        var gtagProductRemove = res.gtag_product_quote_remove_item;

                                        if (gtagProductRemove) {
                                            gtag('event', 'remove_from_cart', { ...gtagProductRemove });
                                        }

                                        // Push dynamic remarketing variables
                                        var remarktingProduct = res.remarketing_product_quote_item;

                                        if (remarktingProduct) {
                                            gtag('event', 'add_to_cart', {
                                                'send_to': res.dynamic_marketing_account_id,
                                                'user_id': res.customer_id,
                                                'value': remarktingProduct.qty * remarktingProduct.price,
                                                'items': [
                                                    {
                                                        'id': remarktingProduct.id,
                                                        'google_business_vertical': 'retail',
                                                    },
                                                    {
                                                        'id': remarktingProduct.id,
                                                        'google_business_vertical': 'custom',
                                                    }
                                                ],
                                            })
                                        }

                                        var fbqProduct = res.fbq_product_quote_item;

                                        if (fbqProduct) {
                                            fbq('track', 'AddToCart', {
                                                'content_type': 'product',
                                                'content_ids': fbqProduct.content_ids,
                                                'value': fbqProduct.value,
                                                'currency': fbqProduct.currency,
                                                'external_id': fbqProduct.external_id
                                            });
                                        }
                                    } else {
                                        if (typeof res.messages != 'undefined') {
                                            var dataModal = jQuery('.block-cart-aside').attr('data-modal');
    
                                            if(dataModal == '1') {
                                                var body = jQuery('body');
                                                body.addClass('ajax-cart-error');
                                            }
                                            
                                            _this.showError(res.messages);
                                        } else {
                                            console.log('Error log #0004', res);
                                            // _this.showError('Something bad happened (Error log #0004).');
                                        }
                                    }
                                }

                                var cartQty = jQuery('.block-cart em').text();

                                if (cartQty === '') {
                                    jQuery('.ajaxcart-modal-footer .btn-checkout').hide();
                                } else {
                                    jQuery('.ajaxcart-modal-footer .btn-checkout').show();
                                }
                            } else {
                                document.location.reload(true);
                            }
                        }
                        catch(e) {
                            console.log(e);
                        }
                    }
                });
            } else {
                if ((typeof obj.form != 'undefined') && (typeof obj.form.down('input[type=file]') != 'undefined')) {
                    // use iframe

                    var form = obj.form;

                    form.insert('<iframe id="upload_target" name="upload_target" src="" style="width: 0; height: 0; border: 0;"></iframe>');

                    var iframe = $('upload_target');

                    iframe.observe('load', function() {
                        try {
                            var doc = iframe.contentDocument ? iframe.contentDocument : (iframe.contentWindow.document || iframe.document),
                                res = doc.body.innerText ? doc.body.innerText : doc.body.textContent;

                            res = res.evalJSON();

                            if (res) {
                                if (res.r == 'success') {
                                    if (res.message) {
                                        // Get data attr
                                        var dataImage = jQuery('#ajaxcart-modal').attr('data-image');
                                        
                                        if(dataImage == '1') {
                                            if (typeof res.name === 'undefined') {
                                                _this.showSuccess(res.message);
                                            } else {
                                                _this.showSuccess(
                                                  '<div class="top-content-ajax">' +
                                                  '<h2>' + Translator.translate('The product has been added to your shopping cart') + '</h2>' +
                                                  '<figure><img title="' + res.name + '" alt="' + res.name + '" src="' + res.image + '" /></figure>' +
                                                  '<h3>' + res.name + '</h3>' +
                                                  '<p class="price">' + res.price + '</p>' +
                                                  '</div>'
                                                );
                                            }
                                        } else {
                                            _this.showSuccess(res.message);
                                        }
                                    } else {
                                        _this.hideProgress();
                                    }

                                    _this.updateBlocks(res.update_blocks);

                                    //  Show dropdown cart in modal
                                    var dataModal = jQuery('.block-cart-aside').attr('data-modal');
    
                                    if(dataModal == '1') {
                                        jQuery('.block-cart > a').removeAttr('data-dock');
                                        var body = jQuery('body');
                                        var cartBlock = jQuery('.block-cart-aside');
        
                                        if (Blugento.isMobileDesign()) {
                                            var cartWidth = jQuery(window).width();
                                            cartBlock.width(cartWidth);
                                        } else {
                                            var cartWidth = '420';
                                            cartBlock.width(cartWidth);
                                        }

                                        body.addClass('cart-modal-open');
        
                                        function openCart() {
                                            if (jQuery(this).hasClass('cart-active')) {
                                                body.animate({
                                                    left: "0"
                                                }, function () {
                                                    body.removeClass('cart-modal-open');
                                                });
                                                cartBlock.animate({
                                                    right: -cartWidth
                                                });
                                                jQuery(this).removeClass('cart-active');
                                            } else {
                                                body.animate({
                                                    left: -cartWidth
                                                });
                                                cartBlock.animate({
                                                    right: "0"
                                                });
                                                jQuery(this).addClass('cart-active');
                                            }
                                        }
        
                                        openCart();
        
                                        jQuery('.block-cart > a').each(function (e) {
                                            jQuery(this).on('touchstart click', function (e) {
                                                e.preventDefault();
                                                body.addClass('cart-modal-open');
                                                openCart();
                                            });
                                        });
        
                                        jQuery('.overlay-modal, .close-modal, .btn-close').on('click', function (e) {
                                            e.preventDefault();
                                            body.animate({
                                                left: "0"
                                            }, function () {
                                                body.removeClass('cart-modal-open');
                                            });
                                            cartBlock.animate({
                                                right: -cartWidth
                                            });
                                            jQuery('.block-cart > a').removeClass('cart-active');
                                        });
                                    }

                                    // Push data variables to GTM
                                    var productAdd = res.product_quote_add_item;
    
                                    if (productAdd) {
                                        window.dataLayer = window.dataLayer || [];
                                        dataLayer.push({
                                            'event': 'addToCart',
                                            'ecommerce': {
                                                'currencyCode': res.currency_code,
                                                'add': {
                                                    'products': [productAdd]
                                                }
                                            }
                                        });
                                    }

                                    // Push data variables to TTQ
                                    var ttqProductAdd = res.ttq_product_quote_add_item;

                                    if (ttqProductAdd) {
                                        window.ttq = window.ttq || [];

                                        if (ttq && ttq.length) {
                                            ttq.track('AddToCart', {
                                                'contents': [ttqProductAdd],
                                                'value': ttqProductAdd.quantity * ttqProductAdd.price,
                                                'currency': res.currency_code,
                                            });
                                        }
                                    }

                                    // Push data variables to Gtag
                                    var gtagProductAdd = res.gtag_product_quote_add_item;

                                    if (gtagProductAdd) {
                                        gtag('event', 'add_to_cart', { ...gtagProductAdd });
                                    }

                                    var gtagProductRemove = res.gtag_product_quote_remove_item;

                                    if (gtagProductRemove) {
                                        gtag('event', 'remove_from_cart', { ...gtagProductRemove });
                                    }

                                    // Push dynamic remarketing variables
                                    var remarktingProduct = res.remarketing_product_quote_item;

                                    if (remarktingProduct) {
                                        gtag('event', 'add_to_cart', {
                                            'send_to': res.dynamic_marketing_account_id,
                                            'user_id': res.customer_id,
                                            'value': remarktingProduct.qty * remarktingProduct.price,
                                            'items': [
                                                {
                                                    'id': remarktingProduct.id,
                                                    'google_business_vertical': 'retail',
                                                },
                                                {
                                                    'id': remarktingProduct.id,
                                                    'google_business_vertical': 'custom',
                                                }
                                            ],
                                        })
                                    }

                                    var fbqProduct = res.fbq_product_quote_item;

                                    if (fbqProduct) {
                                        fbq('track', 'AddToCart', {
                                            'content_type': 'product',
                                            'content_ids': fbqProduct.content_ids,
                                            'value': fbqProduct.value,
                                            'currency': fbqProduct.currency,
                                            'external_id': fbqProduct.external_id
                                        });
                                    }
                                } else {
                                    if (typeof res.messages != 'undefined') {
                                        var dataModal = jQuery('.block-cart-aside').attr('data-modal');
    
                                        if(dataModal == '1') {
                                            var body = jQuery('body');
                                            body.addClass('ajax-cart-error');
                                        }
                                        
                                        _this.showError(res.messages);
                                    } else {
                                        console.log('Error log #0001', res);
                                        // _this.showError('Something bad happened (Error log #0005).');
                                    }
                                }
                            } else {
                                console.log('Error log #0001', res);
                                // _this.showError('Something bad happened (Error log #0006).');
                            }
                        }
                        catch(e) {
                            console.log(e);
                        }
                    });

                    form.target = 'upload_target';

                    _this.showProgress();

                    form.submit();

                    return true;
                } else {
                    // use ajax

                    var form = obj.form || obj,
                        url = form.action,
                        data = form.serialize(true);

                    if (location.protocol === 'https:') {
                        // page is secure
                        url = url.replace('http://', 'https://');
                    }

                    data['update_cart_action'] = (empty ? 'empty_cart' : 'update_qty');

                    data = $H(data).toQueryString();

                    new Ajax.Request(url, {
                        method: 'post',
                        postBody: data,
                        onCreate: function() {
                            _this.showProgress();
                        },
                        onSuccess: function(response) {
                            try {
                                var res = response.responseText.evalJSON();

                                if (res) {
                                    if (res.r == 'success') {
                                        if (res.message) {
                                            // Get data attr
                                            var dataImage = jQuery('#ajaxcart-modal').attr('data-image');
                                            
                                            if(dataImage == '1') {
                                                if (typeof res.name === 'undefined') {
                                                    var url = window.location.href;
                                                    var cartUrl = jQuery('.mini-cart a.view-cart').attr('href');
                                                    var itemUpdate = res.update_blocks.filter(function(item) {
                                                        return item.key === ".cart";
                                                    });

                                                    // Todo: add a better way for rendering redirect or update correct id product
                                                    if (itemUpdate[0].value.includes(url)) {
                                                        _this.showSuccess(res.message);
                                                    } else {
                                                        window.location.href = cartUrl;
                                                    }
                                                } else {
                                                    _this.showSuccess(
                                                        '<div class="top-content-ajax">' +
                                                        '<h2>' + Translator.translate('The product has been added to your shopping cart') + '</h2>' +
                                                        '<figure><img title="' + res.name + '" alt="' + res.name + '" src="' + res.image + '" /></figure>' +
                                                        '<h3>' + res.name + '</h3>' +
                                                        '<p class="price">' + res.price + '</p>' +
                                                        '</div>'
                                                    );
                                                }
                                            } else {
                                                _this.showSuccess(res.message);
                                            }
                                        } else {
                                            _this.hideProgress();
                                        }

                                        _this.updateBlocks(res.update_blocks);
                                        if (res.min_order_message) {
                                            _this.showError(res.min_order_message);
                                        }

                                        // Hide duplicate info message from cart page
                                        if (jQuery('.shopping-cart-item-message').length > 1) {
                                            jQuery('.product-name + .shopping-cart-item-message').remove();
                                        }

                                        var dataModal = jQuery('.block-cart-aside').attr('data-modal');

                                        if(dataModal == '1') {
                                            jQuery('.block-cart > a').removeAttr('data-dock');
                                            var body = jQuery('body');
                                            var cartBlock = jQuery('.block-cart-aside');

                                            if (Blugento.isMobileDesign()) {
                                                var cartWidth = jQuery(window).width();
                                                cartBlock.width(cartWidth);
                                            } else {
                                                var cartWidth = '420';
                                                cartBlock.width(cartWidth);
                                            }

                                            body.addClass('cart-modal-open');

                                            function openCart() {
                                                if (jQuery(this).hasClass('cart-active')) {
                                                    body.animate({
                                                        left: "0"
                                                    }, function () {
                                                        body.removeClass('cart-modal-open');
                                                    });
                                                    cartBlock.animate({
                                                        right: -cartWidth
                                                    });
                                                    jQuery(this).removeClass('cart-active');
                                                } else {
                                                    body.animate({
                                                        left: -cartWidth
                                                    });
                                                    cartBlock.animate({
                                                        right: "0"
                                                    });
                                                    jQuery(this).addClass('cart-active');
                                                }
                                            }

                                            openCart();

                                            jQuery('.block-cart > a').each(function (e) {
                                                jQuery(this).on('touchstart click', function (e) {
                                                    e.preventDefault();
                                                    body.addClass('cart-modal-open');
                                                    openCart();
                                                });
                                            });

                                            jQuery('.overlay-modal, .close-modal, .btn-close').on('click', function (e) {
                                                e.preventDefault();
                                                body.animate({
                                                    left: "0"
                                                }, function () {
                                                    body.removeClass('cart-modal-open');
                                                });
                                                cartBlock.animate({
                                                    right: -cartWidth
                                                });
                                                jQuery('.block-cart > a').removeClass('cart-active');
                                            });
                                        }

                                        // Push data variables to GTM
                                        var productAdd = res.product_quote_add_item;
    
                                        if (productAdd) {
                                            window.dataLayer = window.dataLayer || [];
                                            dataLayer.push({
                                                'event': 'addToCart',
                                                'ecommerce': {
                                                    'currencyCode': res.currency_code,
                                                    'add': {
                                                        'products': [productAdd]
                                                    }
                                                }
                                            });
                                        }

                                        // Push data variables to TTQ
                                        var ttqProductAdd = res.ttq_product_quote_add_item;

                                        if (ttqProductAdd) {
                                            window.ttq = window.ttq || [];

                                            if (ttq && ttq.length) {
                                                ttq.track('AddToCart', {
                                                    'contents': [ttqProductAdd],
                                                    'value': ttqProductAdd.quantity * ttqProductAdd.price,
                                                    'currency': res.currency_code,
                                                });
                                            }
                                        }

                                        // Push data variables to Gtag
                                        var gtagProductAdd = res.gtag_product_quote_add_item;

                                        if (gtagProductAdd) {
                                            gtag('event', 'add_to_cart', { ...gtagProductAdd });
                                        }

                                        var gtagProductRemove = res.gtag_product_quote_remove_item;

                                        if (gtagProductRemove) {
                                            gtag('event', 'remove_from_cart', { ...gtagProductRemove });
                                        }

                                        // Push dynamic remarketing variables
                                        var remarktingProduct = res.remarketing_product_quote_item;

                                        if (remarktingProduct) {
                                            gtag('event', 'add_to_cart', {
                                                'send_to': res.dynamic_marketing_account_id,
                                                'user_id': res.customer_id,
                                                'value': remarktingProduct.qty * remarktingProduct.price,
                                                'items': [
                                                    {
                                                        'id': remarktingProduct.id,
                                                        'google_business_vertical': 'retail',
                                                    },
                                                    {
                                                        'id': remarktingProduct.id,
                                                        'google_business_vertical': 'custom',
                                                    }
                                                ],
                                            })
                                        }

                                        var fbqProduct = res.fbq_product_quote_item;

                                        if (fbqProduct) {
                                            fbq('track', 'AddToCart', {
                                                'content_type': 'product',
                                                'content_ids': fbqProduct.content_ids,
                                                'value': fbqProduct.value,
                                                'currency': fbqProduct.currency,
                                                'external_id': fbqProduct.external_id
                                            });
                                        }
                                    } else {
                                        if (typeof res.messages != 'undefined') {
                                            var dataModal = jQuery('.block-cart-aside').attr('data-modal');
    
                                            if(dataModal == '1') {
                                                var body = jQuery('body');
                                                body.addClass('ajax-cart-error');
                                            }
                                            _this.showError(res.messages);
                                        } else {
                                            console.log('Error log #0001', res);
                                            // _this.showError('Something bad happened (Error log #0007).');
                                        }
                                    }
                                } else {
                                    console.log('Error log #0001', res);
                                    // _this.showError('Something bad happened (Error log #0008).');
                                }
                            }
                            catch(e) {
                                console.log(e);
                            }
                        }
                    });
                }
            }
        }
        catch(e) {
            console.log(e);
            if (typeof obj == 'string') {
                window.location.href = obj;
            } else {
                document.location.reload(true);
            }
        }
    },

    getConfigurableOptions: function(url)
    {
        var _this = this;

        this.hideProgress();

        new Ajax.Request(url, {
            onCreate: function() {
                _this.showProgress();
            },
            onSuccess: function(response) {
                try {
                    var res = response.responseText.evalJSON();

                    if (res) {
                        if (res.r == 'success') {
                            // show configurable options popup
                            _this.showPopup(res.configurable_options_block);
                        } else {
                            if (typeof res.messages != 'undefined') {
                                _this.showError(res.messages);
                            } else {
                                console.log('Error log #0001', res);
                                // _this.showError('Something bad happened (Error log #0001).');
                            }
                        }
                    } else {
                        document.location.reload(true);
                    }
                }
                catch(e) {
                    console.log(e);
                    window.location.href = url;
                }
            }
        });
    },

    addProductEvent: function()
    {
        var _this = this;

        if (typeof productAddToCartForm != 'undefined') {
            productAddToCartForm.submit = function(url) {
                var form = this;

                if (this.form.id === 'product_addtocart_form_from_popup') {
                    form = productAddToCartFormOld;
                }

                if (this.validator && this.validator.validate()) {
                    _this.ajaxCartSubmit(form);
                }

                return false;
            };

            productAddToCartForm.form.onsubmit = function() {
                productAddToCartForm.submit();
                return false;
            };
        }
    },

    deleteProductEvent: function()
    {
        $$('a[href*="/checkout/cart/delete/"]').each(function(e) {
            $(e).observe('click', function(event) {
                if ($(e).dataset.confirmdelete == 1) {
                    if (confirm(Translator.translate('Are you sure you would like to remove this item from the shopping cart?').stripTags())) {
                        setLocation($(e).readAttribute('href'));
                        Event.stop(event);
                    } else {
                        Event.stop(event);
                        return false;
                    }
                } else {
                    setLocation($(e).readAttribute('href'));
                    Event.stop(event);
                }
            });
        });
    },

    updateEvent: function()
    {
        var _this = this,
            form = $$('form[action*="/checkout/cart/updatePost/"]')[0];

        if (typeof form != 'undefined') {
            form.observe('submit', function(event) {
                _this.ajaxCartSubmit(form);
                event.preventDefault();
            });
        }
    },
    
    couponEvent: function()
    {
        var _this = this,
            form = $$('form[action*="/checkout/cart/couponPost/"]')[0];
        var deleteCouponButton = $('delete_coupon_button');
        if (deleteCouponButton) {
            Event.observe(deleteCouponButton, 'click', function(event) {
                _this.ajaxCouponSubmit(form, 1);
                event.preventDefault();
            });
        }
        if (typeof form != 'undefined') {
            form.observe('submit', function(event) {
                _this.ajaxCouponSubmit(form);
                event.preventDefault();
            });
        }
    },
    
    ajaxCouponSubmit: function (obj, remove) {
        var _this = this;
        this.hideProgress();
        this.modal.hide();
        try {
            var form = obj.form || obj,
                url  = form.action,
                data = form.serialize(true);
            if (location.protocol === 'https:') {
                url = url.replace('http://', 'https://');
            }
            data['remove'] = (remove ? '1' : '0');
            data = $H(data).toQueryString();
            new Ajax.Request(url, {
                method: 'post',
                postBody: data,
                onCreate: function () {
                    _this.showProgress();
                },
                onSuccess: function (response) {
                    try {
                        var res = response.responseText.evalJSON();
                        var body = jQuery('body');
                        
                        if (res) {
                            if (res.r == 'success') {
                                _this.showCouponSuccess(res.message);
                                body.addClass('coupon-ajax');
                                if (remove == 1) {
                                    setTimeout(function () {
                                        body.removeClass('coupon-ajax');
                                    }, 2000);
                                }
                            } else {
                                _this.showCouponError(res.messages);
                            }
                            _this.updateBlocks(res.update_blocks);
                        } else {
                            console.log('Error log #0002', res);
                            // _this.showCouponError('Something bad happened (Error log #0002).');
                        }
                    } catch (e) {
                        console.log(e);
                    }
                }
            });
        } catch (e) {
            console.log(e);
        }
    },
    
    showCouponSuccess: function (message) {
        this.hideProgress();
        var messagesList = new Element('div', {
            class: 'ajaxcart-modal-messages coupon-success'
        });
        if (typeof message == 'string') {
            messagesList.insert('<span>' + message + '</span>');
        }
        if (!messagesList.empty()) {
            this.modal.setBody(messagesList).show();
        }
    },
    
    showCouponError: function (message) {
        this.hideProgress();
        var messagesList = new Element('div', {
            class: 'ajaxcart-modal-messages coupon-error'
        });
        if (typeof message == 'string') {
            messagesList.insert('<span>' + message + '</span>');
        } else {
            message.each(function () {
                if (typeof this == 'string') {
                    messagesList.insert('<span>' + this + '</span>');
                }
            });
        }
        if (!messagesList.empty()) {
            this.modal.setBody(messagesList).show();
        }
    },

    updateBlocks: function(blocks)
    {
        var _this = this;

        if (blocks) {
            try {
                blocks.each(function(block) {
                    if (block.key) {
                        var dom_selector = block.key;
                        if ($$(dom_selector)) {
                            $$(dom_selector).each(function(e) {
                                $(e).replace(block.value);
                            });
                        }
                    }
                });
                _this.bindEvents();

                // show details tooltip
                truncateOptions();
            }
            catch(e) {
                // console.log(e);
            }
        }
    },

    showPopup: function(block)
    {
        try {
            this.hideProgress();

            this.modal.setBody(block).show();

            this.extractScripts(block);
            this.bindEvents();
        }
        catch(e) {
            // console.log(e);
        }
    },

    extractScripts: function(strings)
    {
        var scripts = strings.extractScripts();

        scripts.each(function(script) {
            try {
                eval(script.replace(/var /gi, ""));
            }
            catch(e) {
                // console.log(e);
            }
        });
    }
};

var oldSetLocation = setLocation;
var setLocation = (function() {
    return function(url) {
        if (url.search('checkout/cart/add') != -1) {
            // simple/group/downloadable product
            Blugento.AjaxCart.ajaxCartSubmit(url);
        } else if (url.search('checkout/cart/delete') != -1) {
            Blugento.AjaxCart.ajaxCartSubmit(url);
        } else if (url.search('options=cart') != -1) {
            // configurable/bundle product
            url += '&ajax=true';
            Blugento.AjaxCart.getConfigurableOptions(url);
        } else {
            oldSetLocation(url);
        }
    };
})();

setPLocation = setLocation;

document.observe('dom:loaded', function() {
    Blugento.AjaxCart.initialize();
});
