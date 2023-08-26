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

class Blugento_GdprUserData_Helper_Data extends Mage_Core_Helper_Abstract
{
    const FILE_SAVE_PATH = 'GDPR_user_data';

    /**
     *  Check if module is enable
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('blugento_gdpruserdata/general/enabled');
    }

    /**
     *  Get Recaptcha Key
     */
    public function getRecaptchaKey() {
        return Mage::getStoreConfig('blugento_gdpruserdata/general/recaptcha_key');
    }

    /**
     *  Check if the exist request is older than 24 hours
     */
    public function isRequestExpired($date) {
        $requestCreatedTime = strtotime($date);

        if ($requestCreatedTime < (time() - 24*3600)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Concatenate a word before array keys
     */
    public function concatenateWordBeforeKeys($word, $array) {
        foreach ($array as $key => $value) {
            $array[$word.'_'.$key] = $array[$key];
            unset($array[$key]);
        }
        return $array;
    }

    /**
     *  Generate a secret key based on customer email
     */
    public function generateSecretKey($email) {
        return md5($email . '_' . rand(100, 9999));
    }

    /**
     *  Generate a hashed name for zip archive
     */
    public function generateHashedName() {
        $hash = now() . '_' . rand(100, 9999);
        return md5($hash);
    }

    /**
     *  Get export link
     */
    public function getExportLinkUrl($secretKey) {
        return Mage::getUrl('gdpruserdata/exportdata/downloadData/key/' . $secretKey);
    }

    /**
     *  Get GDPR path from media folder
     */
    public function getGdprPath($folder='') {
        if ($folder) {
            return Mage::getBaseDir('media') . DS . self::FILE_SAVE_PATH . DS . $folder;
        } else {
            return Mage::getBaseDir('media') . DS . self::FILE_SAVE_PATH;
        }
    }

    /**
     *  Send transactional email
     */
    public function sendTransactionalEmail($templateId, $recipient, $vars, $error) {

        $incrementId = null;

        // Sender
        $sender['name'] = Mage::getStoreConfig('trans_email/ident_general/name');
        if (!$sender['name']) {
            $sender['name'] = 'Blugento';
        }

        $sender['email'] = Mage::getStoreConfig('trans_email/ident_general/email');
        if (!$sender['email']) {
            $sender['email'] = 'contact@blugento.com';
        }

        // Recipient
        $recipientName = $this->getNameFromEmail($recipient);

        try {
            $storeId = Mage::app()->getStore()->getId();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(true);

            $transactionalEmail = Mage::getModel('core/email_template')
                ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));

            $transactionalEmail->sendTransactional($templateId, $sender, $recipient, $recipientName, $vars, $storeId);
        } catch(Exception $ex) {
            Mage::log($error . ': ' .$ex->getMessage(), null, 'GDPR_user_data.log');
        }
    }

    /**
     *  Get Email Template ID
     */
    public function getEmailTemplate($configPath, $email) {
        if (Mage::getStoreConfig($configPath)) {
            $email = Mage::getStoreConfig($configPath);
        }
        return $email;
    }

    /**
     *  Get name from email
     */
    private function getNameFromEmail($email) {
        $email = explode('@', $email);
        $search = ['_', '.', '-'];
        $name = ucwords(str_replace($search, ' ', $email[0]));

        return $name;
    }

    /**
     *  Anonymize customer email
     */
    public function anonymizeEmail($email) {
        $emailArr = explode('@', $email);

        for ($i=1; $i<strlen($emailArr[0]) - 1; $i++) {
            $emailArr[0][$i] = '*';
        }

        return implode('@', $emailArr);
    }

    /**
     *  Anonymize customer phone number
     */
    public function anonymizePhone($phone) {

        for ($i=4; $i<strlen($phone); $i++) {
            $phone[$i] = '*';
        }

        return $phone;
    }

    /**
     *  Anonymize customer street
     */
    public function anonymizeStreet($street) {

        foreach ($street as $key => $str) {
            $street[$key] = '*****';
        }

        return $street;
    }

    /**
     *  Anonymize data
     */
    public function anonymizeData($data) {

        for ($i=0; $i<strlen($data); $i++) {
            $data[$i] = '*';
        }

        return $data;
    }

    /**
     *  Unlink recursively a directory and all the files from in it
     */
    public function unlinkRecursive($dir, $deleteParent) {
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
}