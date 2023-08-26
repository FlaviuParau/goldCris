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

class Blugento_FanCourier_Model_Api extends Mage_Core_Model_Abstract
{
    /**
     * API URL
     *
     * @var string
     */
    protected $url = 'https://www.selfawb.ro';

    /**
     * API Subscription Key
     *
     * @var string
     */
    protected $clientId = '';

    /**
     * API Username
     *
     * @var string
     */
    protected $username = '';

    /**
     * API Password
     *
     * @var string
     */
    protected $password = '';

    /**
     * Client
     */
    protected $curl;

    public function __construct()
    {
        $this->clientId = $this->getConfig('client_id');
        $this->username = $this->getConfig('username');
        $this->password = $this->getConfig('password');

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
    }

    /**
     * Generate awb by API call
     *
     * @param array $data
     * @return array|string
     */
    public function generateAwb($data)
    {
        /** @var Blugento_FanCourier_Model_Process $process */
        $process = Mage::getModel('blugento_fancourier/process');

        /** @var Blugento_Fixdiacritics_Helper_Data $diacriticsHelper */
        $diacriticsHelper = Mage::helper('fixdiacritics');

        $shippingAddress = $data['shipping_address'];
        $dataByItems = $process->getDataByShippingItemsQty($data);

        $csvParams = array (
            'service_type' => $data['service'],
            'bank' => strpos($data['service'], 'Cont Colector') !== false ? $diacriticsHelper->sanitizeText($this->getConfig('bank')) : '',
            'iban' => strpos($data['service'], 'Cont Colector') !== false ? $diacriticsHelper->sanitizeText($this->getConfig('iban')) : '',
            'envelopes' => $data['envelope'],
            'parcels' => $data['parcel'],
            'weight' => $data['weight'],
            'awb_payer' => $process->getPayerType($data['shipping_amount'], true, $data),
            'cash_on_delivery' => $dataByItems['cash_on_delivery'],
            'cash_on_delivery_payer' => $process->getPayerType($data['shipping_amount'], true, $data),
            'declared_value' => $dataByItems['declared_value'],
            'sender_name' => Mage::getStoreConfig('trans_email/ident_general/name'),
            'observations' => $diacriticsHelper->sanitizeText($data['observations']),
            'content' => $data['increment_id'],
            'recipient' => $shippingAddress['company'] ? $diacriticsHelper->sanitizeText($shippingAddress['company']) : $diacriticsHelper->sanitizeText($shippingAddress['firstname'] . ' ' . $shippingAddress['lastname']),
            'recipient_contact_person' => $diacriticsHelper->sanitizeText($shippingAddress['firstname'] . ' ' . $shippingAddress['lastname']),
            'telephone' => $shippingAddress['telephone'],
            'fax' => $shippingAddress['fax'],
            'email' => $diacriticsHelper->sanitizeText($shippingAddress['email']),
            'region' => $diacriticsHelper->sanitizeText($shippingAddress['region']),
            'city' => $process->getCity($diacriticsHelper->sanitizeText($shippingAddress['region']), $diacriticsHelper->sanitizeText($shippingAddress['city'])),
            'street' => $diacriticsHelper->sanitizeText($shippingAddress['street']),
            'number' => '',
            'zip' => $shippingAddress['postcode'],
            'block' => '',
            'stair' => '',
            'floor' => '',
            'apartment' => '',
            'height' => 1,
            'width' => 1,
            'length' => 1,
            'refund' => '',
            'cost_center' => '',
            'options' => $process->getAdditionalOptions(),
            'packing' => '',
            'personal_information' => ''
        );

        $file = $process->createCsvFile($csvParams, $data['increment_id']);

        $params = array (
            'username' => $this->username,
            'client_id' => $data['client_id'] ?: $this->clientId,
            'user_pass' => $this->password,
            'fisier' => curl_file_create($file)
        );

        $response = $this->makeCall('import_awb_integrat.php', $params, 'csv');
        $response = explode(',', $response[0]);

        if ($response[1] == 0) {
            $result['error'] = Mage::helper('blugento_fancourier')->__('Error(s) when generate AWB: ') . $response[2];
        } else {
            $result = $response[2];
        }

        //remove csv file
        $process->removeFiles($file);

        return $result;
    }

