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
 * @package     Blugento_Localizer
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Localizer_FrontendController extends Mage_Core_Controller_Front_Action
{
    /**
     * Shows the given agreement
     */
    public function agreementsAction()
    {
        $this->loadLayout();
        if ($id = $this->getRequest()->getParam('id')) {
            /* @var $processor Mage_Cms_Model_Template_Filter */
            $processor = Mage::getModel('cms/template_filter');

            /* @var $agreement Mage_Checkout_Model_Agreement */
            $agreement = Mage::getModel('checkout/agreement')->load($id);

            $headBlock = $this->getLayout()->getBlock('head');
            $headBlock->setTitle(
                $headBlock->escapeHtml($processor->filter($agreement->getCheckboxText()))
            );

            $agreementText = $agreement->getContent();
            if (!$agreement->getIsHtml()) {
                $agreementText = $headBlock->escapeHtml($agreementText);
            }

            $agreeBlock = $this->getLayout()->getBlock('agreement');
            $agreeBlock->setText($processor->filter($agreementText));
        }
        $this->renderLayout();
    }
}
