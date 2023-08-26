var Blugento = Blugento || {};

Blugento.Checkout = {};

(function($) {
    $.extend(Blugento.Checkout, {
        setBillingAddress: function(element, id)
        {
            try {
                $('#billing-address-select option[value="' + id + '"]').prop('selected', true);
                $(element).addClass('active').siblings().removeClass('active');
                $('#billing-new-address-form').hide();
                $('#wrap-addNewBillingAddress').show();
            }
            catch(e) {}
        },
        setShippingAddress: function(element, id)
        {
            try {
                $('#shipping-address-select option[value="' + id + '"]').prop('selected', true);
                $('#shipping-address-select').trigger('change');
                $(element).addClass('active').siblings().removeClass('active');
                $('#shipping-new-address-form').hide();
                $('#wrap-addNewShippingAddress').show();
            }
            catch(e) {}
        },
        addNewBillingAddress: function()
        {
            try {
                $('#listAddressBilling').children().removeClass('active');
                $('#billing-address-select').val('');
                $('#billing-new-address-form').show();
                $('#wrap-addNewBillingAddress').hide();
            }
            catch(e) {}
        },
        addNewShippingAddress: function()
        {
            try {
                $('#listAddressShipping').children().removeClass('active');
                $('#shipping-address-select').val('');
                $('#shipping-new-address-form').show();
                $('#wrap-addNewShippingAddress').hide();
            }
            catch(e) {}
        },
        initCards: function()
        {
            Blugento.flex('.list--address', '> li');

            try {
                var shippingAddressValue = $('#shipping-address-select').val(),
                    billingAddressValue = $('#billing-address-select').val();

                $('#blugento-shipping-address-' + shippingAddressValue).addClass('active').siblings().removeClass('active');
                $('#blugento-billing-address-' + billingAddressValue).addClass('active').siblings().removeClass('active');

                $('.new-address-btn').on('click', function() {
                    $(this).addClass('active').siblings().removeClass('active');
                    $('#billing-address-select option[value=""]').prop('selected', true);
                    $('#billing-address-select').trigger('change');
                    var checkoutList   = document.getElementById('checkoutSteps');
                    if (checkoutList.dataset.orientation == 2 && checkoutList.dataset.layout == 1) {
                        var currentSection = document.getElementById('opc-billing');
                        var targetHeight   = currentSection.getElementsByClassName('step')[0].offsetHeight;
                        var titleHeight    = currentSection.getElementsByClassName('step-title')[0].offsetHeight;
                        var height         = titleHeight + targetHeight;
                        checkoutList.setAttribute('style', 'min-height: ' + height + 'px');
                    }
                });

                $('input[name="shipping[same_as_billing]"]').on('change', function() {
                    var id = $('#shipping-address-select').val();
                    //$('#billing-new-address-form').hide();

                    $('#blugento-shipping-address-' + id).addClass('active').siblings().removeClass('active');
                });
            }
            catch(e) {}
        },
        initAddToCartButtons: function()
        {
            try {
                // Show error message if qty it's smaller than default one seted from BO
                valMinQty = $('#qty, .col-qty .qty').attr('data-minimum');
                valQty = $('#qty, .col-qty .qty').attr('data-increment');

                // Disable update button
                $('.btn-update').attr('disabled', true);

                $(document).on('input', '#product_addtocart_form #qty, .col-qty .qty', function() {
                    $(this).parent().find('.minimum-qty').remove();
                    if($(this).val() < parseFloat(valMinQty)) {
                        $('#product_addtocart_form .add-to-cart button, .btn-update').attr('disabled', true);
                        $(this).parent().append('<div class="validation-advice minimum-qty">' + Translator.translate('Minimum quantity allowed is') + ' "' + valMinQty + '"' + '!</div>');
                    } else {
                        $('#product_addtocart_form .add-to-cart button, .btn-update').attr('disabled', false);
                        $(this).parent().find('.minimum-qty').remove();
                    }
                });

                $(document).on('input', '.qty-wrapper input', function() {
                    var minim = $(this).data('minimum')
                    $(this).parent().find('.minimum-qty').remove();

                    if($(this).val() < parseFloat(minim)) {
                        $(this).parent().parent().siblings().find('.btn-cart').attr('disabled', true);
                        $(this).parent().append('<div class="validation-advice minimum-qty">' + Translator.translate('Minimum quantity allowed is') + ' "' + minim + '"' + '!</div>');
                    } else {
                        $(this).parent().parent().siblings().find('.btn-cart').attr('disabled', false);
                        $(this).parent().find('.minimum-qty').remove();
                    }
                });

                $(document).on('input onKeyPress', '.qty-wrapper input, #product_addtocart_form #qty, .col-qty .qty', function() {
                    var addToCartInputQtyIncr = $(this).attr('data-increment').replace(/,/g, '');

                    if (($(this).val() * 10) % (addToCartInputQtyIncr * 10) !== 0) {
                        $('#product-addtocart-button, .btn-update').css('display', 'none').attr('disabled', true);
                        $(this).parent().parent().siblings().find('.btn-cart').attr('disabled', true);
                        $(this).parent().append('<div class="validation-advice qty-multiple">' +
                          Translator.translate('Input quantity is not a multiple of ') + addToCartInputQtyIncr + ' !</div>');
                        // productAddToCartForm.form.onsubmit = function(e) {
                        //     e.preventDefault();
                        // };

                        if ($(this).parent().find('.qty-multiple').length > 1) {
                            $(this).parent().find('.qty-multiple').not(':first').remove();
                        }
                    } else {
                        $('#product-addtocart-button, .btn-update').css('display', 'inline-block').attr('disabled', false);
                        $(this).parent().parent().siblings().find('.btn-cart').attr('disabled', false);
                        $(this).parent().find('.qty-multiple').remove();
                        // productAddToCartForm.form.onsubmit = function() {
                        //     productAddToCartForm.submit();
                        //     return false;
                        // };
                    }
                });

                $('body').on('click', '.button-counter span', function() {
                    var dataIncrement = $('.product-view .product-shop .add-to-cart input').attr('data-increment') ||
                      $(this).parent().parent().find('input[name*="qty"]').data('increment');
                    var minim = $(this).parent().parent().find('input[name*="qty"]').data('minimum') ||
                        $(this).parent().parent().find('span#qty').data('minimum');

                    var increment = dataIncrement ? parseFloat(/(,)/g.test(dataIncrement) ? dataIncrement.replace(/,/g, '') : dataIncrement) :
                      parseFloat($(this).parent().parent().find('span#qty').data('increment'));

                    var spanAttr = $(this).parent().parent().find('span#qty');
                    var button = $(this),
                        input = button.parent().parent().find('input[name*="qty"]'),
	                    span = button.parent().parent().find('span#qty'),
                        oldValue = parseFloat(input.val() ? input.val().replace(/,/g, '') : span.text().replace(/,/g, '')),
                        newValue = parseFloat(oldValue);

                    if (button.hasClass('plus')) {
                        newValue += increment;
	                      spanAttr.text(oldValue + 1);
                    } else {
                        if (oldValue > 0) {
                            newValue -= increment;
	                          spanAttr.text(oldValue -1);
                        } else {
                            newValue = 0;
                        }
                    }
                    
                    if($('#qty').hasClass('decimals')) {
                        input ? input.val(newValue.toFixed(2)) : span.text(newValue.toFixed(2));
                    } else {
                        if (newValue > Math.floor(newValue)) {
                            input ? input.val(newValue.toFixed(2)) : span.text(newValue.toFixed(2));
                        } else {
                            input ? input.val(newValue) : span.text(newValue);
                        }
                    }

                    // Show error message if qty it's smaller than default one seted from BO
                    var qtyParent = $(this).parent().parent(),
                        qtyParentList = $(this).parents('.qty-wrapper'),
                        parentElm = $(this).parents('#product_addtocart_form').find('button'),
                        parentElmList = $(this).parents('.product-actions').find('.btn-cart'),
                        qtyParentSpan = parseFloat($(qtyParent).find('span').text());

                    $(qtyParent).find('.minimum-qty').remove();
                    $(qtyParentList).find('.minimum-qty').remove();
                    
                    if($(qtyParent).find('input').val() < parseFloat(minim) || qtyParentSpan < parseFloat(minim)) {
                        $('.btn-update').attr('disabled', true);
                        $(parentElm).attr('disabled', true);
                        $(parentElmList).attr('disabled', true);
                        
                        if ($('#product_addtocart_form, .col-qty').length) {
                            $(qtyParent).append('<div class="validation-advice minimum-qty">' + Translator.translate('Minimum quantity allowed is') + ' "' + minim + '"' + '!</div>');
                        } else {
                            $(qtyParentList).append('<div class="validation-advice minimum-qty">' + Translator.translate('Minimum quantity allowed is') + ' "' + minim + '"' + '!</div>');
                        }
                    } else {
                        $('.btn-update').attr('disabled', false);
                        $(parentElm).attr('disabled', false);
                        $(parentElmList).attr('disabled', false);
                        $(qtyParent).find('.minimum-qty').remove();
                        $(qtyParentList).find('.minimum-qty').remove();
                    }
                });
            }
            catch(e) {}
        },
        init: function()
        {
            Blugento.Checkout.initCards();
            Blugento.Checkout.initAddToCartButtons();
        }
    });

    $(document).ready(function() {
        Blugento.Checkout.init();
    });

    $(document).ajaxSuccess(function() {
        Blugento.Checkout.initCards();
    });

    $(window).on('blugento.window.resize', function() {
        Blugento.Checkout.initCards();
    });

})(jQuery);
