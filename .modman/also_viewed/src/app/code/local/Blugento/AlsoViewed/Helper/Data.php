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
 * @package     Blugento_AlsoViewed
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_AlsoViewed_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getResizeImage($_imgUrl, $width, $height)
	{
		$imageResizedUrl = '';
		
		if ($_imgUrl) {
			if ($width != '' && $height != '') {
				$folder = Mage::getBaseDir('media') . DS . 'also-viewed' . DS . 'resized' . DS . $width . 'x' . $height;
			} else {
				$folder = Mage::getBaseDir('media') . DS . 'also-viewed' . DS . 'resized';
			}
			
			if (!file_exists($folder)) {
				mkdir($folder, 0777);
			}
			
			$imageName    = substr(strrchr($_imgUrl, '/'), 1);
			$imageResized = $folder . DS . $imageName;
			
			$dirImg = Mage::getBaseDir() . str_replace('/', DS, strstr($_imgUrl, '/media'));
			
			if (!file_exists($imageResized) && file_exists($dirImg)) {
				$imageObj = new Varien_Image($dirImg);
				$imageObj->constrainOnly(true);
				$imageObj->keepAspectRatio(true);
				$imageObj->keepTransparency(true);
				$imageObj->backgroundColor(array(255, 255, 255));
				$imageObj->keepFrame(true);
				$imageObj->resize($width ?: $imageObj->getOriginalWidth(), $height ?: $imageObj->getOriginalHeight());
				$imageObj->save($imageResized);
			}
			
			if ($width != '' && $height != '') {
				$imageResizedUrl = Mage::getBaseUrl('media') . 'also-viewed/resized' . DS . $width . 'x' . $height . DS . $imageName;
			} else {
				$imageResizedUrl = Mage::getBaseUrl('media') . 'also-viewed/resized' . DS . $imageName;
			}
		}
		
		return $imageResizedUrl;
	}
}
