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

class Blugento_FanCourier_Model_Process extends Mage_Core_Model_Abstract
{
    /**
     * Cash on delivery methods
     *
     * @var array
     */
    protected $cashOnDeliveryMethods = array('cashondelivery', 'zitec_dpd_cashondelivery', 'dpd_payment'); // TODO Add additional cash on delivery methods

    /**
     * Return cash on delivery methods
     *
     * @return array
     */
    public function getCashOnDeliveryMethods()
    {
        return $this->cashOnDeliveryMethods;
    }

    /**
     * Print single awb when the order has only one shipment
     *
     * @param string $awbNo
     * @param int $orderId
     * @return array|mixed|string
     */
    public function printSingleAwb($awbNo, $orderId)
    {
        /** @var Blugento_FanCourier_Model_Order_Client $client */
        $client = Mage::getModel('blugento_fancourier/order_client');

        $clientId = $client->getClientIdByOrderId($orderId);

        /** @var Blugento_FanCourier_Model_Api $api */
        $api = Mage::getModel('blugento_fancourier/api');

        $print = $api->getAwbLabel($awbNo, $clientId);

        if (isset($print['error'])) {
            return $print;
        }

        $this->downloadFile($print, $awbNo, 'content');
    }

    /**
     * Print multiple awb when the order has more than one shipment
     *
     * @param array $awbNumbers
     * @param string $orderNo
     * @param int $orderId
     * @return array
     */
    public function printMultipleAwb($awbNumbers, $orderNo, $orderId)
    {
        /** @var Blugento_FanCourier_Model_Order_Client $client */
        $client = Mage::getModel('blugento_fancourier/order_client');

        $clientId = $client->getClientIdByOrderId($orderId);

        /** @var Blugento_FanCourier_Model_Api $api */
        $api = Mage::getModel('blugento_fancourier/api');

        $prints = array();
        $errors = array();
        foreach ($awbNumbers as $awbNo) {
            $print = $api->getAwbLabel($awbNo, $clientId);

            if (isset($print['error'])) {
                $errors[$awbNo] = $print['error'];
            } else {
                $prints[$awbNo] = $print;
            }
        }

        if (count($prints) == 0 && count($errors) > 0) {
            return array(
                'error' => implode('; ', $errors)
            );
        }

        $path = $this->saveFiles($prints, $orderNo);
        $archive = $this->createArchive($path);
        $this->downloadFile($archive);
        $this->removeFiles($path, $archive);
    }

