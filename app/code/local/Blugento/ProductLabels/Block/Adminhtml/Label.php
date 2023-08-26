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
 * @package     Blugento_ProductLabels
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductLabels_Block_Adminhtml_Label extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'blugento_productlabels';
        $this->_controller = 'adminhtml_label';
        $this->_headerText = Mage::helper('blugento_productlabels')->__('Labels');

        parent::__construct();

        $this->_updateButton('add', 'label', Mage::helper('blugento_productlabels')->__('Create Custom Label'));
    }
}