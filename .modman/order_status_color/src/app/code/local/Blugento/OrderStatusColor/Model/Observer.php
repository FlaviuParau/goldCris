<?php
class Blugento_OrderStatusColor_Model_Observer
{
    protected $_currentOrderGridBlockClass = 'Mage_Adminhtml_Block_Sales_Order_Grid';

    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();

        $this->_catchRewrittenOrderGridThatDoesntExtendOriginalClass();

        if ($this->_isBlockOrderGrid($block)) {
            $this->_addDecorateStatusFrameCallback($block->getColumn('status'));
            return $this;
        }

        if ($this->_isStatusFormBlock($block)) {
            $form = $block->getForm();
            $elements = $form->getElements();
            foreach ($elements as $element) {
                if ($this->_isBaseFieldset($element)) {
                    $this->_addColorInputFeild($element);
                    $this->_populateFormWithNewFeild($form);
                }
            }
        }

        return $this;
    }

    private function _catchRewrittenOrderGridThatDoesntExtendOriginalClass()
    {
        $rewriteNode = (string)Mage::getConfig()->getNode('global/blocks/adminhtml/rewrite/sales_order_grid');

        if ($rewriteNode) {
            $this->_currentOrderGridBlockClass = $rewriteNode;
        }

        return $this->_currentOrderGridBlockClass;
    }

    private function _isBlockOrderGrid(Mage_Core_Block_Abstract $block)
    {
        return $block instanceof $this->_currentOrderGridBlockClass;
    }

    private function _addDecorateStatusFrameCallback($column)
    {
        if(is_object($column))
        $column->setFrameCallback(array($this->getHelper(), 'decorateStatus'));
    }

    private function _isStatusFormBlock(Mage_Core_Block_Abstract $block)
    {
        return $block instanceof Mage_Adminhtml_Block_Sales_Order_Status_Edit_Form
            || $block instanceof Mage_Adminhtml_Block_Sales_Order_Status_New_Form;
    }

    private function _isBaseFieldset($element)
    {
        return $element->getId() == 'base_fieldset';
    }

    private function _addColorInputFeild($fieldset)
    {
        $fieldset->addField(
            'color', 'text',
            array(
                'name' => 'color',
                'label' => Mage::helper('sales')->__('Status Color'),
                'class' => 'color {hash:true,adjust:false}'
            )
        );
    }

    private function _populateFormWithNewFeild($form)
    {
        $model = Mage::registry('current_status');
        if ($model) {
            $form->addValues($model->getData());
        }
    }

    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();

        if ($this->_isOrderInfoBlock($block)) {
            $customColor = Mage::helper('blugento_orderstatuscolor')->getStatusColor(
                $block->getOrder()->getStatus()
            );

            $html = $this->_addBackgroundColorToStatusElement($transport->getHtml(),$customColor);

            $transport->setHtml($html);
        }

        return $this;
    }

    private function _addBackgroundColorToStatusElement($html,$backgroundColor)
    {
        $html = preg_replace(
            '/id="order_status"/',
            '$0  class="custom-color" style="background-color:' . $backgroundColor . ';"',
            $html
        );
        return $html;
    }

    private function _isOrderInfoBlock($block)
    {
        return $block->getNameInLayout() == 'order_info';
    }

    protected function getHelper()
    {
        return Mage::helper('blugento_orderstatuscolor');
    }
}
