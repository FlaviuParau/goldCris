<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree. An additional grant
 * of patent rights can be found in the PATENTS file in the code directory.
 */

class Facebook_AdsExtension_Adminhtml_FbpixelController
  extends Mage_Adminhtml_Controller_Action {

  public function indexAction() {
    $this->loadLayout();
    $this->renderLayout();
  }

  public function ajaxAction() {

      Mage::log('a', null, 'fb-pxl.log');
    if (Mage::app()->getRequest()->isAjax()) {
        Mage::log('b', null, 'fb-pxl.log');
      $old_pixel_id =
        Mage::getStoreConfig('facebook_ads_toolbox/fbpixel/id');
        Mage::log('old id: ' . $old_pixel_id, null, 'fb-pxl.log');
      $response = array(
        'success' => false,
        'pixelId' => $old_pixel_id,
        'pixelUsePii' =>
          Mage::getStoreConfig('facebook_ads_toolbox/fbpixel/pixel_use_pii'),
      );
      $pixel_id = $this->getRequest()->getParam('pixelId');
      $pixel_use_pii = $this->getRequest()->getParam('pixelUsePii');
        Mage::log('new id: ' . $pixel_id, null, 'fb-pxl.log');
      if ($pixel_id && $this->isPixelIdValid($pixel_id)) {
          Mage::log('d', null, 'fb-pxl.log');
        Mage::getModel('core/config')->saveConfig(
          'facebook_ads_toolbox/fbpixel/id',
          $pixel_id);
          Mage::log('pixel id saved: ' . Mage::getStoreConfig('facebook_ads_toolbox/fbpixel/id'), null, 'fb-pxl.log');
        Mage::getModel('core/config')->saveConfig(
          'facebook_ads_toolbox/fbpixel/pixel_use_pii',
          $pixel_use_pii === 'true'? '1' : '0');
        $response['success'] = true;
        $response['pixelId'] = $pixel_id;
        $response['pixelUsePii'] = $pixel_use_pii;

        if ($old_pixel_id != $pixel_id) {
          Mage::getModel('core/config')->saveConfig(
            'facebook_ads_toolbox/fbpixel/install_time',
            Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
        }
      }

      $this->getResponse()->setHeader('Content-type', 'application/json');
      $this->getResponse()->setBody(
        Mage::helper('core')->jsonEncode($response)
      );
    } else {
        Mage::log('c', null, 'fb-pxl.log');
      Mage::app()->getResponse()->setRedirect(
        Mage::helper('adminhtml')->getUrl(
          'adminhtml/fbpixel/index'));
    }
  }

  public function isPixelIdValid($pixel_id) {
    return preg_match("/^\d{1,20}$/", $pixel_id) === 1;
  }
}
