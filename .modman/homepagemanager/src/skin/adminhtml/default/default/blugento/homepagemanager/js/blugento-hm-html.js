var BlugentoHMHTML = {
    cssClasses: {
        col12: [
            'col-12'
        ],
        col6x6: [
            'col-6 col-xxs-12',
            'col-6 col-xxs-12'
        ],
        col4x8: [
            'col-4 col-s-6 col-xxs-12',
            'col-8 col-s-6 col-xxs-12'
        ],
        col3x9: [
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-9 col-s-8 col-xs-6 col-xxs-12'
        ],
        col8x4: [
            'col-8 col-s-6 col-xxs-12',
            'col-4 col-s-6 col-xxs-12'
        ],
        col9x3: [
            'col-9 col-s-8 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12'
        ],
        col4x4x4: [
            'col-4 col-s-6 col-xxs-12',
            'col-4 col-s-6 col-xxs-12',
            'col-4 col-s-6 col-xxs-12'
        ],
        col3x6x3: [
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-6 col-s-4 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12'
        ],
        col6x3x3: [
            'col-6 col-s-4 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12'
        ],
        col3x3x6: [
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-6 col-s-4 col-xs-6 col-xxs-12'
        ],
        col3x3x3x3: [
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12',
            'col-3 col-s-4 col-xs-6 col-xxs-12'
        ]
    },

    rowClass: 'row',

    /**
     * Initialize
     */
    init: function() {

    },

    /**
     * Builds the html and json code for a row
     *
     * @param string type Row class - number of columns
     * @returns {*}
     */
    getRowHTML: function(type) {

        var item = this.cssClasses[type];
        if (typeof item === 'undefined') {
            return false;
        }

        var len = item.length,
            nodeId = bgGenerateUid(),
            childId;
        var htm = '<li id="row-container-' +nodeId + '">' +
                    '<div class="hm-row-handler">' + jQuery('#hm-row-right').html() + '</div>' +
                    '<div id="' + nodeId + '" class="' + this.rowClass + ' hm-row-container">';
        var jsn = {
            id: nodeId,
            nodes: []
        };

        for (var i = 0; i<len; i++) {
            childId = bgGenerateUid();
            htm += '<div id="' + childId + '" class="' + item[i] + '">' +
                        '<div id="' + childId + '-content" class="hm-col-content"></div>' +
                        '<div id="' + childId + '-text" style="display:none"></div>' +
                        '<div id="' + childId + '-type" style="display:none"></div>' +
                        '<div id="' + childId + '-params" style="display:none"></div>' +
                        jQuery('#hm-col-footer').html() +
                    '</div>';
            jsn.nodes.push({
                id: childId,
                full_width: 0,
                class: item[i],
                text: '',
                type: '',
                params: ''
            });
        }
        htm += '</div></li>';

        return {
            html: htm,
            json: jsn
        };
    },

    /**
     * Parse html to json
     *
     * @param string elmId
     * @returns {Array}
     */
    parseHTML: function(elmId) {

        var jsn = [];

        jQuery('#' + elmId + ' li').each(function() {
            var row = jQuery(this).children('div.hm-row-container');
            if (!row.length) {
                return;
            }
            var id = row.attr('id');

            var rowH = jQuery(this).children('div.hm-row-handler');
            if (!rowH.length) {
                return;
            }
            var fw = rowH.find('.set-full-width').is(':checked');
            fw = fw ? 1 : 0;
            var rowJsn = {
                id: row.attr('id'),
                full_width: fw,
                nodes: []
            };
            row.children('div').each(function() {
                var o = jQuery(this);
                var colId = o.attr('id');
                var params = jQuery.trim(jQuery('#' + colId + '-params').html());
                var params = params.replace(/<\/?[^>]+>/gi, '');
                if (params != '') {
                    params = JSON.parse(params);
                }
                var node = {
                    id: colId,
                    class: o.attr('class'),
                    text: jQuery('#' + colId + '-text').html(),
                    params: params,
                    type: jQuery('#' + colId + '-type').html()
                };
                rowJsn.nodes.push(node);
            });
            jsn.push(rowJsn);
        });

        return jsn;
    }

};