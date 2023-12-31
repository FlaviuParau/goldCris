<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rules
 */
class Amasty_Rules_Helper_Data extends Mage_Core_Helper_Abstract
{
    const TYPE_CHEAPEST             = 'the_cheapest';
    const TYPE_EXPENSIVE            = 'the_most_expensive';
  //  const WHOLE_CART                = 'whole_cart';
    const TYPE_EACH_M_AFT_N_PERC    = 'each_m_aft_n_perc';
    const TYPE_EACH_M_AFT_N_DISC    = 'each_m_aft_n_disc';
    const TYPE_EACH_M_AFT_N_FIX     = 'each_m_aft_n_fix';
    const TYPE_GROUP_N              = 'group_n';
    const TYPE_GROUP_N_DISC         = 'group_n_disc';
    const TYPE_XY_PERCENT           = 'buy_x_get_y_percent';
    const TYPE_XY_FIXED             = 'buy_x_get_y_fixed';
    const TYPE_XY_FIXDISC           = 'buy_x_get_y_fixdisc';
    const TYPE_XN_PERCENT           = 'buy_x_get_n_percent';
    const TYPE_XN_FIXED             = 'buy_x_get_n_fixed';
    const TYPE_XN_FIXDISC           = 'buy_x_get_n_fixdisc';
    const TYPE_AFTER_N_FIXED        = 'after_n_fixed';
    const TYPE_AFTER_N_DISC         = 'after_n_disc';
    const TYPE_AFTER_N_FIXDISC      = 'after_n_fixdisc';
    const TYPE_AMOUNT               = 'money_amount';
    const TYPE_SETOF_PERCENT        = 'setof_percent';
    const TYPE_SETOF_FIXED          = 'setof_fixed';
    const TYPE_FIXED                = 'fixed';
    const TYPE_EACH_N               = 'each_n';
    const TYPE_EACH_N_FIXDISC       = 'each_n_fixdisc';

    const ERROR_MESSAGE = 'If there is the following text it means that Amasty_Base is not updated to the latest 
                             version.<p>In order to fix the error, please, download and install the latest version of 
                             the Amasty_Base, which is included in all our extensions.
                        <p>If some assistance is needed, please submit a support ticket with us at: 
                        <a href="https://amasty.com/contacts/" target="_blank">https://amasty.com/contacts/</a>';

    public $passedItems = array();

    /**
     * Returns none max discount rules.
     * @return array
     */
    public function getNoneMaxDiscountRules()
    {
        return array('buy_x_get_y_fixdisc', 'buy_x_get_y_fixed', 'buy_x_get_n_fixdisc', 'buy_x_get_n_fixed',
            'each_n_fixdisc', 'fixed', 'each_m_aft_n_disc', 'each_m_aft_n_fix', 'group_n', 'after_n_fixdisc',
            'after_n_fixed', 'setof_fixed');
    }

    /**
     * Returns fixed rules.
     * @return array
     */
    public function getFixedRules() {
        return array('after_n_fixed', 'buy_x_get_n_fixed', 'buy_x_get_y_fixed', 'each_m_aft_n_fix', 'fixed');
    }

    public function addPassedItem($itemId)
    {
        $this->passedItems[] = $itemId;
    }

    public function getPassedItems()
    {
        return $this->passedItems;
    }

