<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Amshopby_FilterController extends Mage_Adminhtml_Controller_Action
{
    // show grid
    public function indexAction()
    {
        $this->_checkRootCategories();
        $this->_checkOldTemplates();
        $this->_checkConflicts();
        $this->_checkEnemyNavigation();
        //$this->_checkMigrations();

        $this->loadLayout();
        $this->_setActiveMenu('catalog/amshopby');
        $this->_addBreadcrumb($this->__('Filters'), $this->__('Filters'));
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_filter'));
        $this->_title($this->__('Improved Navigation Filters'));
        $this->renderLayout();
    }

    protected function _checkRootCategories()
    {
        foreach (Mage::app()->getStores() as $store) {
            $category = Mage::getModel('catalog/category')
                ->setStoreId($store->getId())
                ->load($store->getRootCategoryId());

            if (!$category->getIsAnchor()) {
                $msg = $this->__('Please open Catalog > Manage Categories and set property "Is Anchor" to "Yes" for the store root category.');
                $this->_getSession()->addNotice($msg);
                break;
            }
        }
    }

    protected function _checkOldTemplates()
    {
        $frontendPath = rtrim(Mage::getBaseDir('design') . '/frontend', ' /');

        foreach (Mage::app()->getStores() as $store) {
            $package = Mage::getStoreConfig('design/package/name', $store);
            if (!$package)
                $package = 'default';

            $theme = Mage::getStoreConfig('design/theme/skin', $store);
            if (!$theme)
                $theme = 'default';

            $themePath = $frontendPath . '/' . trim($package, ' /') . '/' . trim($theme, ' /');
            $excessPath = $themePath . '/template/amshopby';

            if (is_dir($excessPath)) {
                $msg = $this->__('In case you need to modify the module templates please copy files from app/design/frontend/base/default/template/amasty/amshopby/  to your custom theme  app/design/frontend/PACKAGE/THEME/template/amasty/amshopby/');
                $this->_getSession()->addNotice($msg);
                break;
            }
        }
    }

    protected function _checkEnemyNavigation()
    {
        $modules = array(
            'Smartwave_Ajaxcatalog',
            'Vt_Ajaxfilter',
        );

        foreach ($modules as $moduleName) {
            if (Mage::helper('core')->isModuleEnabled($moduleName)) {
                $msg =
                    $this->__(
                        'There is a conflict(s) with %s extension.'
                        . ' We recommend turning off this extension via app/etc/modules/%s.xml file.',
                        $moduleName,
                        $moduleName
                    );
                $this->_getSession()->addNotice($msg);
            }
        }
    }

    protected function _checkConflicts()
    {
        $classes = array(
            'model' => array(
                'catalog/layer_filter_price',
                'catalog/layer_filter_decimal',
                'catalog/layer_filter_attribute',
                'catalog/layer_filter_category',
                'catalog/layer_filter_item',
                'catalogsearch/layer_filter_attribute',

            ),
            'block' => array(
                'catalog/layer_filter_attribute',
                'catalog/product_list_toolbar',
                'catalogsearch/layer_filter_attribute',
            ),
        );

        foreach ($classes as $type => $names) {
            foreach ($names as $name) {
                $name = Mage::getConfig()->getGroupedClassName($type, $name);

                if (substr($name, 0, 6) != 'Amasty') {
                    $parentClassName = get_parent_class($name);

                    if (false === strpos($parentClassName, 'Amasty')) {
                        $msg = $this->__('There is a conflict(s) with some other extension: class %s. If the module works incorrect, consider our <a href="http://amasty.com/installation-service.html">Installation Service</a>.', $name);
                        $this->_getSession()->addNotice($msg);
                        break(2);
                    }
                }
            }
        }
    }

    protected function _checkMigrations()
    {
        /** @var Amasty_Shopby_Helper_Migration $migrations */
        $migrations = Mage::helper('amshopby/migration');
        $real = $migrations->getRealStateVersion();
        $next = $migrations->getRealNextStateVersion();
        if ($next) {
            $link = Mage_Adminhtml_Helper_Data::getUrl("adminhtml/amshopby_filter/fixmigration", array('version' => $real));
            $msg = $this->__('Migration issue detected; <a href="'. $link .'">click here</a> to fix. Please contact Amasty Support in case issue not fixed.');
            $this->_getSession()->addError($msg);
        }
    }

    // load filters and their options
    public function newAction()
    {
        Mage::getResourceModel('amshopby/filter')->refreshFilters();
        $this->invalidateCache();
        $this->_redirect('*/*/');
    }

    public function fixmigrationAction()
    {
        $version = $this->getRequest()->getParam('version');

        if ($version) {
            /** @var Mage_Core_Model_Resource $resource */
            $resource = Mage::getSingleton('core/resource');
            $connection = $resource->getConnection('core_read');
            $table = $resource->getTableName('core/resource');
            $connection->update($table, array('version' => $version, 'data_version' => $version), 'code = "amshopby_setup"');
            Mage::app()->cleanCache();
            $this->_getSession()->addNotice('amshopby_setup resource version has been set to ' . $version);
        }

        $this->_redirect('*/*');
    }

    // edit filters (uses tabs)
    public function editAction()
    {
        $id     = (int) $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/filter')->load($id);

        if ($id && !$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Filter does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('amshopby_filter', $model);

        $this->loadLayout();

        $this->_setActiveMenu('catalog/amshopby');
        $this->_addContent($this->getLayout()->createBlock('amshopby/adminhtml_filter_edit'))
             ->_addLeft($this->getLayout()->createBlock('amshopby/adminhtml_filter_edit_tabs'));

        $this->_title($this->__('Edit Filter'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('amshopby/filter');
        $data = $this->getRequest()->getPost();
        if ($data) {
            if (isset($data['multistore'])) {
                foreach ($data['multistore'] as $key => $value) {
                    $data[$key] = serialize($value);
                }
            }

            $model->setData($data);
            $model->setId($id);

            if ($model->getData('display_type') == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_FROMTO) {
                $model->setData('from_to_widget', true);
            }

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $msg = Mage::helper('amshopby')->__('Filter properties have been successfully saved');
                if ($this->getRequest()->getParam('continue')){
                    Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } elseif($this->getRequest()->getParam('mapped_value_url')) {
                    $this->_redirect('*/amshopby_value/edit', array('id' => $this->getRequest()->getParam('mapped_value_url')));
                } elseif ($this->getRequest()->getParam('mapped_option')) {
                    $this->_redirect('*/amshopby_value/new', array('filter_id' => $model->getId()));
                }else {
                    Mage::getSingleton('adminhtml/session')->addSuccess($msg);
                    $this->_redirect('*/*');
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $id));
            }

            $this->invalidateCache();
            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Unable to find a filter to save'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('filter_id');
        if(!is_array($ids)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('amshopby')->__('Please select filter(s)'));
        }
        else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('amshopby/filter')->load($id);
                    $model->delete();
                    // todo delete values or add a foreign key
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            $this->invalidateCache();
        }
        $this->_redirect('*/*/');
    }

    protected function invalidateCache()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        $helper->invalidateCache();
    }

    //for ajax
    public function valuesAction()
    {
        $this->_prepareValues();
        $this->getResponse()->setBody($this->getLayout()
            ->createBlock('amshopby/adminhtml_filter_edit_tab_values')->toHtml());
    }

    public function valuesmappedAction()
    {
        $this->_prepareValues();
        $this->getResponse()->setBody($this->getLayout()
            ->createBlock('amshopby/adminhtml_filter_edit_tab_mapped')->toHtml());
    }

    protected function _prepareValues()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $model = Mage::getModel('amshopby/filter');

        if ($id) {
            $model->load($id);
        }

        Mage::register('amshopby_filter', $model);

        $options = array();
        $valuesCollection = Mage::getModel('amshopby/value')->getCollection()
            ->addFieldToFilter('filter_id', $model->getId());
        foreach ($valuesCollection as $filterValue) {
            /** @var  Amasty_Shopby_Model_Value $filterValue */
            $options[$filterValue->getId()] = $filterValue->getCurrentTitle();
        }
        Mage::register('amshopby_value_links', $options);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/amshopby/filters');
    }
}
