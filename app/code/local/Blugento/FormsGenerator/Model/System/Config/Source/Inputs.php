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
 * @package     Blugento_FormsGenerator
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_FormsGenerator_Model_System_Config_Source_Inputs
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    protected $_options;
    protected $_allowedOptions;

    public function toOptionArray()
    {
        $this->_options = [
            [
                'value' => 'checkbox',
                'label' => 'Checkbox'
            ],
            [
                'value' => 'select',
                'label' => 'Select'
            ],
            [
                'value' => 'hidden',
                'label' => 'Hidden'
            ],
            [
                'value' => 'password',
                'label' => 'Password'
            ],
            [
                'value' => 'radio',
                'label' => 'Radio'
            ],
            [
                'value' => 'reset',
                'label' => 'Reset'
            ],
            [
                'value' => 'submit',
                'label' => 'Submit'
            ],
            [
                'value' => 'text',
                'label' => 'Text'
            ],
            [
                'value' => 'textarea',
                'label' => 'Textarea'
            ],
            [
                'value' => 'file',
                'label' => 'File'
            ]
        ];

        return $this->_options;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    public static function getOptionArray(){
        $allowedOptions = Mage::helper('blugento_formsgenerator')->getAllowedInputTypes();

        foreach($allowedOptions as $key => $value){
            $allowedOptions[$key] = ucwords($value);
        }

        return $allowedOptions;
    }
}