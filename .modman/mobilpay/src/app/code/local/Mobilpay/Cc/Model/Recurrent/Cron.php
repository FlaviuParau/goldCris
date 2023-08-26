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
 * @package     Mobilpay_Cc
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mobilpay_Cc_Model_Recurrent_Cron extends Mage_Core_Model_Abstract
{
    /**
     * Charge recurring profiles
     */
    public function chargeRecurringProfile()
    {
        $_resource = Mage::getSingleton('core/resource');
        $sql = '
			SELECT
				CASE srp.period_unit
					WHEN "day" 			THEN FLOOR(DATEDIFF(NOW(), srp.updated_at) / srp.period_frequency)
					WHEN "week" 		THEN FLOOR(FLOOR(DATEDIFF(NOW(), srp.updated_at) / 7) / srp.period_frequency)
					WHEN "semi_month" 	THEN FLOOR(FLOOR(DATEDIFF(NOW(), srp.updated_at) / 14) / srp.period_frequency)
					WHEN "month" 		THEN FLOOR(PERIOD_DIFF(DATE_FORMAT(NOW(), "%Y%m"), DATE_FORMAT(srp.updated_at, "%Y%m")) - (DATE_FORMAT(NOW(), "%d") < DATE_FORMAT(srp.updated_at, "%d")) / srp.period_frequency)
					WHEN "year" 		THEN FLOOR(YEAR(NOW()) - YEAR(srp.updated_at) - (DATE_FORMAT(NOW(), "%m%d") < DATE_FORMAT(srp.updated_at, "%m%d")) / srp.period_frequency)
				END
				AS billing_count,
				srp.*
			FROM '.$_resource->getTableName('sales_recurring_profile').' AS srp
			WHERE
				srp.method_code = "mobilpay_recurrent" AND
				srp.state = "active" AND
				srp.updated_at <= NOW() AND
				srp.start_datetime <= NOW()
		';

        $connection = $_resource->getConnection('core_read');
        $recurring = Mage::getModel('cc/recurrent');

        foreach ($connection->fetchAll($sql) as $profileArr) {

            $profile = Mage::getModel('sales/recurring_profile')->addData($profileArr);
            $orders = $profile->getResource()->getChildOrderIds($profile);
            $countBillingCycling = count($orders);
            if ($profile->getInitAmount())
                $countBillingCycling--;

            if ($profile->getBillFailedLater()){ // Auto Bill on Next Cycle
                // multi charges
                for ($i = 0; $i < $profile->getBillingCount(); $i++){
                    if ($recurring->chargeRecurringProfile($profile)){
                        $countBillingCycling++;
                    } else {
                        break;
                    }

                    if ($countBillingCycling >= $profile->getPeriodMaxCycles()){
                        $profile->setState(Mage_Sales_Model_Recurring_Profile::STATE_SUSPENDED);
                        $profile->save();
                        break;
                    }
                }
            } else {
                // single charge
                if ($recurring->chargeRecurringProfile($profile))
                    $countBillingCycling++;

                if ($countBillingCycling >= $profile->getPeriodMaxCycles()) {
                    $profile->setState(Mage_Sales_Model_Recurring_Profile::STATE_SUSPENDED);
                    $profile->save();
                }
            }
        }
    }
}