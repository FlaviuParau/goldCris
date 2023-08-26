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

class Blugento_DesignCustomiser_Model_Template_Item extends Varien_Object
{
    /**
     * Template id
     * @var string
     */
    protected $_id = '';

    /**
     * Template title
     * @var string
     */
    protected $_title = '';

    /**
     * Template description
     * @var string
     */
    protected $_description = '';

    /**
     * Construct
     * @param mixed $templateData
     */
    public function __construct($templateData)
    {
        parent::__construct();
        $this->_init($templateData);

    }

    /**
     * Initialize data
     * @param mixed $templateData
     * @return Blugento_DesignCustomiser_Model_Template_Item
     */
    protected function _init($templateData)
    {
        if (is_array($templateData)) {
            $this->_initFromArray($templateData);
        }
        return $this;
    }


    /**
     * Set properties data from array
     * @param array $templateData
     * @return Blugento_DesignCustomiser_Model_Template_Item
     */
    protected function _initFromArray($templateData)
    {
        $this->_id          = isset($templateData['id']) ? $templateData['id'] : '';
        $this->_title       = isset($templateData['title']) ? $templateData['title'] : '';
        $this->_description = isset($templateData['description']) ? $templateData['description'] : '';

        return $this;
    }

    /**
     * Get Id
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get title
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
}
