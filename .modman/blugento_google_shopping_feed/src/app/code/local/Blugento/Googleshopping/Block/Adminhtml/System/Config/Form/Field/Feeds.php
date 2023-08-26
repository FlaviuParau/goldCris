<?php

class Blugento_Googleshopping_Block_Adminhtml_System_Config_Form_Field_Feeds
    extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('googleshopping');
        $storeIds = $helper->getStoreIds('googleshopping/generate/enabled');
        $htmlFeedlinks = '';
        foreach ($storeIds as $storeId) {
            $generateUrl = $this->getUrl('*/googleshopping/generateManual/store_id/' . $storeId);
            $previewUrl = $this->getUrl('*/googleshopping/preview/store_id/' . $storeId);
            $downloadUrl = $this->getUrl('*/googleshopping/download/store_id/' . $storeId);
            $feedText = $helper->getUncachedConfigValue('googleshopping/generate/feed_result', $storeId);
            if (empty($feedText)) {
                $feedText = Mage::helper('googleshopping')->__('No active feed found');
                $downloadUrl = '';
            }

            $store = Mage::getModel('core/store')->load($storeId);
            $storeTitle = $store->getName();
            $storeCode = $store->getCode();

            $htmlFeedlinks .= '<tr>
             <td valign="top">' . $storeTitle . '<br/><small>Code: ' . $storeCode . '</small></td>
             <td>' . $feedText . '</td>
             <td>
              » <a href="' . $generateUrl . '">' . Mage::helper('googleshopping')->__('Generate New') . '</a><br/>
              » <a href="' . $previewUrl . '">' . Mage::helper('googleshopping')->__('Preview 100') . '</a><br/>
              » <a href="' . $downloadUrl . '">' . Mage::helper('googleshopping')->__('Download Last') . '</a>              
             </td>
            </tr>';
        }

        if (empty($htmlFeedlinks)) {
            $htmlFeedlinks = Mage::helper('googleshopping')->__('No enabled feed(s) found');
        } else {
            $htmlHeader = '<div class="grid">
             <table cellpadding="0" cellspacing="0" class="border" style="width: 100%">
              <tbody>
               <tr class="headings"><th>Store</th><th>Feed</th><th>Generate</th></tr>';
            $htmlFooter = '</tbody></table></div>';
            $htmlFeedlinks = $htmlHeader . $htmlFeedlinks . $htmlFooter;
        }

        return sprintf(
            '<tr id="row_%s"><td colspan="6" class="label" style="margin-bottom: 10px;">%s</td></tr>',
            $element->getHtmlId(),
            $htmlFeedlinks
        );
    }

}
