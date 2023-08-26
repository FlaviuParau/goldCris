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

class Blugento_Sliders_Model_Group extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('blugento_sliders/group');
    }

    /**
     * Load the model based on the code field
     *
     * @param string $code
     * @return Blugento_Sliders_Model_Group
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'code');
    }

    /**
     * Determine whether the group is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getIsEnabled();
    }

    /**
     * Determine whether the group has wide layout
     *
     * @return bool
     */
    public function getIsWide()
    {
        if (!$this->_getData('is_wide')) {
            $this->setIsWide(0);
        }

        return (int)$this->_getData('is_wide');
    }

    /**
     * Retrieve a collection of banners associated with this group
     *
     * @return Blugento_Sliders_Model_Mysql4_Banner_Group
     */
    public function getBannerCollection()
    {
        if (!$this->hasBannerCollection()) {
            $this->setBannerCollection($this->getResource()->getBannerCollection($this));
        }

        return $this->_getData('banner_collection');
    }

    /**
     * Retrieve the amount of banners in this group
     *
     * @return int
     */
    public function getBannerCount()
    {
        if (!$this->hasBannerCount()) {
            $this->setBannerCount($this->getBannerCollection()->count());
        }

        return $this->_getData('banner_count');
    }

    /**
     * Determine whether animation is enabled for this group
     *
     * @return bool
     */
    public function isAnimationEnabled()
    {
        return $this->getCarouselAnimate() ? true : false;
    }

    /**
     * Retrieve the carousel duration for this group
     *
     * @return int
     */
    public function getCarouselDuration()
    {
        if (!$this->_getData('carousel_duration')) {
            $this->setCarouselDuration(400);
        }

        return (int)$this->_getData('carousel_duration');
    }

    /**
     * Retrieve the carousel duration for this group
     *
     * @return int
     */
    public function getCarouselAuto()
    {
        if ($this->_getData('carousel_auto') == '') {
            $this->setCarouselAuto(1);
        }

        return (int)$this->_getData('carousel_auto');
    }

    /**
     * Retrieve the carousel auto speed for this group
     *
     * @return int
     */
    public function getCarouselAutospeed()
    {
        if (!$this->_getData('carousel_autospeed')) {
            $this->setCarouselAutospeed(3000);
        }

        return (int)$this->_getData('carousel_autospeed');
    }

    /**
     * Retrieve the carousel effect for this group
     * If no carousel effect is set, get the carousel effect from the config
     *
     * @return string
     */
    public function getCarouselEffect()
    {
        if (!$this->_getData('carousel_effect')) {
            $this->setCarouselEffect('scroll');
        }

        return $this->_getData('carousel_effect');
    }

    /**
     * Retrieve the controls position for this group
     *
     * @return string
     */
    public function getControlsPosition()
    {
        if (!$this->_getData('controls_position')) {
            $this->setControlsPosition('controls-middle');
        }

        return $this->_getData('controls_position');
    }

    /**
      * Retrieve animation data
      * This is used to populate the Adminhtml form
      *
      * @return array
      */
    public function getAnimationData()
    {
        return array(
            'carousel_animate'   => (int)$this->isAnimationEnabled(),
            'carousel_duration'  => $this->getCarouselDuration(),
            'carousel_auto'      => $this->getCarouselAuto(),
            'carousel_autospeed' => $this->getCarouselAutospeed(),
            'carousel_effect'    => $this->getCarouselEffect(),
            'controls_position'  => $this->getControlsPosition()
        );
    }
}