    public function getDiscountTypes($asOptions=false)
    {
        $types = array(
            self::TYPE_CHEAPEST             => $this->__('The Cheapest, also for Buy 1 get 1 free'),
            self::TYPE_EXPENSIVE            => $this->__('The Most Expensive'),
            self::TYPE_AMOUNT               => $this->__('Get discount for each amount spent'),

            self::TYPE_EACH_N               => $this->__('Percent Discount: each 2-d, 4-th, 6-th with 15% 0ff'),
            self::TYPE_EACH_N_FIXDISC       => $this->__('Fixed Discount: each 3-d, 6-th, 9-th with $15 0ff'),
            self::TYPE_FIXED                => $this->__('Fixed Price: each 5th, 10th, 15th for $49'),

            self::TYPE_EACH_M_AFT_N_PERC    => $this->__('Percent Discount: each 1st, 3rd, 5th with 15% 0ff after 5 items added to the cart'),
            self::TYPE_EACH_M_AFT_N_DISC    => $this->__('Fixed Discount: each 3d, 7th, 11th with $15 0ff after 5 items added to the cart'),
            self::TYPE_EACH_M_AFT_N_FIX     => $this->__('Fixed Price: each 5th, 7th, 9th for $89.99 after 5 items added to the cart'),

            self::TYPE_GROUP_N              => $this->__('Fixed Price: Each 5 items for $50'),
            self::TYPE_GROUP_N_DISC         => $this->__('Percent Discount: Each 5 items with 10% off'),

            self::TYPE_XY_PERCENT           => $this->__('Percent Discount: Buy X get Y Free'),
            self::TYPE_XY_FIXDISC           => $this->__('Fixed Discount:  Buy X get Y with $10 Off .'),
            self::TYPE_XY_FIXED             => $this->__('Fixed Price: Buy X get Y for $9.99'),

            self::TYPE_XN_PERCENT           => $this->__('Percent Discount: Buy X get Y Free'),
            self::TYPE_XN_FIXDISC           => $this->__('Fixed Discount:  Buy X get Y with $10 Off .'),
            self::TYPE_XN_FIXED             => $this->__('Fixed Price: Buy X get Y for $9.99'),

            self::TYPE_AFTER_N_DISC         => $this->__('Percent Discount'),
            self::TYPE_AFTER_N_FIXDISC      => $this->__('Fixed Discount'),
            self::TYPE_AFTER_N_FIXED        => $this->__('Fixed Price'),

            self::TYPE_SETOF_PERCENT        => $this->__('Percent discount for product set'),
            self::TYPE_SETOF_FIXED          => $this->__('Fixed price for product set'),
        );

        if (!$asOptions){

			$groups = array(
				'Popular'         => array(self::TYPE_CHEAPEST, self::TYPE_EXPENSIVE, self::TYPE_AMOUNT/*, self::WHOLE_CART*/),
                'Buy X Get Y (X and Y are different products)' => array(self::TYPE_XN_PERCENT, self::TYPE_XN_FIXDISC, self::TYPE_XN_FIXED),
                'Each N-th'       => array(self::TYPE_EACH_N, self::TYPE_EACH_N_FIXDISC, self::TYPE_FIXED),
                'Each Product After N' => array(self::TYPE_EACH_M_AFT_N_PERC, self::TYPE_EACH_M_AFT_N_DISC, self::TYPE_EACH_M_AFT_N_FIX),
                'Each Group of N' => array(self::TYPE_GROUP_N, self::TYPE_GROUP_N_DISC),
				'Product Set'   => array(self::TYPE_SETOF_PERCENT, self::TYPE_SETOF_FIXED),
			);

			$result = array();

			foreach ($groups as $groupName => $groupActions){
				$values = array();
				foreach ($groupActions as $k){
					$values[] = array(
						'value' => $k,
						'label' => $types[$k],
					);
				}
				$result[] = array(
                    'label' => $this->__($groupName),
                    'value' => $values,
				);
			}
            $types = $result;
        }

        return $types;
    }

    /**
     * @param $product
     * @param $qtyRequested
     * @return mixed
     */
    public function checkAvailableQty($product, $qtyRequested)
    {
        /**
         * @var Mage_Checkout_Model_Cart $cart
         */
        $cart = Mage::getModel('checkout/cart');

        $stockItem = Mage::getModel('cataloginventory/stock_item')
            ->assignProduct($product);

        if (!$stockItem->getManageStock()) {
            return $qtyRequested;
        }

        $qtyAdded = 0;

        foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
            if ($item->getProductId() == $product->getId()) {
                $qtyAdded += $item->getQty();
            }
        }

        $qty = $stockItem->getStockQty() - $qtyAdded;

        return min($qty, $qtyRequested);
    }

    public function getMembership($created)
    {
        return round((time() - strtotime($created))  /60 / 60 /24);
    }

    public static function comparePrices($a, $b)
    {
        $res = ($a['price'] < $b['price']) ? -1 : 1;
        if ($a['price'] == $b['price']) {
            $res = ($a['id'] < $b['id']) ? -1 : 1;
            if ($a['id'] == $b['id']) {
                $res = 0;
            }
        }

        return $res;
    }

    public function getRuleCats($rule)
    {
        $promoCats = explode(",",$rule->getPromoCats());
        $promoCats = array_diff($promoCats, array(''));
        $promoCats = array_map('trim', $promoCats);

        return $promoCats;
    }

    public function getRuleSkus($rule)
    {
        $promoSku = explode(',', $rule->getPromoSku());
        $promoSku = array_diff($promoSku, array(''));
        $promoSku = array_map('trim', $promoSku);

        return $promoSku;
    }

    public function isConfigurablePromoItem($object ,$promoSku ){
        $productType  = $object->getProductType();

        if ($productType == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
            foreach($promoSku as $sku){
                $clearSku = str_replace('-amconfigurable', '', $sku);
                if (strpos($sku,'-amconfigurable') !== false
                    && strpos($object->getProduct()->getSku(), $clearSku) !== false
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $rule
     * @return bool
     */
    public function isStopRuleProcessing($rule) {
        return $rule->getStopRulesProcessing() ? true : false;
    }

    /**
     * @param string $string
     *
     * @return array|null
     */
    public function unserialize($string)
    {
        if (!@class_exists('Amasty_Base_Helper_String')) {
            Mage::logException(new Exception(self::ERROR_MESSAGE));

            if (Mage::app()->getStore()->isAdmin()) {
                Mage::helper('ambase/utils')->_exit(self::ERROR_MESSAGE);
            } else {
                Mage::throwException($this->__('Sorry, something went wrong. Please contact us or try again later.'));
            }
        }

        return \Amasty_Base_Helper_String::unserialize($string);
    }
}
