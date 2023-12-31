<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

class Simtech_Searchanise_Adminhtml_SearchaniseController extends Mage_Adminhtml_Controller_Action
{

    const PARAM_USE_FULL_FEED  = 'snize_use_full_feed';

    protected function _initAction()
    {
        $this->_setActiveMenu('catalog/searchanise');

        return $this;
    }

    /*
     * dashboard
     */
    public function indexAction()
    {
        $this->loadLayout()->_initAction();

        $this->_addContent($this->getLayout()->createBlock('core/text', 'inner-wrap-start')->setText('<div id="searchanise-settings-wrapper">'));
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/template')
                ->setTemplate('searchanise/dashboard.phtml'));

        $this->_addContent($this->getLayout()->createBlock('core/text', 'inner-wrap-end')->setText('</div>'));

        $this->renderLayout();
    }

    /*
     * options
     */
    public function optionsAction()
    {
        $useFullFeed = $this->getRequest()->getParam(self::PARAM_USE_FULL_FEED);
        if ($useFullFeed != '') {
            Mage::helper('searchanise/ApiSe')->setUseFullFeed($useFullFeed == 'true' ? true : false);
        }
    }

    /*
     * resync
     */
    public function resyncAction()
    {
        if (Mage::helper('searchanise/ApiSe')->getStatusModule() == 'Y') {
            if (Mage::helper('searchanise/ApiSe')->signup() != true) {

                $this->_redirect(Mage::helper('searchanise/ApiSe')->getSearchaniseLink());
            }
            Mage::helper('searchanise/ApiSe')->queueImport();

            $this->_redirect(Mage::helper('searchanise/ApiSe')->getSearchaniseLink());
        }

        return $this;
    }

    /*
     * Signup
     */
    public function signupAction()
    {
        if (Mage::helper('searchanise/ApiSe')->getStatusModule() == 'Y') {
            if (Mage::helper('searchanise/ApiSe')->signup() == true) {
                Mage::helper('searchanise/ApiSe')->queueImport();
            }

            $this->_redirect(Mage::helper('searchanise/ApiSe')->getSearchaniseLink());
        }

        return $this;
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/searchanise');
    }
}
