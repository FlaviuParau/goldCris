CustomAttrRelation = Class.create();
CustomAttrRelation.prototype = {
    shownElements: [],
    indexedElements: [],
    amcustomerattrRelations: '',
    area: 'billing',

    initialize: function (relationData, area) {
        this.amcustomerattrRelations = relationData;
        this.area = area;
    },

    /*
     * Hide element and its label
     */
    amcustomerattr_hide_element: function (id) {
        if ($(id)) {
            if (navigator.appVersion.indexOf("MSIE 7.") == -1) {
                if ($(id).readAttribute('class') == 'field-row') {
                    $(id).hide();
                } else {
                    $(id).up(3).hide();
                }
            }
            else {
                $(id).hide();
                classAttr = 'label[for=' + id + ']';
                $$(classAttr).each(function (el) {
                    el.up(3).hide();
                });
            }
        }
        this.hide_all_childs(id);
    },

    /*
     * Show element and its label
     */
    amcustomerattr_show_element: function (id) {
        if ($(id)) {
            if (navigator.appVersion.indexOf("MSIE 7.") == -1) {
                if ($(id).readAttribute('class') == 'field-row') {
                    $(id).show();
                } else {
                    $(id).up(3).show();
                }
            } else {
                $(id).show();
                classAttr = 'label[for=' + id + ']';
                $$(classAttr).each(function (el) {
                    el.up(3).show();
                });
            }

            var options = this.amcustomerattr_find_elements(id);
            if (options.length > 0) {
                if($(id).readAttribute('class').indexOf('select') != -1) {
                    element = $(id);
                    this.amcustomerattr_manage_dep(element);
                } else {
                    options.each(function (item) {
                        element = $(id + '___' + item.value);
                        this.amcustomerattr_manage_dep(element);
                    });
                }
            }
        }
    },

    /*
     * Hide childs elements and them labels
     */
    hide_all_childs: function (id) {
        // Find dependents elements
        var dep = this.amcustomerattr_find_elements(id);

        // Iterate throw elements and show required elements
        dep.each(function (el) {
            this.amcustomerattr_hide_element(el.code);
        });
    },

    /*
     * Manage dependencies.
     * Hide or show dependent attribute
     */
    amcustomerattr_manage_dep: function (element) {
        if (element) {
            var elementId = element.id;

            if (element.id.indexOf('___') > 0) {
                elementId = element.id.substr(0, element.id.search('___'));
            }

            if (element.readAttribute('rel')) {
                elementId = element.readAttribute('rel');
            }

            // Find dependents elements
            var dep = this.amcustomerattr_find_elements(elementId);

            if (element.type == 'radio') {
                this.hide_all_childs(elementId);
            }

            // Iterate throw elements and show required elements
            var thisRef = this;
            dep.each(function (el) {
                var fullElementCode = thisRef.area + el.code;
                if ($(fullElementCode)) {
                    // Checkboxes and radio
                    if (element.checked == false) {
                        if (thisRef.must_hide(elementId, dep)) {
                            thisRef.amcustomerattr_hide_element(fullElementCode);
                        }
                    } else if (element.checked) {
                        if (element.getValue().indexOf(el.value) >= 0) {
                            thisRef.amcustomerattr_show_element(fullElementCode);
                            thisRef.indexedElements.push(fullElementCode);
                        } else if (indexedElements.indexOf(fullElementCode) < 0) {
                            if (thisRef.must_hide(elementId, dep)) {
                                thisRef.amcustomerattr_hide_element(fullElementCode);
                            }
                        }
                    } else {
                        // Multiselect and select
                        if (element.getValue() == el.value) {
                            thisRef.amcustomerattr_show_element(fullElementCode);
                            thisRef.indexedElements.push(fullElementCode);
                        } else if (thisRef.indexedElements.indexOf(fullElementCode) < 0) {
                            thisRef.amcustomerattr_hide_element(fullElementCode);
                        }
                    }

                }
            });
            this.indexedElements = [];
        }
    },

    must_hide: function (elementId, dep) {
        var hide = true;
        dep.each(function (el) {
            element = $(elementId + '___' + el.value);
            if (element.checked == true) {
                hide = false;
            }
        });
        return hide;
    },

    /*
     * Listen elements
     */
    amcustomerattr_listen_element: function (id) {
        if ($(id)) {
            var thisRef = this;
            $(id).observe('change', function (event) {
                var element = Event.element(event);
                thisRef.amcustomerattr_manage_dep(element);
            });
        }
    },

    /*
     * Get dependents element for elementId
     */
    amcustomerattr_find_elements: function (elementId) {
        var elements = [];
        var thisRef = this;
        this.amcustomerattrRelations.items.each(function (item) {
            if (thisRef.area + item.parent_code == elementId) {
                var el = {
                    'code': item.dependent_code,
                    'value': item.option_id
                };
                elements.push(el);
            }
        });
        return elements;
    },

    /*
     * Hide dependent elements
     * Listen for changes
     */
    listener_changes: function () {
        var relations = (this.amcustomerattrRelations.hasOwnProperty('items')) ? this.amcustomerattrRelations.items : [];
        for (var index = 0; index < relations.length; index++) {
            if (relations[index]) {
                var relation = relations[index];
                var optionId = relation['option_id'];
                var parentCode = this.area + relation['parent_code'];
                var dependentCode = this.area + relation['dependent_code'];
                var parent = $(parentCode);
                if (parent && $(parent).visible()) {
                    if (parent.value == optionId) {
                        this.amcustomerattr_show_element(dependentCode);
                        this.shownElements.push(dependentCode);
                    }
                }

                parent = $(parentCode + '___' + optionId);
                if (parent && $(parent).visible()) {
                    if (parent.value == optionId && parent.readAttribute('checked') == 'checked') {
                        this.amcustomerattr_show_element(dependentCode);
                        this.shownElements.push(dependentCode);
                    }
                }

                if (this.shownElements.indexOf(dependentCode) < 0) {
                    this.amcustomerattr_hide_element(dependentCode);
                }

                this.amcustomerattr_listen_element(parentCode + '___' + optionId);
                this.amcustomerattr_listen_element(parentCode);
            }
        }
    }
};
