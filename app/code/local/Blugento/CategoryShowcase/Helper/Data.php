<?php

class Blugento_CategoryShowcase_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getResizeCategoryImage($_imgUrl, $width, $height)
    {
        $imageResizedUrl = '';

        if ($_imgUrl) {
        	if ($width != '' && $height != '') {
		        $folder = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . 'resized' . DS . $width . 'x' . $height;
	        } else {
		        $folder = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . 'resized';
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
                $imageObj->keepFrame(true);
                $imageObj->resize($width ?: $imageObj->getOriginalWidth(), $height ?: $imageObj->getOriginalHeight());
                $imageObj->save($imageResized);
            }
	
	        if ($width != '' && $height != '') {
		        $imageResizedUrl = Mage::getBaseUrl('media') . 'catalog/category/resized' . DS . $width . 'x' . $height . DS . $imageName;
	        } else {
		        $imageResizedUrl = Mage::getBaseUrl('media') . 'catalog/category/resized' . DS . $imageName;
	        }
        }

        return $imageResizedUrl;
    }
}
