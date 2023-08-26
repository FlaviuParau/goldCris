var BlugentoHM = Class.create();

BlugentoHM.prototype = {

    _options: {},

    _elm: null,
    _elmId: 'hm-layout-container',

    _dom: [],

    _activeCol: null,
    _activeRow: null,

    _activeWidgetOptionValues: null,
    _activeWidgetEl: null,

    /**
     * Initialize
     *
     * @param object options Initialization options.
     * @return void
     */
    initialize: function(options)
    {
        this._elm = jQuery('#' + this._elmId);
        this._options = Object.extend(this._options, options || {});

        this.prepareEvents();

        BlugentoHMHTML.init();
    },

    /**
     * Setup the page events
     *
     * @return void
     */
    prepareEvents: function()
    {
        jQuery(document).ready(function() {

            // Parse html to get initial json
            bluHomepageManager.setDomJSON(BlugentoHMHTML.parseHTML(bluHomepageManager._elmId));

            // Insert layout buttons click event
            jQuery('.hm-insert-layout').click(function() {
                bluHomepageManager._insertLayoutDialog(jQuery(this).attr('data-layout'));
            });

            // Initialize layout chooser dialogs
            jQuery('.hm-layout-dialog').dialog({
                autoOpen: false,
                modal: true
            });

            // Initialize sortable layout rows
            bluHomepageManager._initSortableItems();

            // Add save action
            jQuery('#hm-save-layout').click(function() {
                bluHomepageManager.saveLayout();
            });

            // Initialize row and cols events
            bluHomepageManager.prepareRowEvents();
        });
    },

    /**
     * Initialize row and cols events
     *
     * @return void
     */
    prepareRowEvents: function()
    {
        // Initialize delete row events
        jQuery('.hm-delete').unbind('click').click(function() {
            var id = jQuery(this).parent().parent().attr('id');

            bluHomepageManager._deleteRow(id);
        });

        // Initialize clear col content events
        jQuery('.hm-clear').unbind('click').click(function() {
            var id = jQuery(this).parent().parent().attr('id');

            bluHomepageManager._clearCol(id);
        });

        // Initialize insert widget events
        jQuery('.hm-widget').unbind('click').click(function() {
            var id = jQuery(this).parent().parent().attr('id');

            bluHomepageManager._openWidgetPopup(id);
        });

        // Initialize set full width event
        jQuery('.set-full-width').unbind('click').click(function() {
            bluHomepageManager._setFullWidth(this);
        });
    },

    /**
     * Option getter.
     *
     * @param string key The option key.
     * @return mixed
     */
    get: function(key)
    {
        if (typeof this._options[key] != 'undefined') {
            return this._options[key];
        }

        return null;
    },

    /**
     * Option setter.
     *
     * @param string key   The option key.
     * @param mixed  value The option value.
     * @return BlugentoHM
     */
    set: function(key, value)
    {
        this._options[key] = value;

        return this;
    },

    /**
     * Set don json content
     *
     * @param jsn
     */
    setDomJSON: function(jsn)
    {
        this._dom = jsn;
        this.updateJson();
    },

    /**
     * Reload the dom json after sorting
     *
     * @return void
     */
    reloadDomJson: function()
    {
        this._dom = BlugentoHMHTML.parseHTML(bluHomepageManager._elmId);
        this.updateJson();
    },

    /**
     * Update dom json node text content
     *
     * @return void
     */
    updateDomJson: function(rowId, colId, content, type, params)
    {
        var _dm = this._dom;

        for (var i in _dm) {
            if (_dm[i].id == rowId) {
                for (var j in _dm[i].nodes) {
                    if (_dm[i].nodes[j].id == colId) {
                        _dm[i].nodes[j].text = content;
                        _dm[i].nodes[j].type = type;
                        _dm[i].nodes[j].params = params;
                        break;
                    }
                }
                break;
            }
        }

        this._dom = _dm;
        this.updateJson();
    },

    updateJson: function()
    {
        jQuery('#form_values').val(JSON.stringify(this._dom));
    },

    /**
     * Insert new layout row
     *
     * @param string type The type of row - number of columns
     * @return bool
     */
    insertLayout: function(type)
    {
        var arr = type.split('-');
        if (arr.length != 2) {
            jQuery('.hm-layout-dialog').dialog('close');
            return false;
        }

        var row = arr[0], col = arr[1];
        jQuery('#hm-layout-dialog-' + row).dialog('close');

        var obj = BlugentoHMHTML.getRowHTML(col);
        if (obj === false) {
            return false;
        }

        var htm = obj.html, jsn = obj.json;
        jQuery(htm).appendTo('#' + this._elmId);
        this._dom.push(jsn);

        // Initialize sortable layout rows
        this._initSortableItems();

        // Initialize row and cols events
        this.prepareRowEvents();
    },

    /**
     * Save layout
     *
     * @return void
     */
    saveLayout: function()
    {
        jQuery('#form_values').val(JSON.stringify(this._dom));
        jQuery('#hm-form-layout').submit();
    },

    /**
     * Insert widget
     */
    insertWidget: function(content, widgetType, params)
    {
        var _ac = this._activeCol,
            _ar = this._activeRow;

        jQuery.ajax({
            url: jQuery('#ajax_widget_preview_url').val(),
            data: {
                type: widgetType,
                params: params,
                isAjax: 1,
                form_key: window.FORM_KEY
            },
            method: 'POST'
        }).done(function(data) {
            jQuery('#' + _ac + '-content').html(data);
            jQuery('#' + _ac + '-text').html(content);
            jQuery('#' + _ac + '-type').html(widgetType);
            jQuery('#' + _ac + '-params').html(params);
            jQuery('.hm-widget', '#' + _ac).removeClass('hm-fbtn--widget-add').addClass('hm-fbtn--widget-edit');
            jQuery('.hm-clear', '#' + _ac).removeClass('hm-fbtn--clear-disabled');

            bluHomepageManager.updateDomJson(_ar, _ac, content, widgetType, params);

            jQuery('#hm-widget-dialog').dialog('close');
        });
    },

    /**
     * Build selected widget options
     *
     * @return bool
     */
    getWidgetOptions: function()
    {
        var _ac = this._activeCol,
            _ar = this._activeRow;

        var _e = jQuery('#' + _ac + '-text');
        if (_e != undefined) {
            var widgetCode = _e.html();
            if (widgetCode.indexOf('{{widget') != -1) {
                this._activeWidgetOptionValues = new Hash({});
                widgetCode.gsub(/([a-z0-9\_]+)\s*\=\s*[\"]{1}([^\"]+)[\"]{1}/i, function(match){
                    if (match[1] == 'type') {
                        this._activeWidgetEl = match[2];
                    } else {
                        this._activeWidgetOptionValues.set(match[1], match[2]);
                    }
                }.bind(this));

                return true;
            }
        }

        this._activeWidgetEl = null;
        this._activeWidgetOptionValues = null;

        return false;
    },

    /**
     * Open dialog for inserting new layout row
     *
     * @param string type The type of row - number of columns
     * @private
     * @return void
     */
    _insertLayoutDialog: function(type)
    {
        jQuery('.hm-layout-dialog').dialog('close');
        jQuery('#hm-layout-dialog-' + type).dialog('open');
    },

    /**
     * Initialize sortable layout rows
     *
     * @private
     * @return void
     */
    _initSortableItems: function()
    {
        jQuery('#' + this._elmId).sortable({
            placeholder: 'ui-state-highlight',
            update: function(event, ui) {
                bluHomepageManager.reloadDomJson();
            }
        });
    },

    /**
     * Delete layout row
     *
     * @param string id Row id
     * @private
     * @return void
     */
    _deleteRow: function(id)
    {
        if (confirm(Translator.translate('Are you sure you want to delete this row?'))) {
            jQuery('#'  + id).find('.hm-clear').trigger('click');

            jQuery('#' + id).fadeOut(400, function () {
                jQuery(this).remove();

                //bluHomepageManager.reloadDomJson();
            });
        }
    },

    /**
     * Clear col content
     *
     * @param string id The col id
     * @private
     * @return void
     */
    _clearCol: function(id)
    {
        jQuery('#' + id + '-content').html('');
        jQuery('#' + id + '-text').html('');
        jQuery('#' + id + '-type').html('');
        jQuery('#' + id + '-params').html('');
        jQuery('.hm-widget', '#' + id).removeClass('hm-fbtn--widget-edit').addClass('hm-fbtn--widget-add');
        jQuery('.hm-clear', '#' + id).addClass('hm-fbtn--clear-disabled');

        this.reloadDomJson();
    },

    /**
     * Open widget popup
     *
     * @param string id
     * @private
     */
    _openWidgetPopup: function(id)
    {
        this._activeCol = id;
        this._activeRow = jQuery('#' + id).parent().attr('id');

        jQuery.ajax({
            url: jQuery('#editor_url').val(),
            data: {
                isAjax: 1,
                form_key: window.FORM_KEY
            },
            method: 'POST'
        }).done(function(data) {
            jQuery('#hm-widget-dialog').html(data);
            jQuery('#hm-widget-dialog').dialog({
                minWidth: 600,
                modal: true,
                create: function( event, ui ) {
                    bluHomepageManager.initWidgetOptionValues();
                }
            });
        });

        return;
    },

    /**
     * Set row full width
     *
     * @param elem
     * @private
     */
    _setFullWidth: function(elem)
    {
        bluHomepageManager.reloadDomJson();
    },

    /**
     * Assign widget options values when existing widget in column
     *
     * @returns {boolean}
     * @private
     */
    initWidgetOptionValues: function()
    {
        var _ac = this._activeCol;

        if (!_ac) {
            this._activeWidgetOptionValues = null;
            this._activeWidgetEl = null;
            return false;
        }

        var _e = jQuery('#' + _ac + '-type');
        if (_e != undefined) {
            var widgetCode = _e.html();
            if (widgetCode) {
                jQuery('#select_widget_type').val(_e.html());
                return true;
            }
        }

        this._activeWidgetOptionValues = null;
        this._activeWidgetEl = null;

        return false;
    }

};

