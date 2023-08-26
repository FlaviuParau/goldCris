var BlugentoTinyMceExtension = Class.create();

BlugentoTinyMceExtension.prototype = {

    _key            : 0,
    _options        : {
        custom_css          : '',
        store_loader_url    : '',
        store_config        : [],
        store_loader_parent : ''
    },
    _storeLoader    : null,

    /**
     * Extend the TinyMCE setup.
     *
     * @return BlugentoTinyMceExtension
     */
    extendSetup: function() {
        var instance = this;
        if (typeof tinyMceWysiwygSetup != 'undefined') {

            tinyMceWysiwygSetup.prototype.getDefaultSettings = tinyMceWysiwygSetup.prototype.getSettings;

            tinyMceWysiwygSetup.prototype.getSettings = function() {
                var settings = this.getDefaultSettings();

                // Preserve HTML source formatting
                settings.apply_source_formatting = false;
                settings.remove_linebreaks = false;

                // Re-write stock setup
                // Creates a bit of maintenance debt
                settings.setup = function(ed) {
                    ed.onSubmit.add(function(ed, e) {
                        varienGlobalEvents.fireEvent('tinymceSubmit', e);
                    });

                    ed.onPaste.add(function(ed, e, o) {
                        varienGlobalEvents.fireEvent('tinymcePaste', o);
                    });

                    ed.onBeforeSetContent.add(function(ed, o) {
                        varienGlobalEvents.fireEvent('tinymceBeforeSetContent', o);
                    });

                    ed.onSetContent.add(function(ed, o) {
                        varienGlobalEvents.fireEvent('tinymceSetContent', o);
                    });

                    ed.onSaveContent.add(function(ed, o) {
                        varienGlobalEvents.fireEvent('tinymceSaveContent', o);
                    });

                    ed.onChange.add(function(ed, l) {
                        varienGlobalEvents.fireEvent('tinymceChange', l);
                    });

                    ed.onExecCommand.add(function(ed, cmd, ui, val) {
                        varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
                    });

                    // Extended for this: to load CSS
                    ed.onPostRender.add(function(ed) {
                        if ( (css = instance.get('custom_css')) ) {
                            ed.dom.loadCSS(instance.tagCss(css));
                        }
                    });
                }

                settings.extended_valid_elements = "layout";
                settings.custom_elements = "layout";

                settings.plugins = 'blugento_homepagemanager,' + settings.plugins;

                settings.toolbar = false;

                settings.theme_advanced_buttons1 = 'blugento_homepagemanager,magentovariable,magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect';
                settings.theme_advanced_buttons2 = 'link,unlink,anchor,image,cleanup';
                settings.theme_advanced_buttons3 = false;
                settings.theme_advanced_buttons4 = false;

                return settings;
            }
        }

        return this;
    },

    /**
     * Option getter.
     *
     * @param string key The option key.
     *
     * @return mixed
     */
    get: function(key) {
        if (typeof this._options[key] != 'undefined') {
            return this._options[key];
        }

        return null;
    },

    /**
     * Prepare the extension.
     *
     * @param object options Initialization options.
     *
     * @return void
     */
    initialize: function(options) {
        this._options = Object.extend(this._options, options || {});

        this.extendSetup();
    },

    /**
     * Option setter.
     *
     * @param string key   The option key.
     * @param mixed  value The option value.
     *
     * @return BlugentoTinyMceExtension
     */
    set: function(key, value) {
        this._options[key] = value;

        return this;
    },

    /**
     * Tag the CSS before loading for management.
     *
     * @param string css The URL to the CSS.
     *
     * @return string
     */
    tagCss: function(css, tag) {
        if (!tag) {
            tag = 'enhanced_tinymce';
        }

        if (css.indexOf('?') > 0) {
            tag = '&' + tag;
        } else {
            tag = '?' + tag;
        }

        tag += '&key=' + this._key.toString();

        this._key++;

        return css + tag;
    }
};

document.observe('dom:loaded', function() {
    var o = $$('input[name="blugento_tinymce_home"');
    if (o.length == 1 && o[0].value == 'home') {
        var bluTinyMce = new BlugentoTinyMceExtension();
    }
});
