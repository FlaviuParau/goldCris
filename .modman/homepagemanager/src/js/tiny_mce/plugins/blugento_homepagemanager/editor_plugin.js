/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2015 MindMagnet S.R.L.
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

tinyMCE.addI18n({en:{
    blugento_homepagemanager:
    {
        insert_layout : "Insert Layout"
    }
}});

(function() {
    tinymce.create('tinymce.plugins.Blugento_homepagemanagerPlugin', {
        /**
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addCommand('mceBlugentohomepagemanager', function() {
                var pluginSettings = ed.settings.magentoPluginsOptions.get('blugento_homepagemanager');
                BlugentoHomepageManagerPlugin.setEditor(ed);
                BlugentoHomepageManagerPlugin.setUrl(url);
                BlugentoHomepageManagerPlugin.loadChooser(null);
            });

            // Register Widget plugin button
            ed.addButton('blugento_homepagemanager', {
                title : 'blugento_homepagemanager.insert_layout',
                cmd : 'mceBlugentohomepagemanager',
                image : url + '/img/blugento__add.png'
            });
        },

        getInfo : function() {
            return {
                longname : 'Blugento Homepage Manager Plugin for TinyMCE 3.x',
                author : 'MindMagnet Team',
                authorurl : 'http://mindmagnetsoftware.com',
                infourl : 'http://mindmagnetsoftware.com',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('blugento_homepagemanager', tinymce.plugins.Blugento_homepagemanagerPlugin);
})();
