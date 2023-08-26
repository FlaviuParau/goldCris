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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_DesignCustomiser_Block_Adminhtml_Grunt_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Constructor
     * Setup buttons and remove reset and back buttons
     */
    public function __construct()
    {
        parent::__construct();

        $this->_controller = 'adminhtml_grunt';

        $this->_blockGroup = 'blugento_designcustomiser';
        $this->_headerText = '';

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('save');

        $this->addButton('advanced_mode', array(
            'label'   => $this->__('Clear Grunt Log File'),
            'onclick' => 'confirmSetLocation(\''. $this->__('Do you really want to clear the Grunt log files?') .'\', \''
                . $this->getUrl('*/adminhtml_grunt/clearlogs') .'\')',
            'class'   => 'advanced'
        ));
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/customiser/' . $action);
    }
}
