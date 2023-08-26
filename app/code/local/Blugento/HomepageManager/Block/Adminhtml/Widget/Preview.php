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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Block_Adminhtml_Widget_Preview
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Get relevant path to template depending on widget type
     * @return string
     */
    public function getTemplate()
    {
        $type = $this->getData('widget_type');

        $this->_template = 'blugento/homepagemanager/widget/preview/default.phtml';

        switch ($type) {
            case 'blugento_sliders/widget_slider':
                $this->_setBannerTemplate();
                break;
            case 'cms/widget_block':
                $this->_setCmsBlockTemplate();
                break;
            case 'cms/widget_page_link':
                $this->_setCmsPageTemplate();
                break;
            case 'multiproducts/catalog_product_widget_multiproducts':
            case 'blugento_multiproducts/catalog_product_widget_multiproducts':
                $this->_setMultiProductsTemplate();
                break;
            case 'catalog/category_widget_link':
                $this->_setCategoryTemplate();
                break;
            case 'catalog/product_widget_new':
                $this->_setNewProductsTemplate();
                break;
            case 'blugento_newproducts/product_widget_new':
                $this->_setNewProductsTemplate();
                break;
            case 'catalog/product_widget_link':
                $this->_setProductLinkTemplate();
                break;
            case 'sales/widget_guest_form':
                $this->_setOrdersTemplate();
                break;
            case 'reports/product_widget_compared':
                $this->_setComparedProductsTemplate();
                break;
            case 'reports/product_widget_viewed':
                $this->_setViewedProductsTemplate();
                break;
            case 'widgetnewsletter/newsletter':
                $this->_setNewsletterTemplate();
                break;
            case 'blog/last':
                $this->_setBlogLatestPosts();
                break;
            case 'blugento_productswidget/category':
                $this->_setProductswidgetCategoryTemplate();
                break;
            case 'blugento_productswidget/custom':
                $this->_setProductswidgetCustomTemplate();
                break;
            case 'blugento_productswidget/discounted':
                $this->_setProductswidgetDiscountedTemplate();
                break;
            case 'blugento_productswidget/new':
                $this->_setProductswidgetNewTemplate();
                break;
            case 'blugento_productswidget/last':
                $this->_setProductswidgetLastTemplate();
                break;
	        case 'blugento_productswidget/bestselling':
		        $this->_setProductswidgetBestsellingTemplate();
		        break;
	        case 'blugento_usp/widget_usp':
		        $this->_setUspsTemplate();
		        break;
            default:
                break;
        }

        return $this->_template;
    }

    /**
     * Set the category preview template
     * @return $this
     */
    protected function _setProductswidgetCategoryTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/productswidget_category.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[category]') {
                $title = $param['value'];
                try {
                    $id = intval(str_replace('category/', '', $title));
                    if ($id) {
                        $model = Mage::getModel('catalog/category')->load($id);
                        $title = $model->getName();
                    }
                } catch (Exception $e) {}

                break;
            }
        }

        $this->setData('title', 'Category: '.$title);

        return $this;
    }

    /**
     * Set the custom preview template
     * @return $this
     */
    protected function _setProductswidgetCustomTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/productswidget_custom.phtml';

        $params = $this->getData('widget_params');

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[products]') {
                try {
                    $products = explode(',', $param['value']);
                } catch (Exception $e) {}

                break;
            }
        }

        $this->setData('title', count($products) . ' Products');

        return $this;
    }

    /**
     * Set the discounted preview template
     * @return $this
     */
    protected function _setProductswidgetDiscountedTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/productswidget_discounted.phtml';

        return $this;
    }
    /**
     * Set the new preview template
     * @return $this
     */
    protected function _setProductswidgetNewTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/productswidget_new.phtml';

        return $this;
    }
    /**
     * Set the last preview template
     * @return $this
     */
    protected function _setProductswidgetLastTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/productswidget_last.phtml';

        return $this;
    }
	
	/**
	 * Set the last preview template
	 * @return $this
	 */
	protected function _setProductswidgetBestsellingTemplate()
	{
		$this->_template = 'blugento/homepagemanager/widget/preview/productswidget_bestselling.phtml';
		
		return $this;
	}

    /**
     * Set the banner preview template
     * @return $this
     */
    protected function _setBannerTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/banner.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[banner_code]') {
                $title = $param['value'];
                try {
                    $model = Mage::getModel('blugento_sliders/group')->load($param['value'], 'code');
                    if ($model) {
                        $title = $model->getTitle();
                    }
                } catch (Exception $e) {}
                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the CMS block preview template
     * @return $this
     */
    protected function _setCmsBlockTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/cms_block.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[block_id]') {
                $title = $param['value'];
                try {
                    $model = Mage::getModel('cms/block')->load($param['value']);
                    if ($model) {
                        $title = $model->getTitle();
                    }
                } catch (Exception $e) {}

                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the CMS page preview template
     * @return $this
     */
    protected function _setCmsPageTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/cms_page.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[page_id]') {
                $title = $param['value'];
                try {
                    $model = Mage::getModel('cms/page')->load($param['value']);
                    if ($model) {
                        $title = $model->getTitle();
                    }
                } catch (Exception $e) {}

                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the multi products preview template
     * @return $this
     */
    protected function _setMultiProductsTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/multi_products.phtml';

        $params = $this->getData('widget_params');
        $title  = array();

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[title]') {
                $title[] = $param['value'];
            } else
                if ($param['name'] == 'parameters[ids]') {
                    $arr = explode('{', $param['value']);
                    $cnt = 0;
                    foreach ($arr as $item) {
                        if (trim($item)) {
                            $cnt++;
                        }
                    }
                    $title[] = '(' . $cnt . ' ' . Mage::helper('blugento_homepagemanager')->__('Products') . ')';
                    break;
                }
        }

        $title = $title ? implode(' ', $title) : '';
        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the multi products preview template
     * @return $this
     */
    protected function _setCategoryTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/category.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[id_path]') {
                $title = $param['value'];
                try {
                    $id = intval(str_replace('category/', '', $title));
                    if ($id) {
                        $model = Mage::getModel('catalog/category')->load($id);
                        $title = $model->getName();
                    }
                } catch (Exception $e) {}

                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the multi products preview template
     * @return $this
     */
    protected function _setNewProductsTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/new_products.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[products_count]') {
                $title = $param['value'] . ' ' . Mage::helper('blugento_homepagemanager')->__('Products');
                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the product link preview template
     * @return $this
     */
    protected function _setProductLinkTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/product_link.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[id_path]') {
                $title = $param['value'];
                try {
                    $id = intval(str_replace('product/', '', $title));
                    if ($id) {
                        $model = Mage::getModel('catalog/product')->load($id);
                        $title = $model->getName();
                    }
                } catch (Exception $e) {}

                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the multi products preview template
     * @return $this
     */
    protected function _setOrdersTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/orders.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[title]') {
                $title = $param['value'];

                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the compared products preview template
     * @return $this
     */
    protected function _setComparedProductsTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/compared_products.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[page_size]') {
                $title = $param['value'] . ' ' . Mage::helper('blugento_homepagemanager')->__('Products');
                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the viewed products preview template
     * @return $this
     */
    protected function _setViewedProductsTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/viewed_products.phtml';

        $params = $this->getData('widget_params');
        $title  = '';

        foreach ($params as $param) {
            if ($param['name'] == 'parameters[page_size]') {
                $title = $param['value'] . ' ' . Mage::helper('blugento_homepagemanager')->__('Products');
                break;
            }
        }

        $this->setData('title', $title);

        return $this;
    }

    /**
     * Set the newsletter preview template
     * @return $this
     */
    protected function _setNewsletterTemplate()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/newsletter.phtml';

        $this->setData('title', '');

        return $this;
    }

    /**
     * Set the blog preview template
     * @return $this
     */
    protected function _setBlogLatestPosts()
    {
        $this->_template = 'blugento/homepagemanager/widget/preview/blog_posts.phtml';

        $this->setData('title', '');

        return $this;
    }
	
	/**
	 * Set usp preview template
	 * @return $this
	 */
	protected function _setUspsTemplate()
	{
		$this->_template = 'blugento/homepagemanager/widget/preview/usps.phtml';
		
		$this->setData('title', '');
		
		return $this;
	}
}
