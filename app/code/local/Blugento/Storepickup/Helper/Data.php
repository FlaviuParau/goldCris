<?php
/**
 * Blugento Store Pickup Shipping Method
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Storepickup
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Storepickup_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getMethodCode($title, $index){

        return str_replace(' ', '_', strtolower($title)).'_'.$index;
    }

    public function getMethodTitle($title, $address = null, $information = null){

        if($address){
            $title .= ', '.$address;
        }

        if($information){
            $title .= ', '.$information;
        }

        return $title;
    }
}
