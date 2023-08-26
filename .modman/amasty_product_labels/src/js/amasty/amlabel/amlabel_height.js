var correctFontSize = true;
amLabelSetCorrectHeight = function () {
    $$('.amlabel-txt2').each(function (item) {
        imageSrc = $(item).getStyle('background-image')
            .replace(/url\((['"])?(.*?)\1\)/gi, '$2')
            .split(',')[0];
        if (imageSrc === 'none') {
            return true;
        }

        var image = new Image();

        image.onload = function () {
            var width = image.width,
                height = image.height;
            if (width && height) {
                var percent = item.getWidth() / width;
                var heightNew = percent * height;
                if (heightNew) {
                    $(item).setStyle({
                        'height': heightNew + 'px'
                    });
                }

                if (correctFontSize && !$(item).select('.amlabel-txt br').length && item.getWidth()) {//disable if <br> exists
                    var textElement = $(item).select('.amlabel-txt')[0];
                    if (textElement.getAttribute('style').indexOf('font-size') == -1) {
                        var flag = 1;
                        textElement.setStyle({
                            'position': 'absolute'
                        });
                        while (textElement.getWidth() > 0.9 * item.getWidth() && flag++ < 15) {
                            textElement.setStyle({
                                'fontSize': (100 - flag * 5) + '%'
                            });
                        }
                        textElement.setStyle({
                            'position': 'initial'
                        });
                    }
                }
            }
        };

        image.src = imageSrc;
    });
};

Event.observe(window, 'load', function () {
    amLabelSetCorrectHeight();
});