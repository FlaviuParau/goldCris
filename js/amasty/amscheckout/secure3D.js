Amasty3DSecure = Class.create();
Amasty3DSecure.prototype = {
    is3DSecureEnabled: false,

    get CVV_CODELENGTH() {
        return 3;
    },

    initialize: function (is3DSecureEnabled) {
        this.is3DSecureEnabled = is3DSecureEnabled;
    },

    isValidPaypalCC: function () {
        var selectors = [
            'paypal_direct_cc_type',
            'paypal_direct_cc_number',
            'paypal_direct_expiration',
            'paypal_direct_expiration_yr'
        ];

        for (var selector in selectors) {
            var input = $(selector);

            if (input && input.value.length == 0)
                return false;
        }

        return true;
    },

    request3DSecure: function (url) {
        if (!this.is3DSecureEnabled) {
            return;
        }

        var formKey = '';
        if ($$('input[name="form_key"]').length) {
            formKey = $$('input[name="form_key"]')[0].value;
        }

        new Ajax.Request(url, {
            method: 'get',
            parameters: {form_key: formKey, isFrame: 1},
            onSuccess: function (response) {
                var centinelAuthBlock = $('centinel_authenticate_block');
                if (centinelAuthBlock) {
                    // This code moves the iframe to container <div class="order-review" id="checkout-review-load">
                    // when user changes payment method then the block with iframe will hide.
                    centinelAuthBlock.remove();
                    $('checkout-review-load').insert({top: centinelAuthBlock});
                    jQuery.fancybox({
                        href: '#centinel_authenticate_block',
                        minWidth: 660,
                        helpers: {
                            overlay: {closeClick: false}
                        }
                    });
                }
            }
        });
    },

    requestAuthCentinel3DSecure: function (url) {
        if (!this.is3DSecureEnabled) {
            return;
        }

        new Ajax.Request(url, {
            method: 'post',
            onSuccess: function (response) {
                var config = response.responseText.evalJSON();
                var amastyBeforeCentinelBlock = $('amasty_before_centinel');
                if (amastyBeforeCentinelBlock) {
                    amastyBeforeCentinelBlock.insert({after: config.html});
                }
                this.request3DSecure(url);
            }
        });
    },

    requestCentinel3DSecure: function () {
        var paypalDirectCidBlock = $('paypal_direct_cc_cid');
        if (paypalDirectCidBlock && $('amasty_before_centinel')) {
            paypalDirectCidBlock.observe('keyup', function () {
                if ($('p_method_paypal_direct').checked && this.isValidPaypalCC() && paypalDirectCidBlock.value.length == this.CVV_CODELENGTH) {
                    updateCheckout('payment_method');
                    this.is3DSecureEnabled = true;
                    var centinelUrl = $('centinel_url').value;
                    this.request3DSecure(centinelUrl);
                }
            });
        }
    }
};
