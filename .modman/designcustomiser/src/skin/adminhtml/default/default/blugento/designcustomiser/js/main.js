var Blugento = Blugento || {};

Blugento.DesignCustomiser = Class.create({
    id: '',
    element: null,

    initialize: function()
    {
    }
});

Blugento.DesignCustomiserScss = Class.create(Blugento.DesignCustomiser, {
    initialize: function(id)
    {
        this.id = id;
        this.element = $(this.id);
        this.ui();
    }
});

Blugento.DesignCustomiserScssFontFamily = Class.create(Blugento.DesignCustomiserScss, {
    ui: function()
    {
        var self = this;

        this.element.on('change', function(element, index) {
            self.element.writeAttribute('data-last-value', self.element.value);
        });
    }
});

Blugento.DesignCustomiserScssGoogleFonts = Class.create(Blugento.DesignCustomiserScss, {
    ui: function()
    {
        try {
            var self = this;

            $(this.id).on('change', function(element, index) {
                self.fillGoogleFonts();
            });

            document.observe('dom:loaded', function() {
                self.fillGoogleFonts();
            });
        }
        catch(e) {
            // console.log(e);
        }
    },

    fillGoogleFonts: function()
    {
        var self = this,
            fonts = $(this.id).getValue(),
            options = '';

        for (var i = 0; i < fonts.length; i++) {
            options += '<option value="' + fonts[i] + '">' + fonts[i] + '</option>';
        }

        if (options === '') {
            $$('optgroup.google-fonts').invoke('hide');
        } else {
            $$('optgroup.google-fonts').each(function(element) {
                try {
                    element.update(options);

                    // --------------------------------------------------------

                    var select = element.up(),
                        selectOptions = [],
                        id = select.readAttribute('id').replace('styling_', ''),
                        defaultValue = select.readAttribute('data-default-value'),
                        lastValue = select.readAttribute('data-last-value');

                    for (var i = 0; i < select.options.length; i++) {
                        selectOptions.push(select.options[i].value);
                    }

                    if (selectOptions.indexOf(lastValue) < 0) {
                        if (selectOptions.indexOf(defaultValue) >= 0) {
                            select.setValue(defaultValue);
                        } else {
                            $('set-inherit-' + id).setValue('auto');
                            $('container-custom-' + id).hide();
                        }
                    } else {
                        select.setValue(lastValue);
                    }
                }
                catch(e) {
                    // console.log(e);
                }
            }).invoke('show');
        }
    }
});

Blugento.DesignCustomiserLayout = Class.create(Blugento.DesignCustomiser, {
    initialize: function(id)
    {
        this.id = id;
        this.ui();
    }
});

// TODO @Daniel: review
Blugento.DesignCustomiserLayoutThumbnail = Class.create(Blugento.DesignCustomiserLayout, {
    ui: function()
    {
        try {

            var element = $('ui-layout_' + this.id);

            if (element) {
                var list = $('ui-layout_' + this.id).childElements(),
                    select = $('layout_' + this.id),
                    current_select_template = $('ui-layout_' + this.id).id;

                $$('#' + current_select_template + ' li').invoke('hide');
                $$('#' + current_select_template + ' li.active').invoke('show');

                if (list) {
                    list.invoke('observe', 'click', function(e) {
                        Event.stop(e);
                        var data_value = $(this).readAttribute('data-value');
                        $(this).addClassName('active');
                        $(this).siblings().invoke('removeClassName', 'active');

                        var collection_items = select.childElements();

                        var len = collection_items.length;

                        for (var i = 1; i < len; i++) {
                            if (i > data_value)
                                break;
                        }
                        collection_items[data_value - 1].selected = true;
                    });
                }
                select.hide();

                var expand = $('expand_' + this.id).id;
                var expand_click = $(expand);

                expand_click.observe('click', function() {

                    $$('#' + current_select_template + ' li').invoke('toggle');
                    $$('#' + current_select_template + ' li.active').invoke('show');
                    $(this).toggleClassName('active');

                });
            }
        }
        catch(e) {
            // console.log(e);
        }
    }
});

// ----------------------------------------------------------------------------

function setInherit(id) {
    try {
        var v = $F('set-inherit-' + id),
            containerCustom = $('container-custom-' + id),
            containerInherit = $('container-inherit-' + id);

        if (v == 'yes') {
            if (containerCustom) {
                containerCustom.hide();
            }
            if (containerInherit) {
                containerInherit.show();
            }
        } else if (v == 'no') {
            if (containerCustom) {
                containerCustom.show();
            }
            if (containerInherit) {
                containerInherit.hide();
            }
        } else {
            if (containerCustom) {
                containerCustom.hide();
            }
            if (containerInherit) {
                containerInherit.hide();
            }
        }
    }
    catch(e) {
        // console.log(e);
    }
}

