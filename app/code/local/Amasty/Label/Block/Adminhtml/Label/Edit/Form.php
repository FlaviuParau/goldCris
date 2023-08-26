<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */
class Amasty_Label_Block_Adminhtml_Label_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        // add required css
        $this->getLayout()->getBlock('head')->addCss('amasty/amlabel/styles.css');
        $this->getLayout()->getBlock('head')->addJs('amasty/amlabel/amlabel.js');
        $this->getLayout()->getBlock('head')->addJs('amasty/amlabel/colorpicker/colorpicker.js');
        $this->getLayout()->getBlock('head')->addCss('amasty/amlabel/colorpicker/css/colorpicker.css');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form', 
            'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();
        if (Mage::getSingleton('core/cookie')->get('amasty_open_tab_input')){
            $html .= '<script>
                Event.observe(window, \'load\', function() {
                    $("' . Mage::getSingleton('core/cookie')->get('amasty_open_tab_input') . '").click();
                });
            </script>';
        }
        return $html; // TODO: Change the autogenerated stub
    }
}