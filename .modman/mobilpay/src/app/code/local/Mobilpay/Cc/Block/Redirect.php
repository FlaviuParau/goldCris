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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 */
class Mobilpay_Cc_Block_Redirect extends Mage_Core_Block_Abstract
{
	protected function _toHtml()
	{
		$cc = Mage::getModel('cc/cc');

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

        $paymentUrl = ($cc->getConfigData('debug') == 1) ? $sandboxUrl : $productionUrl;

		$form->setAction($paymentUrl)
		->setId('cc')
		->setName('cc')
		->setMethod('POST')
		->setUseContainer(true);

        $formData = $cc->getFormData();
        $formKey = $cc->getFormKey();

        $filename = $this->getRequest()->getParam('filename') . '.txt';
        $directory = Mage::getBaseDir('var') . DS . 'mobilpay';
        $filepath = $directory . DS . $filename;

        if (!$formData || $formData == null || $formData == '') {
            if (file_exists($filepath)) {
                $lines = array();

                $file = fopen($filepath, 'r');
                while (!feof($file)) {
                    $lines[] = fgets($file);
                }
                fclose($file);

                $formData = str_replace(PHP_EOL, '', $lines[0]);
                $formKey = $lines[1];
            }
        }

        if (file_exists($filepath)) {
            unlink($filepath);
        }

		$form->addField('data', 'hidden', array('name'=>'data', 'value'=>$formData));
		$form->addField('env_key', 'hidden', array('name'=>'env_key', 'value'=>$formKey));
		$html = '<html><body>';
		$html.= $this->__('You will be redirected to MobilPay in a few seconds.');
		$html.= $form->toHtml();
		$html.= '<script type="text/javascript">document.getElementById("cc").submit();</script>';
		$html.= '</body></html>';

		return $html;
	}
}
