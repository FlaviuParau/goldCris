<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Block_Adminhtml_Form_Layout
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Constructor
     *
     * Sets form urls for preview widget and loading widget editor window
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('blugento/form_layout.phtml');

        $this->setFormAction(Mage::getUrl('*/*/save'));
        $this->setFormAjaxWidgetPreviewUrl(Mage::getModel('adminhtml/url')->getUrl('*/adminhtml_homepagemanager/widgetpreview'));
        $this->setFormEditorUrl(Mage::getModel('adminhtml/url')->getUrl('*/widget/index'));
    }
}
