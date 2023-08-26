/**
 * Add support for xhrFields to fix CORS
 */
Ajax.Request.prototype.setRequestHeaders = Ajax.Request.prototype.setRequestHeaders.wrap(function(setHeaders) {
    setHeaders();
    if (this.options.xhrFields) {
        Object.extend(this.transport, this.options.xhrFields);
    }
});

AjaxLogin = Class.create();
AjaxLogin.prototype = {
    initialize: function(config) {
        this.config = Object.extend({
            triggers: null,
            markup:
            '<div class="d-shadow-wrap">'
            +   '<div class="content"></div>'
            +   '<div class="d-sh-cn d-sh-tl"></div><div class="d-sh-cn d-sh-tr"></div>'
            + '</div>'
            + '<div class="d-sh-cn d-sh-bl"></div><div class="d-sh-cn d-sh-br"></div>'
            + '<button title="Close (Esc)" type="button" class="mfp-close close">×</button>'
            + '<div class="ajaxcart-overlay visible" id="please-wait"><div class="ajaxcart-loader"></div></div>'
        }, config || {});

        this._prepareMarkup();
        this._attachEventListeners();
        this._addEventListeners();
    },

    show: function() {
        $$('select').invoke('addClassName', 'ajaxlogin-hidden');
        $$('#ajaxlogin-login-window').invoke('removeClassName', 'ajaxlogin-window');
        jQuery('#ajaxlogin-mask-enabled').remove();

        jQuery('#g-recaptcha-login, #g-recaptcha-register, #btn-forgot').removeAttr('disabled');

        if (!$('ajaxlogin-mask')) {
            var mask = new Element('div');
            mask.writeAttribute('id', 'ajaxlogin-mask');
            $(document.body).insert(mask);
        }

        if (!window.ajaxloginMaskCounter) {
            window.ajaxloginMaskCounter = 0;
        }
        if (!this.maskCounted) {
            this.maskCounted = 1;
            window.ajaxloginMaskCounter++;
        }

        // set highest z-index
        var zIndex = 999;
        $$('.ajaxlogin-window').each(function(el) {
            maxIndex = parseInt(el.getStyle('zIndex'));
            if (zIndex < maxIndex) {
                zIndex = maxIndex;
            }
        });
        this.window.setStyle({
            'zIndex': zIndex + 1
        });

        this._onKeyPressBind = this._onKeyPress.bind(this);
        document.observe('keyup', this._onKeyPressBind);
        this.window.show();
        document.body.style.overflow = 'hidden';
    },

    hide: function() {
        if (this.modal || !this.window.visible()) {
            return;
        }

        if (this._onKeyPressBind) {
            document.stopObserving('keyup', this._onKeyPressBind);
        }
        if (this.config.destroy) {
            this.window.remove();
        } else {
            this.window.hide();
        }
        this.maskCounted = 0;
        if (!--window.ajaxloginMaskCounter) {
            $('ajaxlogin-mask') && $('ajaxlogin-mask').remove();
            $$('select').invoke('removeClassName', 'ajaxlogin-hidden');
        }
        document.body.style.overflow = 'initial';
    },

    setModal: function(flag) {
        this.modal = flag;

        if (flag) {
            this.window.select('.close').invoke('hide');
        } else {
            this.window.select('.close').invoke('show');
        }
        return this;
    },

    update: function(content, size) {
        var oldContent = this.content.down();
        oldContent && $(document.body).insert(oldContent.hide());

        this.content.update(content);
        content.show();
        this.addActionBar();
        return this;
    },

    addActionBar: function() {
        this.removeActionBar();

        var agreementId = this.content.down().id.replace('-window', ''),
            trigger     = this.config.triggers[agreementId];

        if (!trigger || !trigger.actionbar) {
            return;
        }

        this.content.insert({
            after: '<div class="actionbar">' + trigger.actionbar.html + '</div>'
        });
        $(trigger.actionbar.el).observe(
            trigger.actionbar.event,
            trigger.actionbar.callback.bindAsEventListener(this, agreementId.replace('ajaxlogin-', ''))
        );
    },

    removeActionBar: function() {
        var agreementId = this.content.down().id.replace('-window', ''),
            trigger     = this.config.triggers[agreementId];

        if (trigger && trigger.actionbar) {
            var actionbar = $(trigger.actionbar.el);
            if (actionbar) {
                actionbar.stopObserving(trigger.actionbar.event);
            }
        }

        this.window.select('.actionbar').invoke('remove');
    },

    getActionBar: function() {
        return this.window.down('.actionbar');
    },

    activate: function(trigger) {
        var trigger = this.config.triggers[trigger];
        this.update(trigger.window.show(), trigger.size).show();
    },

    _prepareMarkup: function() {
        this.window = new Element('div');
        this.window.addClassName('ajaxlogin-window');
        this.window.setAttribute('id','ajaxlogin-window');
        this.window.update(this.config.markup).hide();
        this.content = this.window.select('.content')[0];
        this.close   = this.window.select('.close')[0];
        $(document.body).insert(this.window);
    },

    _attachEventListeners: function() {
        if (jQuery(window).width() > 996) {
            // close window
            this.close.observe('click', this.hide.bind(this));
            // show window
            if (this.config.triggers) {
                for (var i in this.config.triggers) {
                    var trigger = this.config.triggers[i];
                    if (typeof trigger === 'function') {
                        continue;
                    }

                    trigger.el.each(function(el) {
                        var t = trigger;
                        el.observe(t.event, function(e) {
                            if (typeof event != 'undefined') { // ie9 fix
                                event.preventDefault ? event.preventDefault() : event.returnValue = false;
                            }
                            Event.stop(e);
                            if (!t.window) {
                                return;
                            }
                            this.update(t.window, t.size).show();
                        }.bind(this));
                    }.bind(this));
                }
            }
        } else {
            // Set modal data for mobile
            if (!jQuery('.hello-user').length) {
                jQuery('.mobile-trigger--profile').attr('data-dock','.ajax-login-modal');
                jQuery('.mobile-trigger--profile').attr('data-dock-position','right');
                jQuery('#g-recaptcha-login, #g-recaptcha-register, #btn-forgot').removeAttr('disabled');
            }

            // Open create form
            jQuery(document).on('click', '#noaccount, #noaccount-ajax', function(e) {
                e.preventDefault();
                jQuery('#ajaxlogin-login-window').hide();
                jQuery('#ajaxlogin-create-window').show();
            });

            // Open forgot form
            jQuery(document).on('click', '.forgot-btn', function(e) {
                e.preventDefault();
                jQuery('#ajaxlogin-login-window').hide();
                jQuery('#ajaxlogin-forgot-window').show();
            });

            // Open login form
            jQuery(document).on('click', '.login-btn', function(e) {
                e.preventDefault();
                jQuery('#ajaxlogin-forgot-window').hide();
                jQuery('#ajaxlogin-create-window').hide();
                jQuery('#ajaxlogin-login-window').show();
            });
    
            // Open custom cms block
            jQuery(document).on('click', '#cmsblock', function(e) {
                e.preventDefault();
                jQuery('#ajaxlogin-login-window').hide();
                jQuery('#ajaxlogin-cms-block-window').show();
            });
        }
    },

    _addEventListeners: function() {
        var self = this;

        $('ajaxlogin-login-form') && $('ajaxlogin-login-form').observe('submit', function(e) {
            if (typeof event != 'undefined') { // ie9 fix
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
            }
            Event.stop(e);

            if (!ajaxLoginForm.validator.validate()) {
                return false;
            }

            $('ajaxlogin-window').addClassName('loading');
            $('please-wait').show();

            new Ajax.Request($('ajaxlogin-login-form').action, {
                xhrFields: {
                    withCredentials: true
                },
                method: "post",
                parameters: $('ajaxlogin-login-form').serialize(),
                onCreate: function(response) {
                    var t = response.transport;
                    t.setRequestHeader = t.setRequestHeader.wrap(function(original, k, v) {
                        if (/^(accept|accept-language|content-language|cookie|access-control-allow-origin|access-control-allow-headers|access-control-allow-credentials)$/i.test(k))
                            return original(k, v);
                        if (/^content-type$/i.test(k) &&
                            /^(application\/x-www-form-urlencoded|multipart\/form-data|text\/plain)(;.+)?$/i.test(v))
                            return original(k, v);
                        return;
                    });
                },
                onSuccess: function(transport) {
                    var section = $('ajaxlogin-login-form');
                    if (!section) {
                        return;
                    }
                    var ul = section.select('.messages')[0];
                    if (ul) {
                        ul.remove();
                    }

                    $('ajaxlogin-window').addClassName('loading');
                    $('please-wait').show();
                    
                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        $('ajaxlogin-window').removeClassName('loading');
                        $('please-wait').hide();

                        var section = $('ajaxlogin-login-form');
                        if (!section) {
                            return;
                        }
                        var ul = section.select('.messages')[0];
                        if (!ul) {
                            section.insert({
                                top: '<ul class="messages"></ul>'
                            });
                            ul = section.select('.messages')[0]
                        }
                        var li = $(ul).select('.error-msg')[0];
                        if (!li) {
                            $(ul).insert({
                                top: '<li class="error-msg"><ul></ul></li>'
                            });
                            li = $(ul).select('.error-msg')[0];
                        }
                        $(li).select('ul')[0].insert(
                            '<li>' + response.error + '</li>'
                        );
                        self.updateCaptcha('user_login');
                    }
                    if (response.redirect) {
                        document.location = response.redirect;
                        return;
                    }
                }
            });
        });

        $('ajaxlogin-create-form') && $('ajaxlogin-create-form').observe('submit', function(e) {
            if (typeof event != 'undefined') { // ie9 fix
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
            }
            Event.stop(e);

            if (!ajaxCreateForm.validator.validate()) {
                return false;
            }

            $('ajaxlogin-window').addClassName('loading');
            $('please-wait').show();

            new Ajax.Request($('ajaxlogin-create-form').action, {
                xhrFields: {
                    withCredentials: true
                },
                method: "post",
                parameters: $('ajaxlogin-create-form').serialize(),
                onCreate: function(response) {
                    var t = response.transport;
                    t.setRequestHeader = t.setRequestHeader.wrap(function(original, k, v) {
                        if (/^(accept|accept-language|content-language|cookie|access-control-allow-origin|access-control-allow-headers|access-control-allow-credentials)$/i.test(k))
                            return original(k, v);
                        if (/^content-type$/i.test(k) &&
                            /^(application\/x-www-form-urlencoded|multipart\/form-data|text\/plain)(;.+)?$/i.test(v))
                            return original(k, v);
                        return;
                    });
                },
                onSuccess: function(transport) {
                    var section = $('ajaxlogin-create-form');
                    if (!section) {
                        return;
                    }
                    var ul = section.select('.messages')[0];
                    if (ul) {
                        ul.remove();
                    }

                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        $('ajaxlogin-window').removeClassName('loading');
                        $('please-wait').hide();

                        var section = $('ajaxlogin-create-form');
                        if (!section) {
                            return;
                        }
                        var ul = section.select('.messages')[0];
                        if (!ul) {
                            section.insert({
                                top: '<ul class="messages"></ul>'
                            });
                            ul = section.select('.messages')[0]
                        }
                        var li = $(ul).select('.error-msg')[0];
                        if (!li) {
                            $(ul).insert({
                                top: '<li class="error-msg"><ul></ul></li>'
                            });
                            li = $(ul).select('.error-msg')[0];
                        }
                        $(li).select('ul')[0].insert(
                            '<li>' + response.error + '</li>'
                        );
                        self.updateCaptcha('user_login');
                    }
                    if (response.redirect) {
                        document.location = response.redirect;
                        return;
                    }
                }
            });
        });

        $('ajaxlogin-forgot-password-form') && $('ajaxlogin-forgot-password-form').observe('submit', function(e) {
            if (typeof event != 'undefined') { // ie9 fix
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
            }
            Event.stop(e);

            if (!ajaxForgotForm.validator.validate()) {
                return false;
            }

            $('ajaxlogin-window').addClassName('loading');
            $('please-wait').show();

            new Ajax.Request($('ajaxlogin-forgot-password-form').action, {
                xhrFields: {
                    withCredentials: true
                },
                method: "post",
                parameters: $('ajaxlogin-forgot-password-form').serialize(),
                onCreate: function(response) {
                    var t = response.transport;
                    t.setRequestHeader = t.setRequestHeader.wrap(function(original, k, v) {
                        if (/^(accept|accept-language|content-language|cookie|access-control-allow-origin|access-control-allow-headers|access-control-allow-credentials)$/i.test(k))
                            return original(k, v);
                        if (/^content-type$/i.test(k) &&
                            /^(application\/x-www-form-urlencoded|multipart\/form-data|text\/plain)(;.+)?$/i.test(v))
                            return original(k, v);
                        return;
                    });
                },
                onSuccess: function(transport) {
                    var section = $('ajaxlogin-forgot-password-form');
                    if (!section) {
                        return;
                    }
                    var ul = section.select('.messages')[0];
                    if (ul) {
                        ul.remove();
                    }

                    $('ajaxlogin-window').addClassName('loading');
                    $('please-wait').show();

                    var response = transport.responseText.evalJSON();

                    if (response.error) {
                        $('ajaxlogin-window').removeClassName('loading');
                        $('please-wait').hide();

                        var section = $('ajaxlogin-forgot-password-form');
                        if (!section) {
                            return;
                        }
                        var ul = section.select('.messages')[0];
                        if (!ul) {
                            section.insert({
                                top: '<ul class="messages"></ul>'
                            });
                            ul = section.select('.messages')[0]
                        }
                        var li = $(ul).select('.error-msg')[0];
                        if (!li) {
                            $(ul).insert({
                                top: '<li class="error-msg"><ul></ul></li>'
                            });
                            li = $(ul).select('.error-msg')[0];
                        }
                        $(li).select('ul')[0].insert(
                            '<li>' + response.error + '</li>'
                        );
                        self.updateCaptcha('user_forgotpassword');
                    } else if (response.message) {
                        var section = $('ajaxlogin-login-form');
                        if (!section) {
                            return;
                        }
                        var ul = section.select('.messages')[0];
                        if (ul) {
                            ul.remove();
                        }
                        var section = $('ajaxlogin-login-form');
                        if (!section) {
                            return;
                        }
                        var ul = section.select('.messages')[0];
                        if (!ul) {
                            section.insert({
                                top: '<ul class="messages"></ul>'
                            });
                            ul = section.select('.messages')[0]
                        }
                        var li = $(ul).select('.success-msg')[0];
                        if (!li) {
                            $(ul).insert({
                                top: '<li class="success-msg"><ul></ul></li>'
                            });
                            li = $(ul).select('.success-msg')[0];
                        }
                        $(li).select('ul')[0].insert(
                            '<li>' + response.message + '</li>'
                        );

                        $('ajaxlogin-window').removeClassName('loading');
                        $('please-wait').hide();

                        if (jQuery(window).width() > 996) {
                            ajaxLoginWindow.activate('login');
                        } else {
                            jQuery('#ajaxlogin-forgot-window').hide();
                            jQuery('#ajaxlogin-login-window').show();
                        }
                    }
                }
            });
        });

        $('ajaxlogin-logout-form') && $('ajaxlogin-logout-form').observe('submit', function(e) {
            if (typeof event != 'undefined') { // ie9 fix
                event.preventDefault ? event.preventDefault() : event.returnValue = false;
            }
            Event.stop(e);

            if (!ajaxLogoutForm.validator.validate()) {
                return false;
            }

            $('ajaxlogin-window').addClassName('loading');
            $('please-wait').show();

            new Ajax.Request($('ajaxlogin-logout-form').action, {
                xhrFields: {
                    withCredentials: true
                },
                method: "post",
                parameters: $('ajaxlogin-logout-form').serialize(),
                onCreate: function(response) {
                    var t = response.transport;
                    t.setRequestHeader = t.setRequestHeader.wrap(function(original, k, v) {
                        if (/^(accept|accept-language|content-language|cookie|access-control-allow-origin|access-control-allow-headers|access-control-allow-credentials)$/i.test(k))
                            return original(k, v);
                        if (/^content-type$/i.test(k) &&
                            /^(application\/x-www-form-urlencoded|multipart\/form-data|text\/plain)(;.+)?$/i.test(v))
                            return original(k, v);
                        return;
                    });
                },
                onSuccess: function(transport) {
                    var section = $('ajaxlogin-logout-form');
                    if (!section) {
                        return;
                    }
                    var ul = section.select('.messages')[0];
                    if (ul) {
                        ul.remove();
                    }

                    var response = transport.responseText.evalJSON();
                    if (response.error) {
                        $('ajaxlogin-window').removeClassName('loading');
                        $('please-wait').hide();

                        var section = $('ajaxlogin-logout-form');
                        if (!section) {
                            return;
                        }
                        var ul = section.select('.messages')[0];
                        if (!ul) {
                            section.insert({
                                top: '<ul class="messages"></ul>'
                            });
                            ul = section.select('.messages')[0]
                        }
                        var li = $(ul).select('.error-msg')[0];
                        if (!li) {
                            $(ul).insert({
                                top: '<li class="error-msg"><ul></ul></li>'
                            });
                            li = $(ul).select('.error-msg')[0];
                        }
                        $(li).select('ul')[0].insert(
                            '<li>' + response.error + '</li>'
                        );
                    }
                    if (response.redirect) {
                        document.location = response.redirect;
                        return;
                    }
                }
            });
        });
    },

    ajaxFailure: function(){
        location.href = this.urls.failure;
    },

    _onKeyPress: function(e) {
        if (!jQuery('.show-login-first').length && e.keyCode == 27) {
            this.hide();
        }
    },

    updateCaptcha: function(id) {
        var captchaEl = $(id);
        if (captchaEl) {
            captchaEl.captcha.refresh(captchaEl.previous('img.captcha-reload'));
            // try to focus input element:
            var inputEl = $('captcha_' + id);
            if (inputEl) {
                inputEl.focus();
            }
        }
    }
};

(function($) {
    $(document).ready(function() {
        if ($('.ajaxlogin-enabled.customer-account-login, .ajaxlogin-enabled.customer-account-create').length) {
            var bodyLogin = $('body');
            $('#ajaxlogin-mask-enabled').show();

            if ($(window).width() > 996) {
                $('.login-btn').attr('id', 'login');
                var loginIn = document.getElementById('login');
                for(var i = 0; i < 50; i++)
                loginIn.click();
            } else {
                if (!$('.hello-user').length) {
                    $('.page-overlay').hide();
                    $(bodyLogin).attr('data-dock','.ajax-login-modal')
                    $(bodyLogin).addClass('dock-open dock-open--right wrap-dock--active');
                    $('.mobile-trigger--profile').addClass('dock-trigger--active');
                    $('.ajax-login-modal').addClass('dock--right dock--active');
                    $('.ajax-login-modal').append('<span class="dock-close-active"></span>')
                }
            }
        }
    });
})(jQuery);
