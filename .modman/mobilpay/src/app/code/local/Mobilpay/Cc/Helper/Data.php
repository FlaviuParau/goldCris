<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mobilpay_Cc_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_CC_METHODS = 'payment';

	public function getMethodInstance($code)
    {
		$key = self::XML_PATH_CC_METHODS.'/'.$code.'/model';
        $class = Mage::getStoreConfig($key);
        if (!$class) {
            Mage::throwException($this->__('Can not configuration for payment method with code: %s', $code));
        }
        return Mage::getModel($class);
    }

    public function generateHash()
    {
        $entities = array ('upper', 'lower', 'digits');
        $upper = range('A', 'Z');
        $lower = range('a', 'z');
        $digits = range(0, 9);
        $length = rand(7, 15);

        $hash = '';

        for ($i = 1; $i <= $length; $i++) {
            $entity = $entities[array_rand($entities)];
            $hash .= $$entity[array_rand($$entity)];
        }

        $hash .= '_' . time();

        return $hash;
    }
}