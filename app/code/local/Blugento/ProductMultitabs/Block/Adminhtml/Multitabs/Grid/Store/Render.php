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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Block_Adminhtml_Multitabs_Grid_Store_Render extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Return store
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $rowStores = $row->getStores();

        if (!is_array($rowStores)) {
            $rowStores = explode(',', $rowStores);
        }

        $data = array();
        if($rowStores != '' and $rowStores == 0) {
            $allStores = Mage::app()->getStores();

            foreach ($allStores as $aStore) {
                $data[] = $aStore->getName();
            }
        } else {
            foreach ($rowStores as $sto) {
                $data[] = Mage::getModel('core/store')->load($sto)->getName();
            }
        }

        return implode(',', $data);
    }
}