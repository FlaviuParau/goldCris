setTimeout(function () {
	function hasClass(ele, cls) {
		return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
	}
	
	if (hasClass(document.getElementById('html-body'), 'adminhtml-catalog-product-new')) {
		var select = document.getElementById('inventory_stock_availability');
		var option;
		
		for (var i = 0; i < select.options.length; i++) {
			option = select.options[i];
			
			if (option.value == '1') {
				option.setAttribute('selected', 'selected');
			}
			
			if (option.value == '0') {
				option.removeAttribute('selected');
			}
		}
	}
}, 200);
