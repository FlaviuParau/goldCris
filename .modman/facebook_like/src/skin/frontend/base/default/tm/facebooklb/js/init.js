document.observe("dom:loaded", function() {
    // initialize facebook like button
    var id = 'facebook-jssdk';
    if ($(id) || !$('fb-root')) return;
    var locale = $('fb-root').hasAttribute('data-locale')
            ? $('fb-root').getAttribute('data-locale') : 'en_US';
    $$('script').first().insert({
        'before': new Element('script', {
            'src': "//connect.facebook.net/"+locale+"/sdk.js#xfbml=1&version=v2.8",
            'id': id
            })
        }
    );
    // listen JS events
    ["ajaxlayerednavigation:ready", "quickshopping:previewloaded",
     "AjaxPro:onSuccess:after", "AjaxPro:onComplete:after"].map(
        function(eventName){
            document.observe(eventName, function(){FB.XFBML.parse()});
        }
    );
});
