// -----------------------------------------------------------------------------
// @see: https://github.com/gruntjs/grunt-contrib-clean
// npm install grunt-contrib-clean --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        css: [
            'media/css/*.css',
            'media/css_secure/*.css',
            'media/mindmagnet_pagespeed/**/*.css'
        ],
        js: [
            'media/js/*.js',
            'media/js_secure/*.js',
            'media/mindmagnet_pagespeed/**/*.js'
        ],
        images: [
            'media/catalog/product/cache/*'
        ],
        cache: [
            '.sass-cache/*',
            'var/cache/*',
            'var/full_page_cache/*'
        ],
        log: [
            'var/log/*'
        ],
        report: [
            'var/report/*'
        ],
        session: [
            'var/session/*'
        ],
        skin: [
            '<%= project.assets %>/css/**/*.map'
        ]
    };
};
