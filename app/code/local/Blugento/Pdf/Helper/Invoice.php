<?php
/**
 * @package   Blugento_Pdf
 * @copyright 2016 Blugento Team
 */

/**
 * Helper for invoice creation.
 *
 * @category  Blugento
 * @package   Blugento_Pdf
 * @author    Blugento Team <team@blugento.com>
 */
class Blugento_Pdf_Helper_Invoice extends Mage_Core_Helper_Abstract
{

    /**
     * Gets the notes for the shipping country of the given order.
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return array the notes for the shipping country of the given order - may be empty!
     */
    public function getShippingCountryNotes(Mage_Sales_Model_Order $order)
    {
        if (!$order->getIsVirtual()) {
            $shippingCountryId = $order->getShippingAddress()->getCountryId();
            $countryNotes = unserialize(Mage::getStoreConfig('sales_pdf/invoice/shipping_country_notes'));
            if($countryNotes) {
                $shippingCountryNotes = array();
                foreach ($countryNotes as $countryNote) {
                    if ($countryNote['country'] == $shippingCountryId) {
                        $shippingCountryNotes[] = $countryNote['note'];
                    }
                }

                return $shippingCountryNotes;
            }
        }

        return array();
    }

}