    /**
     * Get/return shipping price from API
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return array|string
     */
    public function getShippingPrice(Mage_Shipping_Model_Rate_Request $request)
    {
        /** @var Blugento_FanCourier_Model_Process $process */
        $process = Mage::getModel('blugento_fancourier/process');

        $parcels = $this->getConfig('delivery_type') == 'parcel' ? 1 : 0;
        $envelopes = $this->getConfig('delivery_type') == 'envelope' ? 1 : 0;
        $declaredValue = 0;

        if ($this->getConfig('order_amount_price_calculation')) {
            $order = array (
                'shipping_amount' => 0,
                'shipping_tax_amount' => 0,
            );

            $declaredValue = $process->getDeclaredValue($request->getPackageValue(), $order);
        }

        $region = Mage::getModel('directory/region')->load($request->getDestRegionId())->getName();

        $params = array (
            'username' => $this->username,
            'client_id' => $this->clientId,
            'user_pass' => $this->password,
            'serviciu' => $this->getConfig('cash_on_delivery') == 'bank' ? $this->getConfig('service_bank') : $this->getConfig('service_cash'),
            'plata_la' => $process->getPayerType(0),
            'localitate_dest' => $process->getCity($region, $request->getDestCity()),
            'judet_dest' => $region,
            'plicuri' => $envelopes,
            'colete' => $parcels,
            'greutate' => $request->getPackageWeight() > 0 ? ceil($request->getPackageWeight()) : $this->getConfig('default_weight'),
            'lungime' => 0,
            'latime' => 0,
            'inaltime' => 0,
            'val_decl' => $declaredValue,
            'plata_ramburs' => $process->getPayerType(0),
            'options' => $process->getAdditionalOptions(),
        );

        return $this->makeCall('tarif.php', $params);
    }

    /**
     * Get/return AWB label from Fan Courier platform
     *
     * @param string $awb
     * @param string|null $clientId
     * @return array|mixed|string
     */
    public function getAwbLabel($awb, $clientId = null)
    {
        $params = array (
            'username' => $this->username,
            'client_id' => $clientId ?: $this->clientId,
            'user_pass' => $this->password,
            'language' => 'ro',
            'nr' => $awb
        );

        if ($this->getConfig('epod')) {
            $params['page'] = 'A6';
        }

        return $this->makeCall('view_awb_integrat_pdf.php', $params, 'pdf');
    }

    /**
     * Return list of Fan Courier services
     *
     * @return mixed|string
     */
    public function getServices()
    {
        if (!$this->username || !$this->clientId || !$this->password) {
            return Mage::helper('blugento_fancourier')->__('Fan Courier: Username, password and client id are required.');
        }

        $params = array (
            'username' => $this->username,
            'client_id' => $this->clientId,
            'user_pass' => $this->password,
        );

        return $this->makeCall('export_servicii_integrat.php', $params, 'csv');
    }

    /**
     * Make API call
     *
     * @param string $method
     * @param array $params
     * @param string $returnType
     * @param int $timeout - seconds
     * @return mixed
     */
    protected function makeCall($method, $params, $returnType = '', $timeout = 0)
    {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($this->curl, CURLOPT_URL, $this->url . '/' . $method);

        if ($timeout > 0) {
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        }

        $result = curl_exec($this->curl);

        if ($returnType == 'csv') {
            $data = str_getcsv($result, "\n");
        } elseif ($returnType == 'pdf') {
            $data = $result;
        } else {
            $data = $result;
        }

        return $data;
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
