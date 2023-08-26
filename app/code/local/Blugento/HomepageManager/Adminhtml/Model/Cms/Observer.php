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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Adminhtml_Model_Cms_Observer
{
    public function changeContent($observer)
    {
        $event = $observer->getEvent();
        $form = $event->getForm();

        try {
            $model = Mage::registry('cms_page');
            $data = $model->getData();
            if ($data['identifier'] == 'home') {
                $fieldset = $form->addFieldset('blugento_tinymce', array());

                $fieldset->addField('blugento_tinymce_home', 'hidden', array(
                    'name'      => 'blugento_tinymce_home',
                    'value'     => 'home'
                ));
            }
        } catch (Exception $e) {
        }

        return $this;
    }
}
