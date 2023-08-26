IwaysPaypalPlus = Class.create();
IwaysPaypalPlus.prototype = {
    requestUrl: null,
    requestParamObject: null,
    checkout: null,
    initialize: function (url, checkout, params) {
        this.requestUrl = url;
        this.checkout = checkout;
        this.requestParamObject = {
            method: 'post',
            parameters: params,
            onSuccess: function (transport) {
                try {
                    response = eval('(' + transport.responseText + ')');
                }
                catch (e) {
                    response = {};
                }
                if (response.redirect) {
                    review.isSuccess = true;
                    window.ppp.doCheckout();
                    return;
                }
                if (response.success) {
                    review.isSuccess = true;
                    window.ppp.doCheckout();
                }
                else {
                    var msg = response.error_messages;
                    if (typeof(msg) == 'object') {
                        msg = msg.join("\n");
                    }
                    if (msg) {
                        alert(msg);
                    }
                    review.resetLoadWaiting(transport);
                }
            },
            onFailure: this.checkout.ajaxFailure.bind(this.checkout)
        };
    },

    requestToPaypal: function () {
        var ajaxRequest = new Ajax.Request(this.requestUrl, this.requestParamObject);
    }
};