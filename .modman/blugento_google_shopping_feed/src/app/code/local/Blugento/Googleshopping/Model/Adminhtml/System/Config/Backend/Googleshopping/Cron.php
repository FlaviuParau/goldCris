<?php

class Blugento_Googleshopping_Model_Adminhtml_System_Config_Backend_Googleshopping_Cron
    extends Mage_Core_Model_Config_Data
{

    const CRON_MODEL_PATH = 'googleshopping/generate/cron_schedule';
    const CRON_STRING_PATH = 'crontab/jobs/googleshopping_generate/schedule/cron_expr';
    const CRON_RUNMODEL_PATH = 'crontab/jobs/googleshopping_generate/run/model';

    /**
     * @throws Exception
     */
    protected function _afterSave()
    {
        $time = $this->getData('groups/generate/fields/time/value');
        $frequency = $this->getData('groups/generate/fields/frequency/value');
        $custom = $this->getData('groups/generate/fields/frequency_custom/value');

        if ($frequency == 'C' && !empty($custom)) {
            $cronExprString = $custom;
        } else {
            $frequencyWeekly = Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Frequency::CRON_WEEKLY;
            $frequencyMonthly = Blugento_Googleshopping_Model_Adminhtml_System_Config_Source_Frequency::CRON_MONTHLY;
            $storeIds = Mage::helper('googleshopping')->getStoreIds('googleshopping/generate/enabled');
            $count = count($storeIds);
            if ($count > 0) {
                $minute[] = 0;
                $n = floor(60 / $count);
                if ($n == 60) {
                    $n = 0;
                }

                for ($i = 1; $i < $count; $i++) {
                    $min = ($i * $n);
                    $minute[] = $min;
                }

                $minute = implode(',', $minute);
                $cronExprArray = array(
                    $minute,
                    intval($time[0]),
                    ($frequency == $frequencyMonthly) ? '1' : '*',
                    '*',
                    ($frequency == $frequencyWeekly) ? '1' : '*',
                );
                $cronExprString = join(' ', $cronExprArray);
            } else {
                $cronExprString = '';
            }
        }

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_RUNMODEL_PATH, 'path')
                ->setValue((string)Mage::getConfig()->getNode(self::CRON_RUNMODEL_PATH))
                ->setPath(self::CRON_RUNMODEL_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }

}