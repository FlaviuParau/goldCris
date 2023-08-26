<?php
class AW_Blog_Block_Adminhtml_Helper_Image extends Varien_Data_Form_Element_Image
{
    /**
     * Prepend the base image URL to the image filename
     *
     * @return null|string
     */
    protected function _getUrl()
    {
        if ($this->getValue() && !is_array($this->getValue())) {
            return Mage::helper('blog/image')->getImageUrl($this->getValue());
        }
        
        return null;
    }
}
