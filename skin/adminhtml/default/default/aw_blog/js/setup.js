var Blugento = Blugento || {};

Blugento.Util = Blugento.Util || {};

Object.extend(Blugento.Util, {
	/**
	 * @see http://jehiah.cz/a/firing-javascript-events-properly
	 */
	fireEvent: function (element, event)
	{
		var evt;
		
		if (document.createEventObject) {
			// dispatch for IE
			evt = document.createEventObject();
			return element.fireEvent('on' + event, evt)
		} else {
			// dispatch for modern browsers
			evt = document.createEvent('HTMLEvents');
			evt.initEvent(event, true, true); // event type, bubbling, cancelable
			return !element.dispatchEvent(evt);
		}
	}
});

Blugento.Blog = Class.create({
	
	typeOfTemplateWrapper: null,
	
	typeOfTemplate: null,
	
	typeOfTemplateValue: 0,
	
	productSlider: null,
	
	productSliderValue: 0,
	
	initialize: function()
	{
		this.typeOfTemplate = $$('select[name="parameters[type_of_template]"]')[0];
		this.productSlider = $$('select[name="parameters[display_type]"]')[0];
		if (this.typeOfTemplate) {
			this.typeOfTemplateWrapper = this.typeOfTemplate.up();
			this.typeOfTemplateValue = this.typeOfTemplate.value;
		}
		if (this.productSlider) {
			this.productSliderValue = this.productSlider.value;
		}
		
		this.renderHtml();
		this.updateOptions();
		this.handleEvents();
	},
	
	updateOptions: function()
	{
		var optionsSliderDisabled = this.typeOfTemplateWrapper.select('.slider-disabled');
		
		if (this.productSliderValue == 2) {
			optionsSliderDisabled.invoke('hide');
		} else {
			optionsSliderDisabled.invoke('show');
		}
	},
	
	renderHtml: function()
	{
		var s = '<div class="multiproducts-type">';
		
		(this.typeOfTemplate.select('option')).each(function(element, index) {
			var c = 'option' +
				' option-' + index +
				((element.value.indexOf('slider-disabled') > 0) ? ' slider-disabled' : ' slider-enabled') +
				((element.selected) ? ' active' : '');
			
			s += '<a href="javascript:void(0)" data-index="' + index + '" class="' + c  + '">' +
				'<h6>' + element.text + '</h6>' +
				'<div>' +
				'<span></span><span></span><span></span><span></span>' + ((element.value.indexOf('grid-6') > 0) ? '<span></span><span></span>' : '') +
				'</div>' +
				'</a>';
		});
		
		s += '</div>';
		
		this.typeOfTemplateWrapper.insert({
			'bottom': s
		});
	},
	
	handleEvents: function(event) {
		var thiz = this;
		
		this.typeOfTemplateWrapper.on('click', 'a.option', function(event, element) {
			thiz.setTypeOfTemplate(element.readAttribute('data-index'));
			element.addClassName('active').siblings().invoke('removeClassName', 'active');
		});
		
		this.productSlider.on('change', function(event, element) {
			thiz.productSliderValue = element.value;
			thiz.updateOptions();
		});
	},
	
	setTypeOfTemplate: function(i)
	{
		(this.typeOfTemplate.select('option'))[i].selected = true;
		Blugento.Util.fireEvent(this.typeOfTemplate, 'change');
	}
});