    /**
     * Return services from cache if exist
     * If don't exist, get services from API and save them to cache and than return them
     *
     * @return array|string
     */
    public function getServices()
    {
        if ($cachedData = Mage::app()->getCache()->load(Blugento_FanCourier_Helper_Data::CACHE_ID)) {
            $services = unserialize($cachedData);
        } else {
            /** @var Blugento_FanCourier_Model_Api $api */
            $api = Mage::getModel('blugento_fancourier/api');

            $services = $api->getServices();

            $error = '';
            if (!is_array($services)) {
                $error = $services;
            } elseif (in_array('Error', $services)) {
                $error = Mage::helper('blugento_fancourier')->__('Fan Courier: Cannot get Services.');
            }

            $key = array_search('Servicii FAN Courier', $services);
            if (is_int($key) && $key !== false) {
                unset($services[$key]);
            }

            try {
                if (!$error) {
                    Mage::app()->getCache()->save(serialize($services), Blugento_FanCourier_Helper_Data::CACHE_ID, array(Mage_Core_Block_Abstract::CACHE_GROUP));
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }

            if ($error) {
                Mage::log($error);
                return $error;
            }
        }

        return $services;
    }

    /**
     * Return location data from localities json
     *
     * @param string $region
     * @param string $city
     * @return int
     */
    public function getCity($region, $city)
    {
        $filepath = $filepath = Mage::getBaseDir('lib') . DS . 'blugento_fancourier' . DS . 'localities.php';

        if (file_exists($filepath)) {
            $localities = require $filepath;

            $key = strtolower(str_replace(array(' ', '(', ')', '.', '-', 'ș'), array('', '', '', '', '', 's'), $city . $region));

            if (isset($localities[$key])) {
                return $localities[$key]['name'];
            }
        }

        return null;
    }

    /**
     * Prepare data by shipping items quantity
     *
     * @param array $order
     * @return array
     */
    public function getDataByShippingItemsQty($order)
    {
        $grandTotal = $order['grand_total'];

        if ($order['shipping_qty'] != $order['total_qty_ordered']) {
            $grandTotal = $order['order_amount'];
        }

        $cashOnDelivery = $this->getCashOnDeliveryAmount($order, $grandTotal);

        return array (
            'declared_value' => $this->getDeclaredValue($grandTotal, $order),
            'cash_on_delivery' => $cashOnDelivery
        );
    }

    /**
     * Return payer type
     *
     * @param float $shippingAmount
     * @param bool $checkPaymentMethod
     * @param array $order
     * @return int
     */
    public function getPayerType($shippingAmount, $checkPaymentMethod = false, $order = array())
    {
        $payer = 'expeditor';

        if (!$checkPaymentMethod
            && $this->getConfig('payer') == 'recipient'
            && $shippingAmount > 0
        ) {
            $payer = 'destinatar';
        }

        if ($checkPaymentMethod
            && in_array($order['payment_method'], $this->cashOnDeliveryMethods)
            && $this->getConfig('payer') == 'recipient'
            && $this->getConfig('cash_on_delivery') == 'cash'
        ) {
            $payer = 'destinatar';
        }

        return $payer;
    }

    /**
     * Calculate order declared value
     *
     * @param array $orderData
     * @param float $totalValue
     * @return float|int
     */
    public function getDeclaredValue($totalValue, $orderData)
    {
        if ($this->getConfig('insurance')) {
            $declaredValue = round($totalValue - $orderData['shipping_amount'] - $orderData['shipping_tax_amount'], 2);
        } else {
            $declaredValue = 0;
        }

        return $declaredValue;
    }

    /**
     * Calculate order bank amount and order cash amount
     *
     * @param $order
     * @param $amount
     * @return float|string
     */
    public function getCashOnDeliveryAmount($order, $amount)
    {
        $value = 0;

        if (in_array($order['payment_method'], $this->cashOnDeliveryMethods)) {
            if ($this->getPayerType($order['shipping_amount'], true, $order) == 'destinatar') {
                $amount = $amount - $order['shipping_amount'] - $order['shipping_tax_amount'];
            }

            $value = number_format(round((float)$amount,2), 2, '.', '');
        }

        return $value;
    }

    /**
     * Add additional options param to be send by API
     *
     * @return string
     */
    public function getAdditionalOptions()
    {
        $options = '';

        if ($this->getConfig('open_package')) {
            $options .= 'A';
        }

        if ($this->getConfig('epod')) {
            $options .= 'X';
        }

        return $options;
    }

    /**
     * Create CSV file to be send through API
     *
     * @param array $params
     * @param string $orderNumber
     * @return string
     */
    public function createCsvFile($params, $orderNumber)
    {
        $csvHeaders = $this->getCsvHeaders();
        $path = $this->getDirectoryPath($orderNumber);
        $filePath = $path . DS . 'create_awb.csv';

        $file = fopen($filePath, 'a');
        fputcsv($file, $csvHeaders);
        fputcsv($file, $params);
        fclose($file);

        return $filePath;
    }

    /**
     * Remove archive and pdf files
     *
     * @param string $path
     * @param string $zip
     */
    public function removeFiles($path, $zip = '')
    {
        // remove zip archive
        if ($zip) {
            unlink($zip);
        }

        if (is_file($path)) {
            unlink($path);
        } else {
            // remove folder with pdf files
            $this->unlinkRecursive($path, true);
        }
    }

    /**
     * Save files on media directory
     *
     * @param array $files
     * @param string $dirName
     * @return string
     */
    protected function saveFiles($files, $dirName)
    {
        $path = $this->getDirectoryPath($dirName);

        foreach ($files as $awbNumber => $file) {
            $filepath = $path . DS . $awbNumber . '.pdf';
            file_put_contents($filepath, $file);
        }

        return $path;
    }

    /**
     * Return path to directory
     *
     * @param string $dirName
     * @return string
     */
    protected function getDirectoryPath($dirName)
    {
        $dir = Mage::getBaseDir('media') . DS . 'blugento_fancourier';

        if (!file_exists($dir)) {
            mkdir($dir);
        }

        $dir .= DS . $dirName;

        if (!file_exists($dir)) {
            mkdir($dir);
        }

        return $dir;
    }

    /**
     * Download file
     *
     * @param string $file
     * @param null|string $filename
     * @param string $type
     */
    protected function downloadFile($file, $filename = null, $type = 'file')
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        if ($type == 'content') {
            header('Content-Disposition: attachment; filename=fan_courier_awb_' . $filename . '.pdf');
            header('Content-Length: ' . strlen($file) . 'B');
            echo $file;
        } else {
            header('Content-Disposition: attachment; filename=fan_courier_awb_order_' . basename($file));
            header('Content-Length: ' . filesize($file));
            readfile($file);
        }
    }

    /**
     * Create ZIP Archive
     *
     * @param string $path
     * @return string
     */
    protected function createArchive($path)
    {
        $archivePathName = $path . '.zip';

        if (file_exists($path)) {
            $zip = new ZipArchive();
            if ($zip->open($archivePathName, ZipArchive::CREATE) === TRUE) {
                if ($handle = opendir($path)) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != '.' && $entry != '..') {
                            $filename = $path . DS . $entry;
                            $zip->addFile($filename, $entry);
                        }
                    }
                    closedir($handle);
                }
                $zip->close();
            }
        }
        return $archivePathName;
    }

    /**
     * Remove recursive files from a directory
     *
     * @param string $dir
     * @param bool $deleteParent
     */
    protected function unlinkRecursive($dir, $deleteParent = false)
    {
        if(!$child = @opendir($dir)) {
            return;
        }

        while (false !== ($obj = readdir($child))) {
            if($obj == '.' || $obj == '..') {
                continue;
            }

            if (!@unlink($dir . '/' . $obj)) {
                $this->unlinkRecursive($dir.'/'.$obj, true);
            }
        }
        closedir($child);

        if ($deleteParent) {
            @rmdir($dir);
        }
        return;
    }

    /**
     * Return csv headers for AWB creation
     *
     * @return array
     */
    protected function getCsvHeaders()
    {
        return array ('Tip Serviciu', 'Banca', 'IBAN', 'Nr. Plicuri', 'Nr. Colete', 'Greutate', 'Plata expeditie',
            'Ramburs(bani)', 'Plata ramburs la', 'Valoare declarata', 'Persoana contact expeditor', 'Observatii',
            'Continut', 'Nume destinatar', 'Persoana contact', 'Telefon', 'Fax', 'Email', 'Judet', 'Localitatea',
            'Strada', 'Nr', 'Cod postal', 'Bloc', 'Scara', 'Etaj', 'Apartament', 'Inaltime pachet', 'Latime pachet',
            'Lungime pachet', 'Restituire', 'Centru Cost', 'Optiuni', 'Packing', 'Date personale');
    }

    /**
     * Return module configuration
     *
     * @param string $field
     * @return mixed
     */
    protected function getConfig($field)
    {
        return Mage::getStoreConfig('carriers/bgfancourier/' . $field);
    }
}
