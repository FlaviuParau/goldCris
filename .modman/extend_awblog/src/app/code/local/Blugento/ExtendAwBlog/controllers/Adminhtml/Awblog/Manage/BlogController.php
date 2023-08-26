<?php

require_once "AW/Blog/controllers/Adminhtml/Awblog/Manage/BlogController.php";

class Blugento_ExtendAwBlog_Adminhtml_Awblog_Manage_BlogController extends AW_Blog_Adminhtml_Awblog_Manage_BlogController{

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('blog/post');
	        $path = Mage::getBaseDir('media') . DS . 'blogpic' . DS;
	        
            if (isset($_FILES['featured_image']['name']) && (file_exists($_FILES['featured_image']['tmp_name']))) {
                try {
                    $uploader = new Varien_File_Uploader('featured_image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);

                    // setAllowRenameFiles(true) -> move your file in a folder the magento way
                    // setAllowRenameFiles(true) -> move your file directly in the $path folder
                    $uploader->setFilesDispersion(false);

                    $uploader->save($path, $_FILES['featured_image']['name']);

                    Mage::helper('blog/image')->processImage($path . $_FILES['featured_image']['name']);

                    $data['featured_image'] = $_FILES['featured_image']['name'];
	
	                $data['featured_image'] = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $data['featured_image']);
	                if (file_exists($path . $data['featured_image'])) {
	                    $dimensions = getimagesize($path . $data['featured_image']);
	                    if ($dimensions[0] < 450) {
		                    Mage::getSingleton('adminhtml/session')->addNotice($this->__('Featured Image is to small. Please upload a bigger image.'));
		                    $data['featured_image'] = '';
	                    }
                    } else {
	                    Mage::getSingleton('adminhtml/session')->addNotice($this->__('Unable to find image in uploaded folder'));
                    }
                } catch (Exception $e) {
                }
            }
            // handle delete image
            else {
                if (isset($data['featured_image']['delete']) && $data['featured_image']['delete'] == 1) {
                    $data['featured_image'] = '';
                } else {
                    unset($data['featured_image']);
                }
            }

            if (isset($data['tags'])) {
                if ($this->getRequest()->getParam('id')) {
                    $model->load($this->getRequest()->getParam('id'));
                    $originalTags = explode(",", $model->getTags());
                } else {
                    $originalTags = array();
                }

                $tags = explode(',', $data['tags']);
                array_walk($tags, 'trim');

                foreach ($tags as $key => $tag) {
                    $tags[$key] = Mage::helper('blog')->convertSlashes($tag, 'forward');
                }
                $tags = array_unique($tags);

                $commonTags = array_intersect($tags, $originalTags);
                $removedTags = array_diff($originalTags, $commonTags);
                $addedTags = array_diff($tags, $commonTags);

                if (count($tags)) {
                    $data['tags'] = trim(implode(',', $tags));
                } else {
                    $data['tags'] = '';
                }
            }

            if (isset($data['stores'])) {
                if ($data['stores'][0] == 0) {
                    unset($data['stores']);
                    $data['stores'] = array();
                    $stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
                    foreach ($stores as $store) {
                        $data['stores'][] = $store->getId();
                    }
                }
            }

            /* set sort_order = 9999 if is empty */
            if (isset($data['sort_order'])) {
                if ($data['sort_order'] == ""){
                    $data['sort_order'] = 9999;
                }
            }

            $model
                ->setData($data)
                ->setId($this->getRequest()->getParam('id'))
            ;
            $groups = implode(',',$data['enable_for_customer_group']);
            $model->setEnableForCustomerGroup($groups);

            try {
                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                if (isset($data['created_time']) && $data['created_time']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                } else {
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                }

                if ($this->getRequest()->getParam('user') == null) {
                    $model
                        ->setUser(
                            Mage::getSingleton('admin/session')->getUser()->getFirstname() . " " . Mage::getSingleton(
                                'admin/session'
                            )->getUser()->getLastname()
                        )
                        ->setUpdateUser(
                            Mage::getSingleton('admin/session')->getUser()->getFirstname() . " " . Mage::getSingleton(
                                'admin/session'
                            )->getUser()->getLastname()
                        )
                    ;
                } else {
                    $model
                        ->setUpdateUser(
                            Mage::getSingleton('admin/session')->getUser()->getFirstname() . " " . Mage::getSingleton(
                                'admin/session'
                            )->getUser()->getLastname()
                        )
                    ;
                }

	            $featuredImage = Mage::getSingleton('blog/post')->load($model->getId())->getFeaturedImage();
	            if (!file_exists($path . $featuredImage)) {
		            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('blog')->__('Unable to find image in uploaded folder'));
		            $model->setFeaturedImage('')->setId($this->getRequest()->getParam('id'));
	            }

                $model->save();

                /* recount affected tags */
                if (isset($data['stores'])) {
                    $stores = $data['stores'];
                } else {
                    $stores = array(null);
                }

                $affectedTags = array_merge($addedTags, $removedTags);

                foreach ($affectedTags as $tag) {
                    foreach ($stores as $store) {
                        if (trim($tag)) {
                            Mage::getModel('blog/tag')->loadByName($tag, $store)->refreshCount();
                        }
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('Post was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blog')->__('Unable to find post to save'));
        $this->_redirect('*/*/');
    }
}