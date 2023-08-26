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

class Blugento_Campaign_Helper_Data extends Mage_Core_Helper_Abstract
{
    const CACHE_TAG = 'block_html';

    /**
     * Format campaign code
     *
     * @param string $code
     * @param string $name
     * @return string
     */
    public function processCode($code, $name)
    {
        if (!$code || $code == '') {
            $code = $name;
        }

        $code = preg_replace("/[^A-Za-z0-9 ]/", '', $code);

        $code = strtolower($code);
        $code = str_replace(' ', '', $code);

        return $code;
    }

    /**
     * Create campaign shortcode
     *
     * @param int $campaign
     * @param string $layout
     * @return string
     */
    public function createShortcode($campaign, $layout)
    {
        if ($layout == 'two_columns_campaign_category_ajax') {
            $shortcode = '{{block type="blugento_campaign/catalog_product_ajax_list" ';
            $shortcode .= 'campaign_id="' . $campaign . '" ';
            $shortcode .= 'template="blugento_campaign/product/ajax/list.phtml"}}';
        } else {
            $shortcode = '{{block type="blugento_campaign/catalog_product_list" ';
            $shortcode .= 'campaign_id="' . $campaign . '" ';
            $shortcode .= 'template="blugento_campaign/product/list.phtml"}}';
        }

        return $shortcode;
    }

    /**
     * Check if the campaign is active
     *
     * @param string $start
     * @param string $end
     * @return bool
     */
    public function isCampaignActive($start, $end)
    {
        $start = strtotime($start);
        $end = strtotime($end);
        $now = Mage::getModel('core/date')->timestamp(time());

        if ($start && $end && $start < $now && $end > $now) {
            return true;
        }

        if ($start && !$end && $start < $now) {
            return true;
        }

        if (!$start && $end && $end > $now) {
            return true;
        }

        return false;
    }
}