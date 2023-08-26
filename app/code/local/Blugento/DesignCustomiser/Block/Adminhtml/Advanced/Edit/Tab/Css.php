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

class Blugento_DesignCustomiser_Block_Adminhtml_Advanced_Edit_Tab_Css extends Mage_Adminhtml_Block_Widget_Form
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
        $form->setHtmlIdPrefix('css_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('blugento_designcustomiser')->__('Final CSS'),
            'class'  => 'fieldset-wide',
        ));

        $fieldset->addField('link', 'prevnext', array(
            'label'     => Mage::helper('blugento_designcustomiser')->__('Revisions'),
            'after_element_html' => ''
        ));

        $fieldset->addField('final_css', 'textarea', array(
            'name'      => 'final_css',
            'label'     => Mage::helper('blugento_designcustomiser')->__('Your CSS code here'),
            'title'     => Mage::helper('blugento_designcustomiser')->__('Your CSS code here'),
            'class'     => 'validate-css',
            'after_element_html' => '<script>
                var parser = new parserlib.css.Parser({
                    starHack: true,
                    ieFilters: true,
                    strict: true
                });
                document.observe("dom:loaded", function() {
                    Validation.add(\'validate-css\', \'Please enter valid CSS\', function(v) {
                        try {
                            parser.parse($F(\'css_final_css\'));
                        } catch (ex) {
                            alert(ex);
                            return false;
                        }
                        return true;
                    });
                });
            </script>'
        ));

        $backupRevision = Mage::app()->getRequest()->getParam('revision');

        $helper = Mage::helper('blugento_designcustomiser');
        $data = $helper->getFinalCssDefinitionModel()->loadContent($backupRevision);

        $form->setValues(array(
            'final_css' => $data
        ));

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
        return Mage::helper('blugento_designcustomiser')->__('Final CSS');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blugento_designcustomiser')->__('Final CSS');
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
