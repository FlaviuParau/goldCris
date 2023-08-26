<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_StoreCredit
 */


if (!Mage::getStoreConfig('payment/free/active')) {
    Mage::getConfig()->saveConfig('payment/free/active', '1', 'default', 0);
}
