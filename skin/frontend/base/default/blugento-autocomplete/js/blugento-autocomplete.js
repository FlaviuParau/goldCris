(function($) {
    $(document).ready(function() {
        // Set region and city autocomplete to no (false will not work)
        $('.region_id input, .region_id select').attr('autocomplete', 'no');
        $('.city input').attr('autocomplete', 'no');

        let countryVal = $('.country-field select').val();

        function autocompleteFunction(country) {
            // Once user type city/region/zipcode, ajax will load a list of values
            // This will be loaded in a seperate element on page
            if ($('#co-billing-form').length) {
                autocompleteBillingBlugento = AwesompleteUtil.start('.billing-autocomplete',
                    {
                        url: window.location.origin + '/cautocomplete/autocomplete/city' + '?country_code=' + country + '&search='
                    },
                    {
                        minChars: 2,
                        autoFirst: true,
                        tabSelect: true,
                        data: function(rec, input) {
                            if (!$('#co-billing-form .awe-found').length) {
                                $('body').addClass('disable-button');
                            }

                            dataId = rec['region_id'];
                            dataZipcode = rec['zipcode'];
                            cityValue = rec['city'];
                            regionValue = rec['region'];
                            var cityVal = rec['city'].replace(/-/g, " ");

                            return {
                                label: cityVal + ' ' + rec['region'] + ' ' + (rec['zipcode']),
                                value: cityVal + ' ' + rec['region'] + ' ' + (rec['zipcode'])
                            }; 
                        }
                    }
                );

                // Autocomplete hidden fields with values from selected click
                $(document).on('click', '#co-billing-form .awesomplete ul li', function() {
                    $('#co-billing-form .region input').value = regionValue;
                    $('#co-billing-form .region_id select').val(dataId);
                    $('#co-billing-form .city input').val(cityValue);
                    $('#co-billing-form .zip-code input').val(dataZipcode);
                    $('body').removeClass('disable-button');
                });
            }

            if ($('#co-shipping-form').length) {
                autocompleteShippingBlugento = AwesompleteUtil.start('.shipping-autocomplete',
                    {
                        url: window.location.origin + '/cautocomplete/autocomplete/city' + '?country_code=' + country + '&search='
                    },
                    {
                        minChars: 2,
                        autoFirst: true,
                        tabSelect: true,
                        data: function(rec, input) { 
                            if (!$('#co-shipping-form .awe-found').length) {
                                $('body').addClass('disable-button');
                            }

                            dataIdShipping = rec['region_id'];
                            dataZipcodeShipping = rec['zipcode'];
                            cityValueShipping = rec['city'];
                            regionValueShipping = rec['region'];
                            var cityVal = rec['city'].replace(/-/g, " ");

                            return { 
                                label: cityVal + ' ' + rec['region'] + ' ' + (rec['zipcode']),
                                value: cityVal + ' ' + rec['region'] + ' ' + (rec['zipcode'])
                            };
                        }
                    }
                );

                // Autocomplete hidden fields with values from selected click
                $(document).on('click', '#co-shipping-form .awesomplete ul li', function() {
                    $('#co-shipping-form .region input').value = regionValueShipping;
                    $('#co-shipping-form .region_id select').val(dataIdShipping);
                    $('#co-shipping-form .city input').val(cityValueShipping);
                    $('#co-shipping-form .zip-code input').val(dataZipcodeShipping);
                    $('body').removeClass('disable-button');
                });
            }
        }

        if (countryVal === 'RO' || countryVal === 'BE') {
            autocompleteFunction(countryVal);
        }

        // Hide autocomplete field if another country is selected
        $(document).on('change', '.country-field select', function() {
            countryVal = $(this).val();

            if (countryVal === 'RO' || countryVal === 'BE') {
                autocompleteBillingBlugento.destroy();
                autocompleteShippingBlugento.destroy();
                autocompleteFunction(countryVal);

                $('body').removeClass('hide-field');
                $('.results-autocomplete input').addClass('required-entry');
            } else {
                $('body').addClass('hide-field');
                $('.results-autocomplete input').removeClass('required-entry');
            }
        });
    });
})(jQuery);