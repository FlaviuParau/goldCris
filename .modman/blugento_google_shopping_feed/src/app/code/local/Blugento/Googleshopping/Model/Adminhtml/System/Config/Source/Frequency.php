<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Frequency
{

    const CRON_DAILY = 'D';
    const CRON_WEEKLY = 'W';
    const CRON_MONTHLY = 'M';
    const CRON_CUSTOM = 'C';

    /**
     * Options array
     *
     * @var array
     */
    public $options = null;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = array(
                array('label' => Mage::helper('adminhtml')->__('Daily'), 'value' => self::CRON_DAILY),
                array('label' => Mage::helper('adminhtml')->__('Weekly'), 'value' => self::CRON_WEEKLY),
                array('label' => Mage::helper('adminhtml')->__('Monthly'), 'value' => self::CRON_MONTHLY),
                array('label' => Mage::helper('adminhtml')->__('-- Custom'), 'value' => self::CRON_CUSTOM)
            );
        }

        return $this->options;
    }

}