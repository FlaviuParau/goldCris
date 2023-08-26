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

class AW_Giftwrap_Block_Adminhtml_System_Config_Form_Fieldset_Media_Status
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $baseGiftwrapFolderPath = Mage::helper('aw_giftwrap/image')->getBaseGiftwrapFolderPath();
        if (!is_writable($baseGiftwrapFolderPath)) {
           $errorMessage = '<span style="color:#df280a;font-weight:bold;">'
               . Mage::helper('aw_giftwrap')->__('Path "%s" must be writable.', $baseGiftwrapFolderPath)
               . '</span>';
            return '<tr><td colspan="4" style="text-align:center;padding-top:15px;">' . $errorMessage . '</td></tr>';
        }
        return '';
    }
}