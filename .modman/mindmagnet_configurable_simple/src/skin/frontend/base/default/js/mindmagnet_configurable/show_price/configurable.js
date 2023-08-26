/**
 * Get percentage if special price are visible
 */

function getWholePercent(percentFor, percentOf) {
    return Math.floor(percentFor / percentOf * 100);
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
            price = priceOptions[id]['price'] - this.config.basePrice;
            
            if (priceOptions[id]['price'] != priceOptions[id]['oldPrice']) {
                oldPrice = priceOptions[id]['oldPrice'] - this.config.oldPrice;

                if (jQuery('.discount').length) {
                    var discount = priceOptions[id]['oldPrice'] - priceOptions[id]['price'];
                    var discountPrice = this.formatPrice(discount);

                    jQuery('.discount span').text(`(-${discountPrice})`);
                    jQuery('.discount').show();
                }
            } else {
                oldPrice = price;

                jQuery('.discount').hide();
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

    if (id !== undefined && (priceOptions[id]['price'] != priceOptions[id]['oldPrice'])) {
        this.handlePrices(price, oldPrice);
    } else {
        this.handlePrices(price);
    }
    
    optionsPrice.reload();
    
    return price;
};

Product.Config.prototype.handlePrices = function(price, oldPrice = null) {
    var priceElement = $('product-price-' + this.config.productId),
      oldPriceElement = $$('.product-shop .price-box .old-price')[0];
    
    if (oldPriceElement) {
        oldPriceElement.remove();
    }
    
    if (priceElement) {

        if (oldPrice != null) {
            priceElement
              .addClassName('special-price')
              .removeClassName('regular-price');
            
            var oldPriceString = '<p class="old-price">' +
              '<span class="price" id="old-price-' + this.config.productId + '">' +
              oldPrice +
              '</span>' +
              '</p>';
    
            priceElement.insert({
                before: oldPriceString
            });
        } else {
            priceElement
              .addClassName('regular-price')
              .removeClassName('special-price');
        }
    }
};
