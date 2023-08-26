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
 * @package     Mobilpay_Cc
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mobilpay_Cc_Block_Redirect_Recurrent extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $recurrent = Mage::getModel('cc/recurrent');

        $form = new Varien_Data_Form();

        try {
            $storeId = Mage::app()->getStore()->getStoreId();
            $storeLocaleCode = explode('_', Mage::getStoreConfig('general/locale/code', $storeId));
            $code = isset($storeLocaleCode[0]) && $storeLocaleCode[0] !='' ? $storeLocaleCode[0] : 'ro';

            $sandboxUrl    = !$code || $code == 'ro' ? 'http://sandboxsecure.mobilpay.ro' : 'http://sandboxsecure.mobilpay.ro/'. strtolower($code);
            $productionUrl = !$code || $code == 'ro' ? 'https://secure.mobilpay.ro' : 'https://secure.mobilpay.ro/'. strtolower($code);
        } catch (Exception $e) {
            Mage::logException($e);
            $sandboxUrl    = 'http://sandboxsecure.mobilpay.ro';
            $productionUrl = 'https://secure.mobilpay.ro';
        }

        $paymentUrl = ($recurrent->getConfigData('debug') == 1) ? $sandboxUrl : $productionUrl;

        $form->setAction($paymentUrl)
            ->setId('cc')
            ->setName('cc')
            ->setMethod('POST')
            ->setUseContainer(true);

        $form->addField('data', 'hidden', array('name'=>'data', 'value'=>$recurrent->getFormData()));
        $form->addField('env_key', 'hidden', array('name'=>'env_key', 'value'=>$recurrent->getFormKey()));
        $html = '<html><body>';
        $html.= $this->__('You will be redirected to MobilPay in a few seconds.');
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("cc").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}
