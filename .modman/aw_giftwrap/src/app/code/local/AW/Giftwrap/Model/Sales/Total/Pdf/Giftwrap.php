<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Giftwrap
 * @version    1.1.1
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Giftwrap_Model_Sales_Total_Pdf_Giftwrap extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Return totals for AW_Giftwrap
     *
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $label = Mage::helper('aw_giftwrap')->__($this->getTitle());
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        return array(
            'aw_giftwrap' => array(
                'amount'    => $amount,
                'label'     => $label,
                'font_size' => $fontSize,
            )
        );
    }

    /**
     * Retrieve Giftwrap amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getOrder()->getAwGiftwrapAmount();
    }
}