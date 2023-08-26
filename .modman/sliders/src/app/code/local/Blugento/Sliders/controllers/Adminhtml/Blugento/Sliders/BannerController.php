<?php
/**
 * Blugento Sliders
 * Controller Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Sliders
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @author Daniel Gheoltan <daniel.gheoltan@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Sliders_Adminhtml_Blugento_Sliders_BannerController extends Blugento_Sliders_Controller_Adminhtml_Abstract
{
    /**
     * Redirect to Sliders dashboard
     *
     * @return $this
     */
    public function indexAction()
    {
        return $this->_redirect('*/blugento_sliders');
    }

    /**
     * Forward to the edit action so the user can add a new banner
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Display the edit/add form
     *
     */
    public function editAction()
    {
        $this->loadLayout();

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $this->_title('Edit Banner');

            if ($banner = $this->_initBannerModel()) {
                $this->_title($banner->getTitle());
            }
        }

        $this->renderLayout();
    }

    /**
     * Save the banner
     *
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('banner')) {
            $banner = Mage::getModel('blugento_sliders/banner')
                ->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {

                $this->_handleImageUpload($banner);
                $this->_handleTabletImageUpload($banner);
                $this->_handleMobileImageUpload($banner);

                $banner->save();
                $this->_getSession()->addSuccess($this->__('Banner was saved'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }

            if ($this->getRequest()->getParam('back') && $banner->getId()) {
                $this->_redirect('*/*/edit', array('id' => $banner->getId()));
                return;
            }
        } else {
            $this->_getSession()->addError($this->__('There was no data to save'));
        }

        $this->_redirect('*/blugento_sliders');
    }

    /**
     * Upload an image and assign it to the model
     *
     * @param Blugento_Sliders_Model_Banner $banner
     * @param string $field = 'image'
     */
    protected function _handleImageUpload(Blugento_Sliders_Model_Banner $banner, $field = 'image')
    {
        $data = $banner->getData($field);

        if (isset($data['value'])) {
            $banner->setData($field, $data['value']);
        }

        if (isset($data['delete']) && $data['delete'] == '1') {
            $banner->setData($field, '');
        }

        if ($filename = Mage::helper('blugento_sliders/image')->uploadImage($field)) {
            $banner->setData($field, $filename);
        }
    }
	
	/**
	 * Upload tablet image and assign it to the model
	 *
	 * @param Blugento_Sliders_Model_Banner $banner
	 * @param string $field = 'tablet_image'
	 */
	protected function _handleTabletImageUpload(Blugento_Sliders_Model_Banner $banner, $field = 'tablet_image')
	{
		$data = $banner->getData($field);
		
		if (isset($data['value'])) {
			$banner->setData($field, $data['value']);
		}
		
		if (isset($data['delete']) && $data['delete'] == '1') {
			$banner->setData($field, '');
		}
		
		if ($filename = Mage::helper('blugento_sliders/image')->uploadTabletImage($field)) {
			$banner->setData($field, $filename);
		}
	}
	
	/**
	 * Upload mobile image and assign it to the model
	 *
	 * @param Blugento_Sliders_Model_Banner $banner
	 * @param string $field = 'mobile_image'
	 */
	protected function _handleMobileImageUpload(Blugento_Sliders_Model_Banner $banner, $field = 'mobile_image')
	{
		$data = $banner->getData($field);
		
		if (isset($data['value'])) {
			$banner->setData($field, $data['value']);
		}
		
		if (isset($data['delete']) && $data['delete'] == '1') {
			$banner->setData($field, '');
		}
		
		if ($filename = Mage::helper('blugento_sliders/image')->uploadMobileImage($field)) {
			$banner->setData($field, $filename);
		}
	}

    /**
     * Delete a banner
     *
     */
    public function deleteAction()
    {
        if ($bannerId = $this->getRequest()->getParam('id')) {
            $banner = Mage::getModel('blugento_sliders/banner')->load($bannerId);

            if ($banner->getId()) {
                try {
                    $banner->delete();
                    $this->_getSession()->addSuccess($this->__('The banner was deleted.'));
                }
                catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/blugento_sliders');
    }

    /**
     * Delete multiple banners in one go
     *
     */
    public function massDeleteAction()
    {
        $bannerIds = $this->getRequest()->getParam('banner');

        if (!is_array($bannerIds)) {
            $this->_getSession()->addError($this->__('Please select some banners.'));
        } else {
            if (!empty($bannerIds)) {
                try {
                    foreach ($bannerIds as $bannerId) {
                        $banner = Mage::getSingleton('blugento_sliders/banner')->load($bannerId);

                        Mage::dispatchEvent('blugento_sliders_controller_banner_delete', array('blugento_sliders_banner' => $banner));

                        $banner->delete();
                    }

                    $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($bannerIds)));
                }
                catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/blugento_sliders');
    }

    /**
     * Initialise the banner model
     *
     * @return null|Blugento_Sliders_Model_Banner
     */
    protected function _initBannerModel()
    {
        if ($bannerId = $this->getRequest()->getParam('id')) {
            $banner = Mage::getModel('blugento_sliders/banner')->load($bannerId);

            if ($banner->getId()) {
                Mage::register('blugento-sliders-banner', $banner);
                Mage::getSingleton('admin/session')->setActiveTabId('banner');
            }
        }

        return Mage::registry('blugento-sliders-banner');
    }

    /**
     * Determine ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blugento_adminmenu/blugento_sliders');
    }
}
