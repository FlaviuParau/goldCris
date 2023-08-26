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
 * @package     Blugento_GdprUserData
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GdprUserData_Model_Request extends Mage_Core_Model_Abstract
{
    const FILE_SAVE_PATH = 'GDPR_user_data';

    const EXPORT_EMAIL_TEMPLATE = 'blugento_gdpruserdata_export_export';
    const DELETE_EMAIL_TEMPLATE = 'blugento_gdpruserdata_delete_delete';
    const REJECT_EXIST_ACCOUNT_EMAIL_TEMPLATE = 'blugento_gdpruserdata_delete_reject_exist_account';
    const REJECT_STORE_OWNER_EMAIL_TEMPLATE = 'blugento_gdpruserdata_delete_reject_exist_store_owner';

    const EXPORT_CONFIG_PATH = 'blugento_gdpruserdata/export/export';
    const DELETE_CONFIG_PATH = 'blugento_gdpruserdata/delete/delete';
    const REJECT_EXIST_ACCOUNT_CONFIG_PATH = 'blugento_gdpruserdata/delete/reject_exist_account';
    const REJECT_STORE_OWNER_CONFIG_PATH = 'blugento_gdpruserdata/delete/reject_store_owner';


    public function _construct()
    {
        parent::_construct();
        $this->_init('blugento_gdpruserdata/request');
    }

    /**
     * Run the cron to process the requests
     */
    public function cronProcessRequests() {
        $this->initExport();
        $this->initDelete();
        $this->cleanRequests();
    }

    /**
     * Initialize the export process
     */
    private function initExport() {
        $helper = Mage::helper('blugento_gdpruserdata');

        $exportRequests = $this->getCollection()
            ->addFieldToFilter('type', 'export')
            ->addFieldToFilter('status', 'pending');

        if (count($exportRequests) > 0) {
            foreach ($exportRequests as $request) {
                $archiveName = $this->exportUserData($request);

                $request->setStatus('processed');
                $request->setArchiveName($archiveName);
                $request->save();

                if ($archiveName && file_exists($helper->getGdprPath($archiveName))) {
                    $this->sendExportEmail($request);
                }
            }
        }
    }

    /**
     * Initialize the delete process
     */
    private function initDelete() {
        $deleteRequests = $this->getCollection()
            ->addFieldToFilter('type', 'delete')
            ->addFieldToFilter('status', 'processed')
            ->addFieldToFilter('admin_confirmation', 'approved');

        if (count($deleteRequests) > 0) {
            foreach ($deleteRequests as $request) {
                $deletedData = $this->deleteUserData($request);
                if (count($deletedData) > 0) {
                    $this->sendDeletedEmail($deletedData, $request);

                    $request->setStatus('completed');
                    $request->save();
                }
            }
        }
    }

    /**
     * Initialize the log clean process
     */
    private function cleanRequests() {
        $helper = Mage::helper('blugento_gdpruserdata');
        $requests = $this->getCollection()->addFieldToFilter('status', 'processed');

        foreach ($requests as $request) {
            if ($helper->isRequestExpired($request->getCreatedAt())) {
                if ($request->getType() == 'export') {
                    $filepath = $helper->getGdprPath($request->getArchiveName());

                    if (file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                $request->setStatus('deleted');
                $request->save();
            }
        }
    }

    /**
     * Export user data
     */
    private function exportUserData($request) {
        try {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        if (!isset($websiteId) || (isset($websiteId) && !$websiteId)) {
            $websiteId = 1;
        }
        $customer = $this->existAccount($request->getCustomerEmail(), $websiteId);

        if ($customer) {
            $this->createAccountInfoFile($customer, $request->getId());

            $addressBook = $customer->getAddressesCollection();
            if (count($addressBook) > 0) {
                $this->createAddressBookInfoFile($addressBook, $request->getId());
            }

            $reviews = Mage::getModel('review/review')
                ->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId());

            if (count($reviews) > 0) {
                $this->createReviewsInfoFile($reviews, $request->getId());
            }

            $wishlist = Mage::getModel('wishlist/wishlist')->load($customer->getId(), 'customer_id');

            $wishilistItems = Mage::getModel('wishlist/item')
                ->getCollection()
                ->addFieldToFilter('wishlist_id', $wishlist->getId());

            if (count($wishilistItems) > 0) {
                $this->createWishlistInfoFile($wishilistItems, $request->getId());
            }
        }
        $orders = $this->existOrders($request->getCustomerEmail());

        if ($orders) {
            $this->createOrderInfoFile($orders, $request->getId());

            $invoices = $this->getInvoices($orders);

            if ($invoices) {
                $this->createInvoiceInfoFile($invoices, $request->getId());
            }
        }

        $archiveName = $this->createZipArchive($request->getId());
        $this->deleteFiles($request->getId());

        return $archiveName;
    }

    /**
     * Delete user data
     */
    private function deleteUserData($request) {
        $orders = $this->existOrders($request->getCustomerEmail());
        $deletedData = [];

        if ($orders) {
            $this->deleteUserDataFromOrders($orders);
            array_push($deletedData, 'orders');

            if ($this->getInvoices($orders)) {
                array_push($deletedData, 'invoices');
            }

            if ($this->getShipments($orders)) {
                array_push($deletedData, 'shipments');
            }

            if ($this->getCreditMemos($orders)) {
                array_push($deletedData, 'creditmemos');
            }
        }

        $quotes = $this->existQuotes($request->getCustomerEmail());

        if ($quotes) {
            $this->deleteUserDataFromQuotes($quotes);
            array_push($deletedData, 'quotes');
        }
        return $deletedData;
    }

    /**
     * Create account informations file
     */
    private function createAccountInfoFile($customer, $requestId) {
        $helperInfo = Mage::helper('blugento_gdpruserdata/informations_array');
        $informations = $helperInfo->getAccountInformationArray();
        $accountInformations = [];

        foreach ($informations as $key => $info) {
            $methodName = str_replace(' ', '',ucwords(str_replace('_', ' ', $key)));
            $accountInformations[0][$key] = $customer->{'get'.$methodName}();
        }
        $result = $this->createCsvFile($accountInformations, $informations,'account_data', $requestId);

        if ($result != 'success') {
            Mage::log('Cannot create account data file for request with id '.$requestId.'!', null, 'GDPR_export_data.log');
        }
    }

    /**
     * Create address book informations file
     */
    private function createAddressBookInfoFile($addressBook, $requestId) {
        $helperInfo = Mage::helper('blugento_gdpruserdata/informations_array');
        $informations = $helperInfo->getAddressBookArray();
        $addressInformations = [];

        foreach($addressBook as $addKey => $address) {
            foreach ($informations as $key => $info) {
                $addressInformations[$addKey][$key] = $address->getData($key);
            }
        }
        $result = $this->createCsvFile($addressInformations, $informations,'account_addressbook', $requestId);

        if ($result != 'success') {
            Mage::log('Cannot create account address book file for request with id '.$requestId.'!', null, 'GDPR_export_data.log');
        }
    }

    /**
     * Create review informations file
     */
    private function createReviewsInfoFile($reviews, $requestId) {
        $helperInfo = Mage::helper('blugento_gdpruserdata/informations_array');
        $informations = $helperInfo->getReviewsArray();
        $reviewsInformations = [];

        foreach ($reviews as $revKey => $review) {
            foreach ($informations as $key => $info) {
                if ($key == 'entity_pk_value') {
                    $reviewsInformations[$revKey][$key] = Mage::getModel('catalog/product')->load($review->getData($key))->getName();
                } else {
                    $reviewsInformations[$revKey][$key] = $review->getData($key);
                }
            }
        }
        $result = $this->createCsvFile($reviewsInformations, $informations,'reviews', $requestId);

        if ($result != 'success') {
            Mage::log('Cannot create reviews file for request with id '.$requestId.'!', null, 'GDPR_export_data.log');
        }
    }

    /**
     * Create wishlist informations file
     */
    private function createWishlistInfoFile($items, $requestId) {
        $helperInfo = Mage::helper('blugento_gdpruserdata/informations_array');
        $informations = $helperInfo->getWishlistArray();
        $wishlistInformations = [];

        foreach ($items as $wKey => $item) {
            foreach ($informations as $key => $info) {
                if ($key == 'product_id') {
                    $wishlistInformations[$wKey][$key] = $item->getData('product')->getName();
                } else {
                    $wishlistInformations[$wKey][$key] = $item->getData($key);
                }
            }
        }
        $result = $this->createCsvFile($wishlistInformations, $informations,'wishlist', $requestId);

        if ($result != 'success') {
            Mage::log('Cannot create wishlist file for request with id '.$requestId.'!', null, 'GDPR_export_data.log');
        }
    }

    /**
     * Create order informations file
     */
    private function createOrderInfoFile($orders, $requestId) {
        $helper = Mage::helper('blugento_gdpruserdata');
        $helperInfo = Mage::helper('blugento_gdpruserdata/informations_array');

        $informationsOrdArr = $helperInfo->getOrderArray();
        $informationsOrdBillAddrArr = $helperInfo->getOrderBillingAddressArray();
        $informationsOrdShipAddrArr = $helperInfo->getOrderShippingAddressArray();
        $informationsOrdItemsArr = $helperInfo->getOrderItemsArray();

        $informations = array_merge($informationsOrdArr, $helper->concatenateWordBeforeKeys('billing' ,$informationsOrdBillAddrArr), $helper->concatenateWordBeforeKeys('shipping' ,$informationsOrdShipAddrArr));

        foreach ($orders as $oKey => $order) {
            $orderInformations = [];
            $itemInformations = [];
            foreach ($informationsOrdArr as $key => $info) {
                $methodName = str_replace(' ', '',ucwords(str_replace('_', ' ', $key)));
                $orderInformations[$oKey][$key] = $order->{'get'.$methodName}();
            }

            $billingAddress = Mage::getModel('sales/order_address')->load($order->getBillingAddressId());

            foreach ($informationsOrdBillAddrArr as $key => $info) {
                $orderInformations[$oKey]['billing_'.$key] = $billingAddress->getData($key);
            }

            $shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddressId());

            foreach ($informationsOrdShipAddrArr as $key => $info) {
                $orderInformations[$oKey]['shipping_'.$key] = $shippingAddress->getData($key);
            }

            $this->createCsvFile($orderInformations, $informations,'order_' .$order->getIncrementId(), $requestId, 'orders');

            $orderItems = Mage::getModel('sales/order_item')
                ->getCollection()
                ->addFieldToFilter('order_id', $order->getId());

            foreach ($orderItems as $itKey => $item) {
                foreach ($informationsOrdItemsArr as $key => $info) {
                    $itemInformations[$itKey][$key] = $item->getData($key);
                }
            }
            $this->createCsvFile($itemInformations, $informationsOrdItemsArr,'order_' .$order->getIncrementId() , $requestId, 'orders', true);
        }
    }

    /**
     * Delete user data from orders and order addresses
     */
    private function deleteUserDataFromOrders($orders) {
        $helper = Mage::helper('blugento_gdpruserdata');

        foreach ($orders as $order) {
            try {
                $order->setCustomerEmail($helper->anonymizeEmail($order->getCustomerEmail()));
                $order->save();

                $addresses = Mage::getModel('sales/order_address')
                    ->getCollection()
                    ->addFieldToFilter('parent_id', $order->getId());

                foreach ($addresses as $address) {
                    $address->setEmail($helper->anonymizeEmail($address->getEmail()));
                    $address->setTelephone($helper->anonymizePhone($address->getTelephone()));
                    $address->setStreet($helper->anonymizeStreet($address->getStreet()));
                    $address->setPostcode($helper->anonymizeData($address->getPostcode()));
                    $address->setBlugentoCustomerCnp($helper->anonymizeData($address->getBlugentoCustomerCnp()));
                    $address->save();
                }

            } catch (Exception $e) {
                Mage::log($helper->__('Some of the user order data cannot be anonymized for order with id ') . $order->getId() . ': ' . $e->getMessage(), null, 'GDPR_user_data.log');
            }
        }
    }

    /**
     * Create invoice informations file
     */
    private function createInvoiceInfoFile($invoices, $requestId) {
        $helperInfo = Mage::helper('blugento_gdpruserdata/informations_array');

        $informationsInvoiceArray = $helperInfo->getInvoiceArray();
        $informationsInvoiceItemsArray = $helperInfo->getInvoiceItemsArray();

        foreach ($invoices as $inKey => $invoice) {
            $invoiceInformations = [];
            $itemInformations = [];

            foreach ($informationsInvoiceArray as $key => $info) {
                if ($key == 'order_increment_id') {
                    $invoiceInformations[$inKey][$key] = Mage::getModel('sales/order')->load($invoice->getOrderId())->getIncrementId();
                } else {
                    $invoiceInformations[$inKey][$key] = $invoice->getData($key);
                }
            }
            $this->createCsvFile($invoiceInformations, $informationsInvoiceArray, 'invoice_' . $invoice->getIncrementId(), $requestId, 'invoices');

            $invoiceItems = $invoice->getAllItems();

            foreach ($invoiceItems as $itKey => $item) {
                foreach ($informationsInvoiceItemsArray as $key => $info) {
                    $itemInformations[$itKey][$key] = $item->getData($key);
                }
            }
            $this->createCsvFile($itemInformations, $informationsInvoiceItemsArray, 'invoice_' . $invoice->getIncrementId(), $requestId, 'invoices', true);
        }
    }

    /**
     * Delete user data from quotes and quote addresses
     */
    private function deleteUserDataFromQuotes($quotes) {
        $helper = Mage::helper('blugento_gdpruserdata');

        foreach ($quotes as $quote) {

            try {
                $quote->setCustomerEmail($helper->anonymizeEmail($quote->getCustomerEmail()));
                $quote->save();

                $addresses = Mage::getModel('sales/quote_address')
                    ->getCollection()
                    ->addFieldToFilter('quote_id', $quote->getId());

                foreach ($addresses as $address) {
                    $address->setEmail($helper->anonymizeEmail($address->getEmail()));
                    $address->setTelephone($helper->anonymizePhone($address->getTelephone()));
                    $address->setStreet($helper->anonymizeStreet($address->getStreet()));
                    $address->setPostcode($helper->anonymizeData($address->getPostcode()));
                    $address->setBlugentoCustomerCnp($helper->anonymizeData($address->getBlugentoCustomerCnp()));
                    $address->save();
                }
            } catch (Exception $e) {
                Mage::log($helper->__('Some of the user quote data cannot be anonymized for quote with id ') . $quote->getId() . ': ' . $e->getMessage(), null, 'GDPR_user_data.log');
            }
        }
    }

    /**
     * Create CSV file
     */
    private function createCsvFile($data, $infoArray, $filename, $requestId, $folder=false, $append=false) {
        $result = 'success';
        $filepath = $this->getFilePath($filename, 'csv', $requestId, $folder);

        try {
            if ($append) {
                $file = fopen($filepath, 'a');

                //add an empty row
                fputcsv($file, array());
            } else {
                $file = fopen($filepath, 'w');
            }

            fputcsv($file, $infoArray, '|', '"');

            foreach ($data as $info) {
                fputcsv($file, $info, '|', '"');
            }
            fclose($file);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $result;
    }

    /**
     * Create file path for requests
     */
    public function getFilePath($filename, $extension, $requestId, $folder=false) {
        $helper = Mage::helper('blugento_gdpruserdata');
        $path = $helper->getGdprPath('request_' . $requestId);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($folder) {
            $path .= DS . $folder;

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        return $path . DS . $filename . '.' . $extension;
    }

    /**
     * Create zip archive file
     */
    private function createZipArchive($requestId) {
        $helper = Mage::helper('blugento_gdpruserdata');

        $requestsFilepath = $helper->getGdprPath('request_' . $requestId);

        if (file_exists($requestsFilepath)) {
            $archiveName = $helper->generateHashedName() . '.zip';
            $archivePathName = $helper->getGdprPath($archiveName);

            $zip = new ZipArchive();
            if ($zip->open($archivePathName, ZipArchive::CREATE) === TRUE) {

                if ($handle = opendir($requestsFilepath)) {
                    while (false !== ($entry = readdir($handle))) {

                        if ($entry != '.' && $entry != '..') {
                            $filename = $requestsFilepath . DS . $entry;

                            if (is_dir($filename)) {
                                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filename, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
                                foreach ($files as $file) {

                                    if (is_dir($file)) {
                                        $zip->addEmptyDir(str_replace($filename . '/', '', $file . '/'));
                                    } elseif ($file != '.' && $file != '..') {
                                        $zip->addFromString(str_replace($filename . DS, $entry . '/', $file), file_get_contents($file));
                                    }
                                }
                            } else {
                                $zip->addFile($filename, $entry);
                            }
                        }
                    }
                    closedir($handle);
                }
                $zip->close();
                return $archiveName;
            }
        }
    }

    /**
     * Delete CSV files and folders
     */
    private function deleteFiles($requestId) {
        $helper = Mage::helper('blugento_gdpruserdata');
        $requestsFilepath = $helper->getGdprPath('request_' . $requestId);

        if(file_exists($requestsFilepath)) {
            $helper->unlinkRecursive($requestsFilepath, true);
        }
    }

    /**
     * Get invoices for orders
     */
    private function getInvoices($orders) {
        $invoices = [];

        foreach ($orders as $order) {
            $orderInvoices = $order->getInvoiceCollection();
            foreach ($orderInvoices as $invoice) {
                array_push($invoices, $invoice);
            }
        }

        if (count($invoices) > 0) {
            return $invoices;
        } else {
            return false;
        }
    }

    /**
     * Get shipments for orders
     */
    private function getShipments($orders) {
        $shipments = [];

        foreach ($orders as $order) {
            $orderShipments = $order->getShipmentsCollection();
            foreach ($orderShipments as $shipment) {
                array_push($shipments, $shipment);
            }
        }

        if (count($shipments) > 0) {
            return $shipments;
        } else {
            return false;
        }
    }

    /**
     * Get credit memos for orders
     */
    private function getCreditMemos($orders) {
        $creditmemos = [];

        foreach ($orders as $order) {
            $orderCreditmemos = $order->getCreditmemosCollection();
            foreach ($orderCreditmemos as $creditmemo) {
                array_push($creditmemos, $creditmemo);
            }
        }

        if (count($creditmemos) > 0) {
            return $creditmemos;
        } else {
            return false;
        }
    }

    /**
     *  Prepare export email
     */
    private function sendExportEmail($request) {
        $helper = Mage::helper('blugento_gdpruserdata');

        $templateIdentifier = $helper->getEmailTemplate(self::EXPORT_CONFIG_PATH, self::EXPORT_EMAIL_TEMPLATE);
        $error = 'The email cannot be sent for export request with id ' . $request->getId();

        $vars = [
            'export_url' => $helper->getExportLinkUrl($request->getSecretKey())
        ];

        $helper->sendTransactionalEmail($templateIdentifier, $request->getCustomerEmail(), $vars, $error);
    }

    /**
     *  Prepare delete email
     */
    private function sendDeletedEmail($data, $request) {
        $helper = Mage::helper('blugento_gdpruserdata');

        $templateIdentifier = $helper->getEmailTemplate(self::DELETE_CONFIG_PATH, self::DELETE_EMAIL_TEMPLATE);
        $error = 'The email cannot be sent for delete request with id ' . $request->getId();

        $vars = [];
        foreach ($data as $value) {
            $vars[$value] = $value;
        }

        $helper->sendTransactionalEmail($templateIdentifier, $request->getCustomerEmail(), $vars, $error);
    }

    /**
     *  Prepare rejection email for existing account
     */
    public function sendAccountRejectionEmail() {
        $helper = Mage::helper('blugento_gdpruserdata');
        $vars = [];

        $templateIdentifier = $helper->getEmailTemplate(self::REJECT_EXIST_ACCOUNT_CONFIG_PATH, self::REJECT_EXIST_ACCOUNT_EMAIL_TEMPLATE);
        $error = 'The email cannot be sent for Account Rejection request for email: ' . $this->getCustomerEmail();

        $helper->sendTransactionalEmail($templateIdentifier, $this->getCustomerEmail(), $vars, $error);
    }

    /**
     *  Prepare rejection email by store owner
     */
    public function sendRejectionMessageEmail($email, $message, $requestId) {
        $helper = Mage::helper('blugento_gdpruserdata');

        $templateIdentifier = $helper->getEmailTemplate(self::REJECT_STORE_OWNER_CONFIG_PATH, self::REJECT_STORE_OWNER_EMAIL_TEMPLATE);
        $error = 'The email for reject deletion for request with id ' . $requestId . ' cannot be sent!';

        $vars = [
            'message' => $message
        ];

        $helper->sendTransactionalEmail($templateIdentifier, $this->getCustomerEmail(), $vars, $error);
    }

    /**
     *  Check if there are informations in accordance with customer email
     */
    public function customerHasData($email) {
        try {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        if (!isset($websiteId) || (isset($websiteId) && !$websiteId)) {
            $websiteId = 1;
        }

        if ($this->existAccount($email, $websiteId)) {
            return true;
        }

        if ($this->existOrders($email)) {
            return true;
        }

        if ($this->existQuotes($email)) {
            return true;
        }

        return false;
    }

    /**
     *  Check if there is an account in accordance with customer email
     */
    public function existAccount($email, $websiteId) {
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($email);

        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     *  Check if there are orders in accordance with customer email
     */
    public function existOrders($email) {
        $orders = Mage::getModel('sales/order')
            ->getCollection()
            ->addFieldToFilter('customer_email', $email);

        if (count($orders) > 0) {
            return $orders;
        }
        return false;
    }

    /**
     *  Check if there are quotes in accordance with customer email
     */
    public function existQuotes($email) {
        $quotes = Mage::getModel('sales/quote')
            ->getCollection()
            ->addFieldToFilter('customer_email', $email);

        if (count($quotes) > 0) {
            return $quotes;
        }
        return false;
    }
}