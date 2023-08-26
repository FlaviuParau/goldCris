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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Model_Campaign extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_campaign/campaign');
    }

    /**
     * Check if a campaign with the same code already exist
     *
     * @param string $code
     * @return bool
     */
    public function alreadyExist($code)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('id', array('neq' => $this->getId()));

        $exist = false;
        if ($collection->getSize() > 0) {
            $exist = true;
        }

        return $exist;
    }

    /**
     * Check if another campaign is active
     *
     * @return bool
     */
    public function isAnotherCampaignActive()
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('status', 1)
            ->addFieldToFilter('id', array('neq' => $this->getId()));

        $isActive = false;
        if ($collection->getSize() > 0) {
            $isActive = true;
        }

        return $isActive;
    }

    /**
     * Set campaign layout
     *
     * @param string $cmsPage
     * @param string $layout
     */
    public function setCampaignsLayout($cmsPage, $layout)
    {
        $sql = 'UPDATE blugento_campaign
                SET layout = "' . $layout . '"
                WHERE cms_page LIKE "' . $cmsPage . '"';

        try {
            $this->_getConnection('write')->query($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return campaign associated CMS Page for redirect if the campaign is active
     *
     * @return bool|string
     */
    public function getActiveCampaign()
    {
        /** @var Blugento_Campaign_Helper_Data $helper */
        $helper = Mage::helper('blugento_campaign');

        $this->load(1, 'status');

        if (!$helper->isCampaignActive($this->getStartDate(), $this->getEndDate())) {
            $this->clearInstance();
        }

        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _clearData()
    {
        $this->_data = array();
        return parent::_clearData();
    }

    /**
     * Retrieve connection
     *
     * @param null|string $type
     * @return mixed
     */
    private function _getConnection($type = null)
    {
        if ($type == 'write') {
            return $this->_getResourceConnection()->getConnection('core_write');
        } else {
            return $this->_getResourceConnection()->getConnection('core_read');
        }
    }

    /**
     * Get the resource model
     *
     * @return Mage_Core_Model_Abstract
     */
    private function _getResourceConnection()
    {
        return Mage::getSingleton('core/resource');
    }
}
