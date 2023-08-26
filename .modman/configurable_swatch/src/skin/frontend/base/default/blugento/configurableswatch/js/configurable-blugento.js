jQuery.noConflict();

jQuery(function ($) {
	if ($(".category-products, .cms-index-index, .block-upsell, .block-related")[0]) {
		$('.products-grid .configurable-swatch-list li, .products-list .configurable-swatch-list li').each(function () {
			$(this).on('click', function () {
				$(this).parent().find('li').removeClass('selected');
				$(this).addClass('selected');
				$(this).parent().addClass('swatch-selected');
				$(this).parent().find('div.validation-advice:not(.validation-advice-qty)').hide();
			});
		});
		
		$('.products-widget .configurable-swatch-list li, .category-products .configurable-swatch-list li, .block-upsell .configurable-swatch-list li, .block-related .configurable-swatch-list li').each(function () {
			$(this).on('click', function () {
				var id = $(this).parent().find('input').attr('data-id-val');
				var label = $(this).attr('data-option-label');
				var val_index = $(this).attr('data-value-index');
				var child_id = $(this).attr('data-child-id');
				var productImgUpdate = $(this).closest('.item').find('.product-img');
				var productPriceUpdate = $(this).closest('.item').find('.price-box :not(.special-price) .price');
				var productSpecialPriceUpdate = $(this).closest('.item').find('.price-box .special-price .price');
				var qty = $(this).attr('data-qty');
				var stockStatus = $(this).closest('.item').find('div.availability-box');
				var button = $(this).parents('.item').find('.btn-cart');
				var details = $(this).parents('.item').find('a.button.btn-swatch');
				var backorder = $(this).attr('data-backorder');
				
				$(this).parent().find('input').attr('data-selected-value', val_index);
				
				var def_image = $(this).parent().find('input').attr('data-selected-value', val_index);
				
				new Ajax.Request(getSwatchProductDataUrl, {
					method: 'get',
					parameters: {label: label, id: id, valindex: val_index, childid: child_id, defimage: def_image},
					onSuccess: function (transport) {
						var response = JSON.parse(transport.responseText);
						
						var optionPrice = response.price;
						var optionSpecialPrice = response.special_price;
						var productId = response.id;
						var image = response.image;
						
						if (response.error !== 1) {
							$(productPriceUpdate).html(optionPrice);
							
							if ($(productSpecialPriceUpdate)) {
								$(productSpecialPriceUpdate).html(optionSpecialPrice);
							}
							
							if (qty <= 0 && backorder == 0) {
								$(stockStatus).html(Translator.translate('Out of Stock')).addClass('out-of-stock')
									.removeClass('backorder').removeClass('in-stock');
							} else if (qty <= 0 && backorder != 0) {
								$(stockStatus).html(Translator.translate('Backorder')).addClass('backorder')
									.removeClass('out-of-stock').removeClass('in-stock');
							} else {
								$(stockStatus).html(Translator.translate('In Stock')).addClass('in-stock')
									.removeClass('backorder').removeClass('out-of-stock');
							}
							
							if (qty < 1 && backorder != 0) {
								$(button).prop('disabled', false).show();
								$(details).hide();
							} else if (qty < 1 && backorder == 0) {
								$(button).prop('disabled', true).hide();
								$(details).css('display', 'block');
							} else {
								$(button).prop('disabled', false).show();
								$(details).hide();
							}
							
							if (image) {
								$(productImgUpdate).attr('src', image);
								$(productImgUpdate).attr('srcset', image);
								$(productImgUpdate).attr('data-src', image);
								$(productImgUpdate).attr('data-srcset', image);
							} else {
								$(productImgUpdate).attr('src', def_image);
								$(productImgUpdate).attr('srcset', def_image);
								$(productImgUpdate).attr('data-src', def_image);
								$(productImgUpdate).attr('data-srcset', def_image);
							}
							return false;
						}
					},
					onFailure: function () {
						console.log('Something went wrong...');
						return false;
					}
				});
			});
		});
		
		$('.products-grid .add-to-cart .btn-cart').each(function () {
			$(this).click(function () {
				if (!$(this).parent().parent().find('.configurable-swatch-list').hasClass('swatch-selected')) {
					$(this).parent().parent().find('.configurable-swatch-list div.validation-advice:not(.validation-advice-qty)').show();
				} else {
					var product_id = $(this).parent().parent().find('input').attr('data-id-val');
					var selected_value = $(this).parent().parent().find('input').attr('data-selected-value');
					var attribute_id = $(this).parent().parent().find('input').attr('data-attribute-id');
					var qty_id = $(this).parent().parent().find('#qty').text();
					
					new Ajax.Request(addToCartUrl, {
						method: 'get',
						parameters: {
							productid: product_id,
							selectedvalue: selected_value,
							attributeid: attribute_id,
							qtyvalue: qty_id
						},
						onSuccess: function (transport) {
							var response = JSON.parse(transport.responseText);
							console.log(response);
							if (response.error) {
								console.log(response.error);
							} else {
								if (response.minicart) {
									//$('#mini-cart').html(response.minicart); Ajax update
									location.reload();
								}
							}
						},
						onFailure: function () {
							console.log('Something went wrong...');
							return false;
						}
					});
				}
			});
		});
		
		
		$('.products-grid .item, .products-list .item').each(function () {
			$(this).find('.configurable-swatch-list.last-selected').children('li').last().trigger('click');
			$(this).find('.configurable-swatch-list.first-selected').children('li').first().trigger('click');
			var defaultValue = $(this).find('.configurable-swatch-list').children('li').last().attr('data-value-index');
			$(this).find('.configurable-swatch-list').find('input').attr('data-selected-value', defaultValue);
		});
	}
});
