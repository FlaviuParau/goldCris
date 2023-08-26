<?php
/**
 * Blugento Sliders
 * Widget Block Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Block_Widget_Slider extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface
{
    /**
     * Produce banner slider rendered as html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        $bannerCode = $this->getData('banner_code');

        if (empty($bannerCode)) {
            return $html;
        }
        $html .= Mage::app()->getLayout()->createBlock('blugento_sliders/view')->setGroupCode($bannerCode)->setDisplayControls(1)->toHtml();

        return $html;
    }
}
