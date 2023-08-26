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

class Blugento_FbConversion_Model_Api extends Mage_Core_Model_Abstract
{
    /**
     * Send event to Facebook
     *
     * @param Blugento_FbConversion_Model_Event $event
     */
    public function sendEvent($event)
    {
        $userData = [
            'client_ip_address' => $event->getClientIpAddress(),
            'client_user_agent' => $event->getClientUserAgent(),
//            'fbp' => $event->getFbp(),
        ];

        if ($emailHash = $event->getUserEmailHash()) {
            $userData['em'] = [$emailHash];
        }

        if ($phoneHash = $event->getUserPhoneHash()) {
            $userData['ph'] = [$phoneHash];
        }

        if ($fbc = $event->getFbc()) {
            $userData['fbc'] = $fbc;
        }

        if ($userExternalId = $event->getUserExternalId()) {
            $userData['external_id'] = [$emailHash];
        }

        $data[] = [
            'event_name' => $event->getName(),
            'event_time' => $event->getTime(),
            'event_id' => $event->getEventId(),
            'user_data' => $userData,
            'contents' => $event->getContent() ? unserialize($event->getContent()) : [],
            'custom_data' => $event->getCustomData() ? unserialize($event->getCustomData()) : [],
            'event_source_url' => $event->getSourceUrl(),
            'action_source' => $event->getActionSource(),
        ];

        $params['data'] = json_encode($data);

        if (Mage::getStoreConfig('blugento_fbconversion/general/test_mode', $event->getStoreId())) {
            $params['test_event_code'] = Mage::getStoreConfig('blugento_fbconversion/general/test_event_code', $event->getStoreId());
        }

        $result = $this->makeCall($params, $event->getStoreId());
        $result = json_decode($result, 1);

        if (isset($result['error'])) {
            $message = [];
            if (isset($result['error']['message'])) {
                $message[] = $result['error']['message'];
            }
            if (isset($result['error']['error_user_msg'])) {
                $message[] = $result['error']['error_user_msg'];
            }

            if (count($message)) {
                Mage::log('Facebook Conversion API Error: ' . implode(' ', $message));
            }
        }
    }

    /**
     * Make API call
     *
     * @param array $params
     * @param int $storeId
     * @return mixed
     */
    protected function makeCall($params, $storeId)
    {
        $helper = Mage::helper('blugento_fbconversion');

        $url = rtrim($helper->getConfigurations('url', $storeId), '/');
        $token = $helper->getConfigurations('token', $storeId);

        $finalUrl = $url . '?access_token=' . $token;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_URL, $finalUrl);

        $result = curl_exec($curl);

        return $result;
    }
}
