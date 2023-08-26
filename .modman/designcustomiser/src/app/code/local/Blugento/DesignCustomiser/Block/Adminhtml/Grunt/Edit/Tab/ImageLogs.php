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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Block_Adminhtml_Grunt_Edit_Tab_ImageLogs extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Prepare page form with the custom functionality
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('logs_');
        
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('blugento_designcustomiser')->__('Grunt Images Logs'),
            'class'  => 'fieldset-wide',
        ));
        
        $fieldset->addField('logs-images', 'textarea', array(
            'name'      => 'logs-images',
            'class'     => 'fieldset-wide',
            'style'     => 'height: 24em;',
            'disabled'  => true,
            'label'     => Mage::helper('blugento_designcustomiser')->__('Logs'),
            'title'     => Mage::helper('blugento_designcustomiser')->__('Logs')
        ));

        $model = Mage::helper('blugento_designcustomiser')->getGruntLogsImageDefinitionModel();
        $data = $model ? $model->loadContent() : '';

        $form->setValues(array('logs-images' => $data));
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('blugento_designcustomiser')->__('View Images Log File');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blugento_designcustomiser')->__('View Images Log File');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
