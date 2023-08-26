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

class Blugento_FanCourier_Model_System_Config_Source_ServiceCash
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $services = $this->getData();

        $options = array();
        if (is_array($services)) {
            foreach ($services as $service) {
                if (strpos($service, 'Cont Colector') === false) {
                    $options[] = array(
                        'value' => $service,
                        'label' => $service,
                    );
                }
            }
        }

        return $options;
    }

    /**
     * Get services options
     *
     * @return mixed
     */
    protected function getData()
    {
        /** @var Blugento_FanCourier_Model_Process $process */
        $process = Mage::getModel('blugento_fancourier/process');

        return $process->getServices();
    }
}