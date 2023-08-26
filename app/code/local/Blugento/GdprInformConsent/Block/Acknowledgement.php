<?php
/**
 *
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
 * @package     Blugento_GdprInformConsent
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GdprInformConsent_Block_Acknowledgement extends Mage_Core_Block_Template
{
    /**
     * Return Privacy Policy page content
     *
     * @return string
     */
    public function getPrivacyPolicyContent()
    {
        /** @var Mage_Cms_Model_Page $cmsPage */
        $cmsPage = Mage::getModel('cms/page');

        $identifier = 'politica-de-confidentialitate';
        $pageId = $cmsPage->checkIdentifier($identifier, Mage::app()->getStore()->getId());

        $content = '';
        if ($pageId) {
            $page = $cmsPage->load($pageId);
            $content = $page->getContent();
        }

        if (!$content) {
            $content = '<a target="_blank" href="' . Mage::getBaseUrl() . '/politica-de-confidentialitate">' . $this->__('Show Privacy Policy') . '</a>';
        }

        return $content;
    }
}