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
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Blugento_ErpProcess_InvoiceController extends Mage_Core_Controller_Front_Action
{
    /**
     * Run cron action
     */
    public function addAction()
    {
        if (Mage::getStoreConfig('blugento_erpprocess/general/enable_invoice_endpoint')) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                /** @var Blugento_ErpProcess_Model_Process $process */
                $process = Mage::getModel('blugento_erpprocess/process');

                $body = json_decode($this->getRequest()->getRawBody(), 1);

                if (!isset($body['order_number']) || (isset($body['order_number']) && !$body['order_number'])) {
                    $response = [
                        'error' => 'true',
                        'message' => 'Missing order number.'
                    ];
                } else if (!$process->existOrder($body['order_number'])) {
                    $response = [
                        'error' => 'true',
                        'message' => 'Order with number ' . $body['order_number'] . ' does not exists.'
                    ];
                } else {
                    /** @var Blugento_ErpProcess_Helper_Data $helper */
                    $helper = Mage::helper('blugento_erpprocess');

                    $invoiceNumber = isset($body['invoice_number']) && $body['invoice_number'] ? $body['invoice_number'] : null;

                    if (isset($body['invoice']) && $body['invoice']) {
                        $invoiceCollection = new Varien_Data_Collection();
                        $invoiceObject = new Varien_Object();
                        $config = new Varien_Object();

                        if (stripos($body['invoice'], 'http://') !== false
                            || stripos($body['invoice'], 'https://') !== false
                        ) {
                            $config->setType('remote');
                            $invoiceObject->setDownloadUrl($body['invoice']);
                        } else {
                            $config->setType('content');
                            $invoiceObject->setContent(base64_decode($body['invoice']));
                        }

                        $invoiceObject->setId($body['order_number']);
                        $invoiceCollection->addItem($invoiceObject);

                        $config->setExtension('pdf');
                        $config->setOrderType('increment');

                        $helper->storeInvoice($invoiceCollection, $config);
                        $helper->createInvoiceForOrder($body['order_number'], $invoiceNumber);
                        $helper->updateOrderStatus($body['order_number']);

                        $response = [
                            'error' => 'false',
                            'message' => 'Invoice successfully saved.'
                        ];
                    } else {
                        $helper->createInvoiceForOrder($body['order_number'], $invoiceNumber);
                        $helper->updateOrderStatus($body['order_number']);

                        $response = [
                            'error' => 'false',
                            'message' => 'Invoice successfully generated.'
                        ];
                    }
                }
            } else {
                $response = [
                    'error' => 'true',
                    'message' => 'Unauthorized request method.'
                ];
            }
        } else {
            $response = [
                'error' => 'true',
                'message' => 'Access Denied.'
            ];
        }

        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));
        return $this;
    }
}
