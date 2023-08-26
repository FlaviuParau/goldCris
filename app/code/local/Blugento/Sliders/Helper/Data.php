<?php
/**
 * Blugento Sliders
 * Helper Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Determine whether the extension is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('blugento_sliders/settings/enabled');
    }
	
	/**
	 * Resize banner image
	 *
	 * @param $_imgUrl
	 * @param null $width
	 * @param null $height
	 * @return string
	 */
	public function getResizeBannerImage($_imgUrl, $width = null, $height = null)
    {
        $imageResizedUrl = '';

        if ($_imgUrl && $width) {
            $folder = Mage::getBaseDir('media') . DS . 'blugento_sliders'. DS . 'resized/' . $width;
            
            if ($height) {
            	$folder .= 'x' . $height;
            }

            if (!file_exists($folder)) {
                mkdir($folder, 0777);
            }

            // get image name
            $imageName = substr(strrchr($_imgUrl, '/'), 1);

            // resized image path (media/catalog/category/resized/IMAGE_NAME)
            $imageResized = $folder . DS . $imageName;

            // changing image url into direct path
            $dirImg = Mage::getBaseDir() . str_replace('/', DS, strstr($_imgUrl, '/media'));

            // if resized image doesn't exist, save the resized image to the resized directory
            if (!file_exists($imageResized) && file_exists($dirImg)) {
                $imageObj = new Varien_Image($dirImg);
                $imageObj->constrainOnly(true);
                $imageObj->keepAspectRatio(true);
                $imageObj->keepTransparency(true);
                $imageObj->backgroundColor(array(255, 255, 255));
                $imageObj->keepFrame(false);
                $imageObj->resize($width, $height);
                $imageObj->save($imageResized);
            }

            $imageResizedUrl = Mage::getBaseUrl('media') . 'blugento_sliders/resized/' . $width;
            
            if ($height) {
            	$imageResizedUrl .= 'x' . $height;
            }
            
            $imageResizedUrl .= '/' . $imageName;

            return $imageResizedUrl;
        }
        return $_imgUrl;
    }
}
