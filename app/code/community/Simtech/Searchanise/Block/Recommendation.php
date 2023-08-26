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

class Simtech_Searchanise_Block_Recommendation extends Mage_Core_Block_Text
{
    protected function _toHtml()
    {
        $add_url = Mage::getBaseUrl() . 'searchanise/add';
        $name = $this->getNameInLayout();
        $name = str_replace('searchanise_', '', $name);
        $attrs = array('page-type' => $name);

        if ($name == 'product') {
            $product = Mage::registry('current_product');

            if ($product) {
                $attrs['product-ids'] = $product->getId();
            }

        } elseif ($name == 'cart') {
            $cart_items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
            foreach ($cart_items as $cart_item) {
                $attrs['product-ids'] = $cart_item->getProductId();
            }

        } elseif ($name == 'home_page' || $name == 'search_results' || $name == 'advanced_search_results') {
            $attrs['page-type'] = str_replace('_', ' ', $name);

        } elseif ($name == 'categories') {
            $attrs['page-type'] = 'category';

        }

        $html = '<div class="snize-recommendation-wrapper" data-add-url="'.$add_url.'"';
        foreach ($attrs as $key => $val) {
            $val = is_array($val) ? implode(',', $val) : $val;
            $html .= " data-{$key} = \"{$val}\"";
        }
        $html .= "></div>";

        return $html;
    }
}
