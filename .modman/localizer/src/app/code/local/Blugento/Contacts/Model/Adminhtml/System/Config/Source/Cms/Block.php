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
 * @package     Blugento_Contacts
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Contacts_Model_Adminhtml_System_Config_Source_Cms_Block
{
    protected $_options;

    /**
     * Convert block collection to array for select options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $collection = Mage::getResourceModel('cms/block_collection')
                ->setOrder('title', Varien_Data_Collection_Db::SORT_ORDER_ASC)
            ;

            // prepend empty value
            $res = array('' => array(
                'value' => '',
                'label' => Mage::helper('adminhtml')->__('-- Please select --'),
            ));

            // add all identifiers, combining duplicates
            foreach ($collection as $item) {
                $identifier = $item->getData('identifier');
                $label      = $item->getData('title');

                if (array_key_exists($identifier, $res) && ($res[$identifier]['label'] !== $label)) {
                    $label .= ' / ' . $res[$identifier]['label'];
                }

                $res[$identifier] = array(
                    'value' => $identifier,
                    'label' => $label,
                );
            }

            $this->_options = array_values($res);
        }

        return $this->_options;
    }
}
