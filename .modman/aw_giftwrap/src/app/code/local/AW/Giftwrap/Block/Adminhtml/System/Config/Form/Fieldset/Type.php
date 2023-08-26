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

class AW_Giftwrap_Block_Adminhtml_System_Config_Form_Fieldset_Type
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $html = $this->_getHeaderHtml($element);
        $html .= $this->_getTypesGridHtml($element);
        foreach ($element->getSortedElements() as $field) {
            $html.= $field->toHtml();
        }
        $html .= $this->_getFooterHtml($element);
        return $html;
    }

    protected function _getTypesGridHtml($element)
    {
        $typeListBlock = Mage::app()->getLayout()
            ->createBlock('aw_giftwrap/adminhtml_system_config_form_fieldset_type_list')
            ->setElement($element)
        ;
        return $typeListBlock->toHtml();
    }
}