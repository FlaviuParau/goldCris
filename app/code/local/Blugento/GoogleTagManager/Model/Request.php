<?php
/**
 *
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
 * @package     Blugento_GoogleTagManager
 * @author      St�ncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GoogleTagManager_Model_Request extends Mage_Core_Model_Abstract
{
	/**
	 * Google Tag Manager data
	 *
	 * @var Blugento_GoogleTagManager_Helper_Data _gtmHelper
	 */
	protected $_gtmHelper = null;
	
	/**
	 * DataLayer Quote
	 *
	 * @var null _quote
	 */
	protected $_quote = null;
	
	/**
	 * Datalayer Customer Variables
	 *
	 * @var array _customerVariables
	 */
	protected $_customerVariables = array();
	
	/**
	 * Datalayer Product Variables
	 *
	 * @var array _productVariables
	 */
	protected $_productVariables = array();
	
	/**
	 * Datalayer Category Variables
	 *
	 * @var array _categoryVariables
	 */
	protected $_categoryVariables = array();
	
	/**
	 * Datalayer Product Impression Variables
	 *
	 * @var array _productImpressionVariables
	 */
	protected $_productImpressionVariables = array();
	
	/**
	 * Datalayer Product Click Variables
	 *
	 * @var array _productClickVariables
	 */
	protected $_productClickVariables = array();
	
	/**
	 * Datalayer Add Quote Item Variables
	 *
	 * @var array _addQuoteItemVariables
	 */
	protected $_addQuoteItemVariables = array();
	
	/**
	 * Datalayer Remove Quote Item Variables
	 *
	 * @var array _removeQuoteItemVariables
	 */
	protected $_removeQuoteItemVariables = array();

	/**
	 * Datalayer Initiate Checkout Variables
	 *
	 * @var array _initiateCheckoutEvent
	 */
	protected $_initiateCheckoutEvent = array();
	
	/**
	 * Datalayer Success Page Variables
	 *
	 * @var array _successPageVariables
	 */
	protected $_successPageVariables = array();
	
	/**
	 * Datalayer Global Transaction Event
	 *
	 * @var array _globalTransactionEvent
	 */
	protected $_globalTransactionEvent = array();
	
	/**
	 * Datalayer Category Filter Variables
	 *
	 * @var array _categoryFilterVariables
	 */
	protected $_categoryFilterVariables = array();
	
	/**
	 * Datalayer Category Sort Variables
	 *
	 * @var array _categorySortVariables
	 */
	protected $_categorySortVariables = array();
	
	/**
	 * Customer session
	 *
	 * @var Mage_Customer_Model_Session _customerSession
	 */
	protected $_customerSession;

	/**
	 * Customer Log
	 *
	 * @var Mage_Log_Model_Customer _customerLog
	 */
	protected $_customerLog;
	
	/**
	 * DataLayer ActionName
	 *
	 * @var string _fullActionName
	 */
	protected $_fullActionName;

	/**
     * @var array
     */
	protected $productsDisplayModes = ['PRODUCTS', 'PRODUCTS_AND_PAGE', 'SUBCATEGORY_AND_PRODUCTS',
        'SUBCATEGORY_MIXED_ALL'];
	
	/**
	 * Blugento_GoogleTagManager_Model_Request constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_gtmHelper       = Mage::helper('blugento_googletagmanager');
		$this->_customerSession = Mage::getSingleton('customer/session');
		$this->_fullActionName  = Mage::app()->getFrontController()->getAction() ?
			Mage::app()->getFrontController()->getAction()->getFullActionName() : Mage::helper('blugento_googletagmanager')->__('Unknown');
		
		$this->_setCustomerDataLayer();
		$this->_setProductDataLayer();
		$this->_setProductImpressionDataLayer();
		$this->_setProductClickDataLayer();
		$this->_setCategoryDataLayer();
		$this->_setAddQuoteItemDataLayer();
		$this->_setRemoveQuoteItemDataLayer();
		$this->_setInitiateCheckoutDataLayer();
		$this->_setSuccessPageDataLayer();
		$this->_getGlobalTransactionDataLayer();
		$this->_setCategoryFilterDataLayer();
		$this->_setCategorySortDataLayer();
	}
	
	/**
	 * Return Data Layer Customer Variables
	 *
	 * @return array
	 */
	public function getCustomerVariables()
	{
		return $this->_customerVariables;
	}
	
	/**
	 * Add Customer Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addCustomerVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_customerVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Product Variables
	 *
	 * @return array
	 */
	public function getProductVariables()
	{
		return $this->_productVariables;
	}
	
	/**
	 * Add Product Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addProductVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_productVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Product Impression Variables
	 *
	 * @return array
	 */
	public function getProductImpressionVariables()
	{
		return $this->_productImpressionVariables;
	}
	
	/**
	 * Add Product Impression Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addProductImpressionVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_productImpressionVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Product Click Variables
	 *
	 * @return array
	 */
	public function getProductClickVariables()
	{
		return $this->_productClickVariables;
	}
	
	/**
	 * Add Product Click Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addProductClickVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_productClickVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Category Variables
	 *
	 * @return array
	 */
	public function getCategoryVariables()
	{
		return $this->_categoryVariables;
	}
	
	/**
	 * Add Category Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addCategoryVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_categoryVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Add Quote Item Variables
	 *
	 * @return array
	 */
	public function getAddQuoteItemVariables()
	{
		return $this->_addQuoteItemVariables;
	}
	
	/**
	 * Add Quote Item Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addAddQuoteItemVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_addQuoteItemVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Remove Quote Item Variables
	 *
	 * @return array
	 */
	public function getRemoveQuoteItemVariables()
	{
		return $this->_removeQuoteItemVariables;
	}
	
	/**
	 * Add Remove Quote Item Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addRemoveQuoteItemVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_removeQuoteItemVariables[$name] = $value;
		}
		
		return $this;
	}

	/**
	 * Return Data Layer Initiate Checkout Variables
	 *
	 * @return array
	 */
	public function getInitiateCheckoutVariables()
	{
		return $this->_initiateCheckoutEvent;
	}

	/**
	 * Add Initiate Checkout Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addInitiateCheckoutVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_initiateCheckoutEvent[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Success Page Variables
	 *
	 * @return array
	 */
	public function getSuccessPageVariables()
	{
		return $this->_successPageVariables;
	}
	
	/**
	 * Add Success Page Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addSuccessPageVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_successPageVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Global Transaction Event Information
	 *
	 * @return array
	 */
	public function getGlobalTransactionEvent()
	{
		return $this->_globalTransactionEvent;
	}
	
	/**
	 * Add Global Transaction Event information
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addGlobalTransactionEvent($name, $value)
	{
		if (!empty($name)) {
			$this->_globalTransactionEvent[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Category Filter Variables
	 *
	 * @return array
	 */
	public function getCategoryFilterVariables()
	{
		return $this->_categoryFilterVariables;
	}
	
	/**
	 * Add Category Filter Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addCategoryFilterVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_categoryFilterVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Return Data Layer Category Sort Variables
	 *
	 * @return array
	 */
	public function getCategorySortVariables()
	{
		return $this->_categorySortVariables;
	}
	
	/**
	 * Add Category Filter Variables
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Blugento_GoogleTagManager_Model_Request
	 */
	public function addCategorySortVariables($name, $value)
	{
		if (!empty($name)) {
			$this->_categorySortVariables[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Get current product information
	 *
	 * @param Mage_Catalog_Model_Product $_product
	 * @return array $product
	 */
	public function getProductInfo($_product)
	{
		$product = array();
		$product['name']               = $_product->getName();
		$product['brand']              = $this->getProductManufacturer($_product);
		$product['category']           = $this->getProductCategories($_product);
		$product['price']              = $_product->getSpecialPrice() ?: $_product->getPrice();
		$product['sku']                = $_product->getSku();
		$product['id']                 = $_product->getId();
		$product['priceFull']          = $_product->getPrice();
		$product['daysToShipping']     = $_product->getDeliveryTime() ?: '';
		$product['description']        = $_product->getDescription() ?: '';
		$product['discountValue']      = $this->_getDiscountValue($_product);
		$product['discountPercentage'] = $this->_getDiscountPercentage($_product);
		$product['shipping']           = $this->_getProductPackageName($_product);
		$product['stockValue']         = $this->_getProductStockValue($_product);
		$product['stockAge']           = $this->_getProductStockAge($_product);
		
		return $product;
	}
	
	/**
	 * Get Current Product Manufacturer Name
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	public function getProductManufacturer($product)
	{
		$brand = '';
		if ($product->getAttributeText('manufacturer') != '') {
			$brand = $product->getAttributeText('manufacturer');
		}
		
		return $brand;
	}
	
	/**
	 * Get Product Categories by Product
	 *
	 * @param $product
	 * @return string
	 */
	public function getProductCategories($product)
	{
		$categoriesIds = $product->getCategoryIds();
		$attributeId = $this->getAttributeId('name');

		if ($attributeId && count($categoriesIds) > 0) {
            try {
            $sql = "SELECT `value` AS name
                    FROM `catalog_category_entity_varchar`
                    WHERE `attribute_id` = $attributeId
                    AND `entity_id` IN (" . implode(',', $categoriesIds) . ")";

                $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);

                $names = [];
                foreach ($result as $item) {
                    $names[] = $item['name'];
                }

                return implode(',', array_unique($names));
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return '';
	}
	
	/**
	 * Get Current Product Package Name
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	private function _getProductPackageName($product)
	{
		$package = '';
		
		$attr = Mage::getResourceModel('catalog/eav_attribute')
			->loadByCode('catalog_product', 'package_id')->getId();
		
		if ($attr) {
			if ($product->getAttributeText('package_id') != '') {
				$package = $product->getAttributeText('package_id');
			}
		}
		
		return $package;
	}
	
	/**
	 * Set Customer Data Layer
	 */
	private function _setCustomerDataLayer()
	{
		if ($this->_customerSession->isLoggedIn() && $this->_gtmHelper->isEnabled()) {
			
			$orders = $this->_getCustomerOrderCollection($this->_getCustomerId());
			
			$ordersValue         = 0;
			$ordersCanceledCount = 0;
			$ordersCompleteCount = 0;
			$ordersCanceledValue = 0;
			$ordersCompleteValue = 0;
			
			foreach ($orders as $order) {
				if ($order['status'] == 'canceled') {
					$ordersCanceledCount++;
					$ordersCanceledValue += (float)$order['grand_total'];
				} elseif ($order['status'] == 'complete') {
					$ordersCompleteCount++;
					$ordersCompleteValue += (float)$order['grand_total'];
				}
				
				if ((float)$order['grand_total'] != 0) {
					$ordersValue += (float)$order['grand_total'];
				}
			}
			
			$lastPurchased = Mage::app()->getLocale()->date($orders['0']['created_at'], Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
			$lastPurchased = $this->currentTime() - $lastPurchased;
			$lastPurchased = $lastPurchased / (3600 * 24);
			
			$ascOrders      = array_reverse($orders);
			$firstPurchased = Mage::app()->getLocale()->date($ascOrders['0']['created_at'], Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
			$firstPurchased = $this->currentTime() - $firstPurchased;
			$firstPurchased = $firstPurchased / (3600 * 24);
			
			$customer = array();
			$customer['id']                   = $this->_getCustomerId();
			$customer['gender']               = $this->_getCustomerGender($this->_customerSession->getCustomer());
			$customer['orders']               = count($orders);
			$customer['ordersCanceled']       = $ordersCanceledCount;
			$customer['ordersShipped']        = $ordersCompleteCount;
			$customer['ordersValue']          = number_format($ordersValue, 2);
			$customer['ordersCanceledValue']  = number_format($ordersCanceledValue, 2);
			$customer['ordersShippedValue']   = number_format($ordersCompleteValue, 2);
			$customer['lastPurchase']         = (int) $lastPurchased;
			$customer['firstPurchase']        = (int) $firstPurchased;
			$customer['lastLogin']            = $this->_getLastLoginDate();
			$customer['firstLogin']           = $this->_getFirstLoginDate($this->_getCustomerId());
			$customer['email']                = md5($this->_customerSession->getCustomer()->getEmail());
			$customer['lastDelivery']         = $orders['0']['shipping_method'];
			$customer['lastPaymentType']      = ($this->_getCustomerOrderPayment($orders['0']['entity_id']) != false) ? $this->_getCustomerOrderPayment($orders['0']['entity_id']) : '';
			$customer['lastPurchaseProducts'] = $this->_getLastPurchasedProducts($this->_getCustomerId());
			
			$this->addCustomerVariables('event', 'customer');
			$this->addCustomerVariables('user', $customer);
		}
		
		return $this;
	}
	
	/**
	 * Get Customer Id
	 *
	 * @return mixed
	 */
	private function _getCustomerId()
	{
		return $this->_customerSession->getCustomer()->getId();
	}
	
	/**
	 * Get Customer Gender
	 *
	 * @param Mage_Customer_Model_Customer $customer
	 * @return string $gender
	 */
	private function _getCustomerGender($customer)
	{
		$gender = $customer->getData('gender');
		
		if (isset($gender) && $gender != '') {
			if ($gender == 1) {
				$gender = Mage::helper('blugento_googletagmanager')->__('Male');
			} else {
				$gender = Mage::helper('blugento_googletagmanager')->__('Female');
			}
		} else {
			$gender = '';
		}
		
		return $gender;
	}
	
	/**
	 * Return Order Collection by Customer Id
	 *
	 * @param int $customerId
	 * @return array $orderCollection
	 */
	private function _getCustomerOrderCollection($customerId)
	{
		$orderCollection = Mage::getResourceModel('sales/order_collection')
			->addFieldToSelect(array('status', 'grand_total', 'shipping_method', 'created_at'))
			->addFieldToFilter('customer_id', $customerId)
			->setOrder('created_at', 'DESC');
		
		return $orderCollection->getData();
	}
	
	/**
	 * Return Order Payment Method by Order Id
	 *
	 * @param int $orderId
	 * @return string $order
	 */
	private function _getCustomerOrderPayment($orderId)
	{
		$method = Mage::getSingleton('sales/order_payment')
			->load($orderId)
			->getData('method');
		
		return $method;
	}
	
	/**
	 * Load Customer Log model
	 *
	 * @return Mage_Log_Model_Customer
	 */
	private function _getCustomerLog()
	{
		if (!$this->_customerLog) {
			$this->_customerLog = Mage::getSingleton('log/customer')
				->loadByCustomer($this->_getCustomerId());
		}
		
		return $this->_customerLog;
	}
	
	/**
	 * Get Customer Last Login Date
	 *
	 * @return string
	 */
	private function _getLastLoginDate()
	{
		$date = $this->_getCustomerLog()->getLoginAt();
		
		if ($date) {
			$logInAt = Mage::helper('core')->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$lastLogIn = Mage::app()->getLocale()->date($logInAt, Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
			$lastLogIn = $this->currentTime() - $lastLogIn;
			$lastLogIn = $lastLogIn / 3600;
			
			return (int) $lastLogIn;
		}
		
		return Mage::helper('blugento_googletagmanager')->__('Never');
	}
	
	/**
	 * Get Customer First Login Date
	 *
	 * @param $customerId
	 * @return string
	 */
	private function _getFirstLoginDate($customerId)
	{
		$date = Mage::getSingleton('log/customer')
			->load($customerId)
			->getLoginAt();
		
		if ($date) {
			$logInAt    = Mage::helper('core')->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$firstLogIn = Mage::app()->getLocale()->date($logInAt, Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
			$firstLogIn = $this->currentTime() - $firstLogIn;
			$firstLogIn = $firstLogIn / (3600 * 24);
			
			return (int) $firstLogIn;
		}
		
		return Mage::helper('blugento_googletagmanager')->__('Never');
	}
	
	/**
	 * Return Order Item Collection by Customer
	 *
	 * @param int $customerId
	 *
	 * @return Mage_Sales_Model_Resource_Order_Collection
	 */
	private function _getOrderCollection($customerId)
	{
		$orderCollection = Mage::getSingleton('sales/order')
			->load($customerId)
			->getItemsCollection()
			->addFieldToSelect('product_id');
		
		return $orderCollection->getData();
	}
	
	/**
	 * Get Product Categories from Last Order
	 *
	 * @param $customerId
	 * @return string
	 */
	private function _getLastPurchasedProducts($customerId)
	{
		$productId = $this->_getOrderCollection($customerId)[0]['product_id'];
		$product   = $this->_loadProductById($productId);
		
		return $this->getProductCategories($product);
	}
	
	/**
	 * Set category Data Layer
	 */
	private function _setCategoryDataLayer()
	{
		if ($this->_fullActionName == 'catalog_category_view' && $this->_gtmHelper->isEnabled()) {
			$categoryId = Mage::app()->getRequest()->getParam('id');
			/** @var Mage_Catalog_Model_Category $_category */
			$_category  = Mage::getSingleton('catalog/category')->load($categoryId);
			
			$category = array();
			$category['pageType'] = $this->_fullActionName;
			$category['products'] = $_category->getProductCount();
			$category['name']     = $_category->getName();
			$category['minPrice'] = $this->_getPriceInfo($_category, 'ASC');
			$category['maxPrice'] = $this->_getPriceInfo($_category, 'DESC');
			
			$this->addCategoryVariables('listing', $category);
		}
		
		return $this;
	}
	
	/**
	 * Determine product final price based on price filter order from collection
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @param string $order
	 * @return float $price
	 */
	private function _getPriceInfo($category, $order)
	{
		$product = $category->getProductCollection()
			->setOrder('price', $order)
			->getFirstItem();
		
		$price = $product->getFinalPrice();
		
		return $price;
	}

	/**
     * Return attribute id by attribute code
     *
     * @param string $code
     * @return mixed
     */
    private function getAttributeId($code)
    {
        $sql = "SELECT `attribute_id`
                FROM `eav_attribute`
                WHERE `attribute_code` LIKE '$code'";

        try {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

            return $connection->fetchOne($sql);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return null;
    }
	
	/**
	 * Return product collection by Categoty Id
	 *
	 * @param int $categoryId
	 * @return Mage_Catalog_Model_Resource_Product_Collection $productCollection
	 */
	private function _getCategoryProductCollection($categoryId)
	{
		$category = Mage::getSingleton('catalog/category')->load($categoryId);

		if (in_array($category->getDisplayMode(), $this->productsDisplayModes)) {
			$productsModeDisplay = $this->getProductDisplayMode();
            $limit = Mage::getStoreConfig('catalog/frontend/' . $productsModeDisplay . '_per_page');

			$productCollection = $category->getProductCollection()
				->addAttributeToSelect(array('id', 'name', 'price', 'special_price'))
				->addAttributeToFilter('type_id', array('in' => array('simple', 'configurable', 'grouped', 'bundle', 'virtual', 'downloadable')))
				->addAttributeToFilter('status', 1)
				->addAttributeToFilter('visibility', 4)
                ->setPage(Mage::getBlockSingleton('page/html_pager')->getCurrentPage(), $limit)
                ->setPageSize($limit)
                ->setOrder(Mage::getBlockSingleton('catalog/product_list_toolbar')->getCurrentOrder(), Mage::getBlockSingleton('catalog/product_list_toolbar')->getCurrentDirection());

			return $productCollection;
		}
	}

	/**
     * Get product display mode
     *
     * @return string
     */
    private function getProductDisplayMode()
    {
        $configMode = Mage::getStoreConfig('catalog/frontend/list_mode');

        if (Mage::app()->getRequest()->getParam('mode') == 'grid'
            || $configMode == 'grid'
            || $configMode == 'grid-list'
        ) {
            $productDisplayMode = 'grid';
        } else {
            $productDisplayMode = 'list';
        }

        return $productDisplayMode;
    }
	
	/**
	 * Set product Data Layer
	 */
	private function _setProductDataLayer()
	{
		if ($this->_fullActionName == 'catalog_product_view' && $this->_gtmHelper->isEnabled()) {
			
			$productId = Mage::app()->getRequest()->getParam('id');
			/** @var Mage_Catalog_Model_Product $_product */
			$_product  = $this->_loadProductById($productId);

			$this->addProductVariables('event', 'productDetail');
			$this->addProductVariables('ecommerce', array(
				'detail' => array(
					'actionField' => array('list' => $this->_fullActionName),
					'products'    => array($this->getProductInfo($_product))
				),
			));
		}
		
		return $this;
	}

	/**
	 * Set product impression Data Layer
	 */
	private function _setProductImpressionDataLayer()
	{
		if ($this->_fullActionName == 'catalog_category_view' && $this->_gtmHelper->isEnabled()) {
			$categoryId = Mage::app()->getRequest()->getParam('id');
			$_products  = $this->_getCategoryProductCollection($categoryId);

			if ($_products) {
				$this->addProductImpressionVariables('event', 'productImpression');
				$this->addProductImpressionVariables('ecommerce', array(
					'currencyCode' => Mage::app()->getStore()->getCurrentCurrencyCode(),
					'impressions'  => $this->_getProductImpression($_products)
				));
			}
		}
		
		return $this;
	}
	
	/**
	 * Set product click Data Layer
	 */
	private function _setProductClickDataLayer()
	{
		$product = Mage::getSingleton('checkout/session')->getProductClick();
		if ($product != '' && $this->_gtmHelper->isEnabled()) {
			$this->addProductClickVariables('event', 'productClick');
			$this->addProductClickVariables('ecommerce', array(
				'click' => array(
					'actionField' => array('list' => $this->_fullActionName),
					'products'    => array($product)
				),
			));
		}
		
		return $this;
	}
	
	/**
	 * Determine product discount value
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	private function _getDiscountValue($product)
	{
		$_actualPrice        = $product->getPrice();
		$_specialPrice       = $product->getFinalPrice();
		$_priceDiscountValue = '';
		
		if ($_specialPrice != $_actualPrice) {
			$_priceDiscountValue = $_actualPrice - $_specialPrice;
		}
		
		return $_priceDiscountValue;
	}
	
	/**
	 * Determine product discount percentage
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return float|string
	 */
	private function _getDiscountPercentage($product)
	{
		$_actualPrice             = $product->getPrice();
		$_specialPrice            = $product->getFinalPrice();
		$_priceDiscountPercentage = '';
		
		if ($_specialPrice != $_actualPrice && $_actualPrice != 0) {
			$_priceDiscountPercentage = round(100 - ($_specialPrice / $_actualPrice) * 100);
		}
		
		return $_priceDiscountPercentage;
	}
	
	/**
	 * Get Product Stock Value by Product Type
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return int/null
	 */
	private function _getProductStockValue($product)
	{
		if (!$product->getStockItem()) {
			return null;
		}

		$stockValue = $product->getStockItem()->getQty();

		if ($product->getTypeId() == 'configurable') {
			$userProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);
			foreach ($userProducts as $_product) {
				$stockValue += $_product->getStockItem()->getQty();
			}
		}
		
		return number_format($stockValue, 2);
	}
	
	/**
	 * Get Product Stock Age by Product
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return int/null
	 */
	private function _getProductStockAge($product)
	{
		if (!$product->getStockItem()) {
			return null;
		}

		if ($product->getStockItem()->getIsInStock() == 0 && $product->getStockItem()->getQty() == 0) {
			$updatedAt = Mage::app()->getLocale()->date($product->getUpdatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
			$createdAt = Mage::app()->getLocale()->date($product->getCreatedAt(), Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
			$stockAge  = $updatedAt - $createdAt;
			$stockAge  = $stockAge / (3600 * 24);
			
			return (int) $stockAge;
		}
		
		return null;
	}
	
	/**
	 * Get Individual Product Info from Current Category
	 *
	 * @param Mage_Catalog_Model_Resource_Product_Collection _products
	 * @return array $products
	 */
	private function _getProductImpression($_products)
	{
		$products = array();
		
		foreach ($_products as $_product) {
			$product = array();
			$product['name']     = $_product->getName();
			$product['id']       = $_product->getId();
			$product['price']    = $_product->getSpecialPrice() ?: $_product->getPrice();
			$product['brand']    = $this->getProductManufacturer($_product);
			
			$products[] = $product;
		}
		
		return $products;
	}
	
	/**
	 * Set success page Data Layer
	 */
	private function _setSuccessPageDataLayer()
	{
		if ($this->_fullActionName == 'checkout_onepage_success') {
			$quote = $this->_getQuote();
			
			$transaction = array();
			$items       = array();
			
			$transaction['id']          = $quote->getRealOrderId();
			$transaction['affiliation'] = '';
			$transaction['revenue']     = $quote->getGrandTotal();
			$transaction['tax']         = $quote->getTaxAmount();
			$transaction['shipping']    = $quote->getShippingAmount();
			$transaction['coupon']      = $quote->getCouponCode();
			$transaction['payment']     = $quote->getPayment()->getMethod();
			$transaction['delivery']    = $quote->getShippingMethod();
			$transaction['type']        = ($quote->getBillingAddress()->getBlugentoPurchaseType() == 25) ?
				$this->_gtmHelper->__('Personal Purchase') : $this->_gtmHelper->__('Company Purchase');
			
			foreach ($quote->getAllVisibleItems() as $item) {
				/** @var Mage_Catalog_Model_Product $_product */
				$_product = Mage::getModel('catalog/product')->load($item->getProductId());
				
				$_item             = $this->getProductInfo($_product);
				$_item['quantity'] = $item->getQtyOrdered();
				$_item['coupon']   = '';
				
				$items[] = $_item;
			}
			
			$this->addSuccessPageVariables('event', 'purchase');
			$this->addSuccessPageVariables('ecommerce', array(
				'currencyCode' => Mage::app()->getStore()->getCurrentCurrencyCode(),
				'purchase'     => array(
					'actionField' => $transaction,
					'products'    => $items
				)
			));
		}
		
		return $this;
	}
	
	/**
	 * Set global transaction event information for Data Layer
	 */
	private function _getGlobalTransactionDataLayer()
	{
		$transaction = Mage::getSingleton('checkout/session')->getOrderTransactionData();
		$items       = Mage::getSingleton('checkout/session')->getOrderItemsData();
		
		if (is_array($transaction) && is_array($cart) && is_array($items)) {
			$this->addGlobalTransactionEvent('event', 'purchase');
			$this->addGlobalTransactionEvent('ecommerce', array(
				'currencyCode' => Mage::app()->getStore()->getCurrentCurrencyCode(),
				'purchase'     => array(
					'actionField' => $transaction,
					'products'    => array($product)
				)
			));
			
			unset($transaction);
			unset($items);
			Mage::getSingleton('checkout/session')->unsOrderTransactionData();
			Mage::getSingleton('checkout/session')->unsOrderItemsData();
		}
		
		return $this;
	}
	
	
	/**
	 * Get active quote
	 *
	 * @return object _quote
	 */
	private function _getQuote()
	{
		if (null == $this->_quote) {
			$_quoteId     = Mage::getSingleton('checkout/session')->getLastRealOrderId();
			$this->_quote = Mage::getModel('sales/order')->loadByIncrementId($_quoteId);
		}
		
		return $this->_quote;
	}
	
	/**
	 * Set add product to quote Data Layer
	 */
	private function _setAddQuoteItemDataLayer()
	{
		$product = Mage::getSingleton('checkout/session')->getProductQuoteAddItem();
		if (is_array($product) && count($product) > 0 && $this->_gtmHelper->isEnabled()) {
			$this->addAddQuoteItemVariables('event', 'addToCart');
			$this->addAddQuoteItemVariables('ecommerce', array(
				'currencyCode' => Mage::app()->getStore()->getCurrentCurrencyCode(),
				'add'          => array(
					'products' => array($product)
				)
			));
		}

		return $this;
	}
	
	/**
	 * Set remove product from quote Data Layer
	 */
	private function _setRemoveQuoteItemDataLayer()
	{
		$product = Mage::getSingleton('checkout/session')->getProductQuoteRemoveItem();
		if (is_array($product) && count($product) > 0 && $this->_gtmHelper->isEnabled()) {
			$this->addRemoveQuoteItemVariables('event', 'removeFromCart');
			$this->addRemoveQuoteItemVariables('ecommerce', array(
				'remove' => array(
					'products' => array($product)
				)
			));
		}
		
		return $this;
	}

	/**
	 * Set Initiate Checkout Data Layer
	 */
	private function _setInitiateCheckoutDataLayer()
	{
		if ($this->_fullActionName == 'checkout_onepage_index' && $this->_gtmHelper->isEnabled()) {
			$quote = Mage::getSingleton('checkout/session')->getQuote();
			$items = [];

            foreach ($quote->getAllVisibleItems() as $item) {
				$_item             = $this->getProductInfo($item);
				$_item['quantity'] = $item->getQty();
				$_item['coupon']   = '';

				$items[] = $_item;
			}

			$this->addInitiateCheckoutVariables('event', 'checkout');
			$this->addInitiateCheckoutVariables('ecommerce', array(
				'checkout' => array(
					'products' => $items
				)
			));
		}
		
		return $this;
	}
	
	/**
	 * Set category filter Data Layer
	 */
	private function _setCategoryFilterDataLayer()
	{
		if ($this->_fullActionName == 'catalog_category_view' && $this->_gtmHelper->isEnabled()) {
			$appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
			$_filters = array();

			if (count($appliedFilters) > 0) {
				foreach ($appliedFilters as $item) {
					$filterApplied = array();
					$filterApplied[$item->getName()] = $item->getLabel();

					$_filters[] = $filterApplied;
				}
				
				$this->addCategoryFilterVariables('event', 'filter');
				$this->addCategoryFilterVariables('filterApplied', $_filters);
			}
		}
		
		return $this;
	}
	
	/**
	 * Set category sort Data Layer
	 */
	private function _setCategorySortDataLayer()
	{
		if ($this->_fullActionName == 'catalog_category_view' && $this->_gtmHelper->isEnabled()) {
			$sort = array();
			$sortOrder     = Mage::app()->getLayout()->createBlock('catalog/category_view')->getLayout()->createBlock('blugento_sort/product_list_toolbar')->getCurrentOrder();
			$sortDirection = Mage::app()->getLayout()->createBlock('catalog/category_view')->getLayout()->createBlock('blugento_sort/product_list_toolbar')->getCurrentDirection();
			
			$sort[$sortOrder] = $sortDirection;
			
			$this->addCategorySortVariables('event', 'sorting');
			$this->addCategorySortVariables('sortingApplied', $sort);
		}
		
		return $this;
	}
	
	/**
	 * Load Product by Id
	 *
	 * @param $productId
	 * @return Mage_Core_Model_Abstract
	 */
	private function _loadProductById($productId)
	{
		return Mage::getSingleton('catalog/product')->load($productId);
	}
	
	/**
	 * Get Current Time in timestamp format
	 *
	 * @return int|string
	 */
	private function currentTime()
	{
		$currentDate = Mage::getSingleton('core/date')->date('Y-m-d H:i:s');
		
		return Mage::app()->getLocale()->date($currentDate, Varien_Date::DATETIME_INTERNAL_FORMAT)->getTimestamp();
	}
}
