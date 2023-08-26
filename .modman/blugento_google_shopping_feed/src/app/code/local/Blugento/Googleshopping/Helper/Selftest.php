<?php

class Blugento_Googleshopping_Helper_Selftest extends Blugento_Googleshopping_Helper_Data
{

    const SUPPORT_URL = 'https://www.blugento.eu/help/googleshopping/googleshopping-selftest-results';

    /**
     *
     */
    public function runFeedTests()
    {
        $result = array();

        /** @var Blugento_Googleshopping_Model_Googleshopping $model */
        $model = Mage::getModel("googleshopping/googleshopping");

        $enabled = Mage::getStoreConfig('googleshopping/general/enabled');
        if ($enabled) {
            $result[] = $this->getPass('Module Enabled');
        } else {
            $result[] = $this->getFail('Module Disabled');
        }

        $moduleDisabled = $this->getUncachedConfigValue('advanced/modules_disable_output/Blugento_Googleshopping');
        if ($moduleDisabled) {
            $result[] = $this->getFail('Module Output Disabled', '#module-output');
        }

        $flatProduct = Mage::getStoreConfig('catalog/frontend/flat_catalog_product');
        $bypassFlat = Mage::getStoreConfig('googleshopping/generate/bypass_flat');

        if ($flatProduct) {
            if ($bypassFlat) {
                $result[] = $this->getNotice('Catalog Product Flat bypass is enabled');
            } else {
                $result[] = $this->getPass('Catalog Product Flat is enabled');

                $storeId = $this->getStoreIdConfig();
                $nonFlatAttributes = $this->checkFlatCatalog($model->getFeedAttributes($storeId, 'flatcheck'));

                if (!empty($nonFlatAttributes)) {
                    $atts = '<i>' . implode($nonFlatAttributes, ', ') . '</i>';
                    $url = Mage::helper("adminhtml")->getUrl('adminhtml/googleshopping/addToFlat');
                    $msg = $this->__('Missing Attribute(s) in Catalog Product Flat: %s', $atts);
                    $msg .= '<br/> ' . $this->__(
                        '<a href="%s">Add</a> attributes to Flat Catalog or enable "Bypass Flat Product Tables"',
                        $url
                    );
                    $result[] = $this->getFail($msg, '#missingattributes');
                }
            }
        } else {
            $result[] = $this->getNotice('Catalog Product Flat is disabled');
        }

        $flatCategoy = Mage::getStoreConfig('catalog/frontend/flat_catalog_category');
        if ($flatCategoy) {
            $result[] = $this->getPass('Catalog Category Flat is enabled');
        } else {
            $result[] = $this->getNotice('Catalog Category Flat is disabled');
        }

        if ($lastRun = $this->checkMagentoCron()) {
            if ((time() - strtotime($lastRun)) > 3600) {
                $msg = $this->__('Magento cron not seen in last hour (last: %s)', $lastRun);
                $result[] = $this->getFail($msg, '#cron');
            } else {
                $msg = $this->__('Magento cron seems to be running (last: %s)', $lastRun);
                $result[] = $this->getPass($msg);
            }
        } else {
            $result[] = $this->getFail('Magento cron not setup');
        }

        return $result;
    }

    /**
     * @param        $msg
     * @param string $link
     *
     * @return string
     */
    public function getPass($msg, $link = null)
    {
        return $this->getHtmlResult($msg, 'pass', $link);
    }

    /**
     * @param        $msg
     * @param        $type
     * @param string $link
     *
     * @return string
     */
    public function getHtmlResult($msg, $type, $link)
    {
        $format = null;

        if ($type == 'pass') {
            $format = '<span class="googleshopping-success">%s</span>';
        }

        if ($type == 'fail') {
            $format = '<span class="googleshopping-error">%s</span>';
        }

        if ($type == 'notice') {
            $format = '<span class="googleshopping-notice">%s</span>';
        }

        if ($format) {
            return sprintf($format, Mage::helper('googleshopping')->__($msg));
        }
    }

    /**
     * @param        $msg
     * @param string $link
     *
     * @return string
     */
    public function getFail($msg, $link = null)
    {
        return $this->getHtmlResult($msg, 'fail', $link);
    }

    /**
     * @param        $msg
     * @param string $link
     *
     * @return string
     */
    public function getNotice($msg, $link = null)
    {
        return $this->getHtmlResult($msg, 'notice', $link);
    }

    /**
     *
     */
    public function checkMagentoCron()
    {
        $tasks = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToSelect('finished_at')
            ->addFieldToFilter('status', 'success');

        $tasks->getSelect()
            ->limit(1)
            ->order('finished_at DESC');

        return $tasks->getFirstItem()->getFinishedAt();
    }
}
