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

class Blugento_FormsGenerator_Model_Template_Filter extends Mage_Widget_Model_Template_Filter{

    public function customFormDirective($construction){

        $output = '';

        if(Mage::getStoreConfig('formsgenerator/general/enable')){
            $id = $this->_getIncludeParameters($construction[2]);

            /** @var Blugento_FormsGenerator_Model_Forms $form */
            $form = Mage::getModel('blugento_formsgenerator/forms')->load($id);

            if($form->getFormStatus() == 1){
                $output = $form->getFormCode();
            }
        }

        return $output;
    }
}