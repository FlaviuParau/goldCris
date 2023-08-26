<?php
class AW_Blog_Block_Adminhtml_Widget_Setup extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $block = $this->getLayout()->createBlock('core/template')->setTemplate('aw_blog/widget/setup.phtml');
        $element->setData('after_element_html', $block->toHtml());
        return $element;
    }
}
