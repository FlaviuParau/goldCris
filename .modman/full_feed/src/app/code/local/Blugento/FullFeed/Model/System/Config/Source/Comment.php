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
 * @package     Blugento_FullFeed
 * @author      Mihai Rastasan <mihai.rastasan@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FullFeed_Model_System_Config_Source_Comment
{
    public function getCommentText()
    {
        $text = '<p>Select product attributes that will be added to feed. If you generate XML feed you cannot have 
                space character on Feed Attribute Code column. Feed Atribute Code: if empty, magento code is used. Sort 
                Order: if empty or 0 the field is excluded from feed.</p>';
        $text .= '<p><strong>Map Values replace current value example:</strong> dbvalue1:feedvalue1#dbvalue2:feedvalue2</p>';
        $text .= '<p>
                    <strong>Map Values price formula example:</strong> 
                    x*1.1+5; where x is current price; if current price is 100 => 100*1.1+5=115;
                    <strong style="color: red">Only for price and special_price.</strong>
                  </p>';

        return $text;
    }
}
