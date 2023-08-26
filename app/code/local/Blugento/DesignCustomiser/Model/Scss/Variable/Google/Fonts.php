<?php
/**
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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Model_Scss_Variable_Google_Fonts
    extends Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
{
    const GOOGLE_FONTS_API_KEY = 'AIzaSyCEmI3ik3osOEaJmjXa4ecgE8-WWkUoDW8';

    /**
     * Variable input type form
     */
    protected $_inputTypeForm = 'multiselect';

    /**
     * Font Family values
     * @var array
     */
    private $_values = array();

    /**
     * Construct
     * @param mixed $variableData
     */
    public function __construct($variableData)
    {
        parent::__construct($variableData);
        $this->_importGoogleFonts();
    }

    /**
     * Get type
     * @return string
     */
    public function getType()
    {
        return Mage::helper('blugento_designcustomiser')->getAllowedVariableType('google_fonts');
    }

    /**
     * Validate save value
     * @return boolean
     */
    public function validate()
    {
        $values = explode('**', $this->getSaveValue());
        if ((count($values) == 1) && ($values[0] == '')) {
            return true;
        }
        foreach ($values as $value) {
            if (!$value || ($value != Mage::helper('blugento_designcustomiser')->getVariableAutoValue() && !in_array($value, array_keys($this->_values)))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get Google Fonts
     */
    private function _getGoogleFonts()
    {
        $ch = curl_init('https://www.googleapis.com/webfonts/v1/webfonts?key=' . self::GOOGLE_FONTS_API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($ch);

        return $content;
    }

    /**
     * Import Google Fonts
     */
    private function _importGoogleFonts()
    {
        $cacheKey = 'BLUGENTO_DESIGNCUSTOMISER_GOOGLE_FONTS';

        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $fonts = json_decode($this->_getGoogleFonts(), true);

            $options = array();
            foreach ($fonts['items'] as $k => $v) {
                $options[$v['family']] = $v['family'];
            }

            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('BLUGENTO_DESIGNCUSTOMISER'));
            }
        }

        $this->_values = $options;
    }

    /**
     * Get options
     * @return array
     */
    public function getOptions()
    {
        return $this->_values;
    }

    /**
     * Set Save Value
     * @param string $value
     * @return Blugento_DesignCustomiser_Model_Scss_Variable_Abstract
     */
    public function setSaveValue($value)
    {
        if (is_array($value)) {
            $value = implode('**', $value);
        }
        $this->_saveValue = $value;
        return $this->_saveValue;
    }
}
