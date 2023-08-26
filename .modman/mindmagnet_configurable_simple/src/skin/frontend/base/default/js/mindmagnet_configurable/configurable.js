/**
 * Get percentage if special price are visible
 */

function getWholePercent(percentFor,percentOf){
    return Math.floor(percentFor/percentOf*100);
}

/**
 * Rewrite the reloadPrice method from /js/varien/configurable.js
 */
Product.Config.prototype.reloadPrice = function() {
    if (this.config.disablePriceReload) {
        return;
    }

    var price = 0,
        oldPrice = 0,
        products = [];

    for (var i = this.settings.length - 1; i >= 0; i--) {
        var selected = this.settings[i].options[this.settings[i].selectedIndex];

        if (selected.config) {
            if (products.length == 0) {
                products = selected.config.allowedProducts;
            } else {
                products = products.intersect(selected.config.allowedProducts);
            }
        }
    }
    
    if (products.length) {
        var id = products[0];

        if ((typeof priceOptions == 'object') && (typeof priceOptions[id] != 'undefined')) {
            var oldPriceElement = jQuery('.product-shop .price-box .old-price');
            price = priceOptions[id]['price'] - this.config.basePrice;
            oldPrice = priceOptions[id]['oldPrice'] - this.config.oldPrice;

            // Hide old price if is the same like new price
            if (priceOptions[id]['oldPrice'] == priceOptions[id]['price']) {
                oldPriceElement.hide();
                jQuery('.discount').hide();
            } else {
                if (jQuery('.discount').length) {
                    var discount = priceOptions[id]['oldPrice'] - priceOptions[id]['price'];
                    var discountPrice = this.formatPrice(discount);
    
                    jQuery('.discount span').text(`(-${discountPrice})`);
                    jQuery('.discount').show();
                }

                oldPriceElement.show();
            }

            // Calculate discount from old price and special price and than add new percentage to the label
            const percent = Math.round(100 - (priceOptions[id]['price'] / priceOptions[id]['oldPrice']) * 100);
            // var priceDiscount = priceOptions[id]['oldPrice'] - priceOptions[id]['price']
            // var wholePercent = getWholePercent(priceDiscount, priceOptions[id]['oldPrice']);
            
            if (percent === 0) {
                jQuery('.product-view .badge--sale').hide();
            } else {
                jQuery('.product-view .badge--sale').html('-' + percent + '%');
                jQuery('.product-view .badge--sale').show();
            }
        }
    }

    optionsPrice.changePrice('config', {
        'price': price,
        'oldPrice': oldPrice
    });

    this.handlePrices(price, oldPrice);

    optionsPrice.reload();

    return price;
};

Product.Config.prototype.handlePrices = function(price, oldPrice) {
    var priceElement = $('product-price-' + this.config.productId);

    if (priceElement) {
        priceElement
            .addClassName('regular-price')
            .removeClassName('special-price');

        var priceLabel = priceElement.down('.price-label');

        if (priceLabel) {
            priceLabel.remove();
        }
    }
};
