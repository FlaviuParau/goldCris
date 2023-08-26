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
 * @package     Blugento_Campaign
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Campaign_Model_System_Config_Source_Layout
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'one_column_campaign',
                'label'=>Mage::helper('blugento_campaign')->__('One Column Campaign')
            ),
            array(
                'value' => 'two_columns_campaign',
                'label'=>Mage::helper('blugento_campaign')->__('Two Columns Campaign')
            ),
            array(
                'value' => 'two_columns_campaign_category_ajax',
                'label'=>Mage::helper('blugento_campaign')->__('Two Columns Campaign Category Ajax')
            ),
        );
    }
}
