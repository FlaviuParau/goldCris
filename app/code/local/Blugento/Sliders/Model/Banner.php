<?php
/**
 * Blugento Sliders
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Model_Banner extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('blugento_sliders/banner');
    }

    /**
     * Retrieve the group model associated with the banner
     *
     * @return Blugento_Sliders_Model_Group|false
     */
    public function getGroup()
    {
        if (!$this->hasGroup()) {
            $this->setGroup($this->getResource()->getGroup($this));
        }

        return $this->getData('group');
    }

    /**
     * Determine whether the banner has a valid URL
     *
     * @return bool
     */
    public function hasUrl()
    {
        return strlen($this->getUrl()) > 1;
    }

    /**
     * Retrieve the alt text
     * If the alt_text field is empty, use the title field
     *
     * @return string
     */
    public function getAltText()
    {
        return $this->getData('alt_text') ? $this->getData('alt_text') : $this->getTitle();
    }

    /**
     * Retrieve the image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        if (!$this->hasImageUrl()) {
            $this->setImageUrl(Mage::helper('blugento_sliders/image')->getImageUrl($this->getImage()));
        }

        return $this->getData('image_url');
    }
	
	/**
	 * Retrieve tablet image URL
	 *
	 * @return string
	 */
	public function getTabletImageUrl()
	{
		if (!$this->hasTabletImageUrl()) {
			$this->setTabletImageUrl(Mage::helper('blugento_sliders/image')->getTabletImageUrl($this->getTabletImage()));
		}
		
		return $this->getData('tablet_image_url');
	}
	
	/**
	 * Retrieve mobile image URL
	 *
	 * @return string
	 */
	public function getMobileImageUrl()
	{
		if (!$this->hasMobileImageUrl()) {
			$this->setMobileImageUrl(Mage::helper('blugento_sliders/image')->getMobileImageUrl($this->getMobileImage()));
		}
		
		return $this->getData('mobile_image_url');
	}

    /**
     * Retrieve the background color
     * If the background_color field is empty, use the title field
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->getData('background_color') ? $this->getData('background_color') : 'transparent';
    }
	
	/**
	 * Retrieve image width for tablet
	 * If the tablet_banner_width field is empty, use default
	 *
	 * @return integer
	 */
	public function getTabletImageWidth()
	{
		return $this->getData('tablet_banner_width') ? $this->getData('tablet_banner_width') : '1024';
	}
	
	/**
	 * Retrieve image height for tablet
	 * If the tablet_banner_height field is empty, use null
	 *
	 * @return integer
	 */
	public function getTabletImageHeight()
	{
		return $this->getData('tablet_banner_height') ? $this->getData('tablet_banner_height') : '';
	}
	
	/**
	 * Retrieve image width for mobile
	 * If the mobile_banner_width field is empty, use default
	 *
	 * @return integer
	 */
	public function getMobileImageWidth()
	{
		return $this->getData('mobile_banner_width') ? $this->getData('mobile_banner_width') : '640';
	}
	
	/**
	 * Retrieve image height for mobile
	 * If the mobile_banner_height field is empty, use null
	 *
	 * @return integer
	 */
	public function getMobileImageHeight()
	{
		return $this->getData('mobile_banner_height') ? $this->getData('mobile_banner_height') : '';
	}

    /**
     * Retrieve the URL
     * This converts relative URL's to absolute
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->_getData('url')) {
            if (strpos($this->_getData('url'), 'http://') === false && strpos($this->_getData('url'), 'https://') === false) {
                $this->setUrl(Mage::getBaseUrl() . ltrim($this->_getData('url'), '/ '));
            }
        }

        return $this->_getData('url');
    }
}