// ----------------------------------------------------------------------------

Blugento.DesignCustomiserSetup = Class.create({
    initialize: function()
    {
        var thiz = this;

        this.createHorizontalTabs();

        varienGlobalEvents.attachEventHandler('showTab', function(arg) {

            var tabId = arg.tab.readAttribute('id'),
                visibleTopItemsLinks = [];

            // Show horizontal menu items with respect to the selected item in vertical menu, hide other contextual items
            $('designcustomiser_tabs2').childElements().each(function(item) {
                if (item.readAttribute('data-tab') == tabId) {

                    item.show();

                    // Immediately save the ID of the active item in horizontal menu
                    var itemLink = item.firstDescendant();
                    if (itemLink.hasClassName('active')) {
                        $('setup_active_tab_top_id').setValue(itemLink.identify());
                    }

                    visibleTopItemsLinks.push(itemLink);
                } else {
                    item.hide();
                }
            });

            // console.log(visibleTopItemsLinks);

            // If the selected vertical menu item has fieldsets: show the horizontal menu & setup active tab
            if (visibleTopItemsLinks.length) {

                // Get active tab
                var activeTabTopId = $('setup_active_tab_top_id').getValue();

                // Prepare to show the active item
                var _itemLink = visibleTopItemsLinks.detect(function(e) {
                    return e.hasClassName('active');
                });

                // console.log(_itemLink);

                for (var i = 0; i < visibleTopItemsLinks.length; i++) {
                    if (visibleTopItemsLinks[i].identify() == activeTabTopId) {
                        _itemLink = visibleTopItemsLinks[i];
                    }
                }

                if ( ! _itemLink) {
                    _itemLink = visibleTopItemsLinks[0];
                }

                // Set active item and show it
                _itemLink.addClassName('active');
                thiz.setActiveTab(_itemLink, _itemLink.identify().replace('designcustomiser_tabs2_', ''), tabId);

                $('designcustomiser_tabs2').show();
            } else {
                $('designcustomiser_tabs2').hide();
            }
        });

        // ------------------------------------------------------------------------

        $$('ul#designcustomiser_tabs a.tab-item-link').each(function(element) {
            element.observe('click', function(event) {
                Event.stop(event);
                $('setup_active_tab_id').setValue(element.identify());
            });
        });

        $$('ul#designcustomiser_tabs2 a.tab-item-link').each(function(element) {
                element.observe('click', function(event) {
                    Event.stop(event);
                    $('setup_active_tab_top_id').setValue(element.identify());
                });
            });
    },

    setActiveTab: function(element, fieldsetId, contentId)
    {
        $$('#designcustomiser_tabs2 [data-tab="' + contentId + '"] .tab-item-link').invoke('removeClassName', 'active');
        $(element).addClassName('active');

        $$('#' + contentId + '_content .container-tab').invoke('addClassName', 'no-show');
        $(fieldsetId).removeClassName('no-show');
    },

    createHorizontalTabs: function()
    {
        var s = '';

        $$('ul#designcustomiser_tabs a.tab-item-link').each(function(element) {
            var id = element.readAttribute('id'),
                contentId = id + '_content',
                contentFieldsets = $$('#' + contentId + ' .container-tab');

            contentFieldsets.each(function(fieldset) {
                var fieldsetId = fieldset.readAttribute('id'),
                    fieldsetName = $('legend-' + fieldsetId).innerHTML.trim(),
                    liClass = 'tab',
                    aClass = 'tab-item-link',
                    aId = 'designcustomiser_tabs2_' + fieldsetId;

                s += '<li class="' + liClass + '" data-tab="' + id + '">' +
                         '<a href="javascript:void(0)" class="' + aClass + '" id="' + aId + '" onclick="Blugento.DesignCustomiserSetup.prototype.setActiveTab(this, \'' + fieldsetId + '\', \'' + id + '\')">' + fieldsetName + '</a>' +
                     '</li>';
            });
        });

        if (s != '') {
            s = '<ul class="tabs-horiz" id="designcustomiser_tabs2" style="display: none;">' + s + '</ul>';
            $('edit_form').insert({
                before: s
            });
        }
    }
});

document.observe('dom:loaded', function() {
    new Blugento.DesignCustomiserSetup();
});