var bluHomepageManager = new BlugentoHM();

/**
 * Overwrite update content in WysiwygWidget class
 *
 * @param string content
 */
WysiwygWidget.Widget.prototype.updateContent = function(content) {

    var formElements = [];
    var i = 0;
    Form.getElements($(this.formEl)).each(function(e) {
        if(!e.hasClassName('skip-submit')) {
            formElements[i] = {
                name: e.name,
                value: e.getValue()
            };
            i++;
        }
    });

    bluHomepageManager.insertWidget(content, this.widgetEl.value, formElements);
};

/**
 * Overwrite assign widget options values when existing widget selected in WYSIWYG
 */
WysiwygWidget.Widget.prototype.initOptionValues = function() {

    bluHomepageManager.getWidgetOptions();

    if (bluHomepageManager._activeWidgetEl && bluHomepageManager._activeWidgetOptionValues) {
        this.widgetEl.value = bluHomepageManager._activeWidgetEl;
        this.optionValues = bluHomepageManager._activeWidgetOptionValues;
        this.loadOptions();
    }
};

/**
 * Overwrite show widget description
 */
WysiwygWidget.Widget.prototype._showWidgetDescription = function() {

    var noteCnt = this.widgetEl.next().down('small');
    var descrCnt = $('widget-description-' + this.widgetEl.selectedIndex);
    if(noteCnt != undefined) {
        var description = (descrCnt != undefined ? descrCnt.innerHTML : '');
        noteCnt.update(description);
    }
};
