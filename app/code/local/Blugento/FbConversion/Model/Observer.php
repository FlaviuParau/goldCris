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
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Blugento_FbConversion_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * @var Blugento_FbConversion_Model_Event
     */
    protected $eventModel;

    /**
     * @var array
     */
    protected $pages = ['catalog_category_view', 'cms_index_index', 'cms_page_view', 'checkout_cart_index',
        'checkout_onepage_index', 'customer_account_login', 'customer_account_create', 'customer_account_forgotpassword',
        'checkout_onepage_success', 'catalogsearch_result_index', 'searchanise_result_index',
        'gdpruserdata_deletedata_index', 'gdpruserdata_exportdata_index'];

    /**
     * Blugento_FbConversion_Model_Observer constructor.
     */
    public function __construct()
    {
        $this->eventModel = Mage::getModel('blugento_fbconversion/event');
    }

    /**
     * Product page event
     */
    public function productPageViewEvent(Varien_Event_Observer $observer)
    {
        $storeId = Mage::app()->getStore()->getId();

        if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
            return;
        }

        // create ViewPage event
        $data = $this->createPageViewEvent($storeId);

        try {
            $product = $observer->getEvent()->getProduct();

            // create ViewContent event
            $data['name'] = 'ViewContent';

            $eventId = Mage::helper('blugento_fbconversion')->createEventId('ViewContent', $product->getId());
            Mage::getSingleton('core/session')->setFbEventViewcontent($eventId);
            $data['event_id'] = $eventId;

            $content = [
                [
                    'id' => $product->getId(),
                    'item_price' => number_format($product->getFinalPrice(), 2, '.', ''),
                    'quantity' => $product->getStockItem()->getQty(),
                ]
            ];

            $customData = [
                'content_type' => 'product',
                'content_ids' => [$product->getId()],
                'value' => number_format($product->getFinalPrice(), 2, '.', ''),
                'content_name' => $product->getName(),
                'currency' => Mage::app()->getStore()->getCurrentCurrencyCode()
            ];

            $data['content'] = serialize($content);
            $data['custom_data'] = serialize($customData);

            $this->eventModel->setId(null);
            $this->eventModel->setData($data);
            $this->eventModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Other pages event
     *
     * @param Varien_Event_Observer $observer
     */
    public function pagesViewEvent(Varien_Event_Observer $observer)
    {
        $storeId = Mage::app()->getStore()->getId();

        if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
            return;
        }

        $quote = null;

        $action = $observer->getEvent()->getControllerAction();
        $actionName = $action->getFullActionName();

        if (in_array($actionName, $this->pages)) {
            $this->createPageViewEvent($storeId);
        }

        if ($actionName == 'checkout_onepage_index') {
            if (Mage::getSingleton('checkout/session')->getQuote()) {
                $quote = Mage::getSingleton('checkout/session')->getQuote();
            }
            $this->createInitiateCheckoutEvent($quote);
        }

        if ($actionName == 'checkout_onepage_savePayment') {
            if (Mage::getSingleton('checkout/session')->getQuote()) {
                $quote = Mage::getSingleton('checkout/session')->getQuote();
            }
            $this->createAddPaymentEvent($quote);
        }

        if ($actionName == 'catalog_category_view') {
            $this->createViewCategoryEvent();
        }
    }

    /**
     * Add to cart event
     *
     * @param Varien_Event_Observer $observer
     */
    public function addToCart(Varien_Event_Observer $observer)
    {
        $storeId = Mage::app()->getStore()->getId();

        if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
            return;
        }

        try {
            $data = $this->createEventParams($storeId);

            $product = $observer->getEvent()->getProduct();

            $data['name'] = 'AddToCart';

            $eventId = Mage::helper('blugento_fbconversion')->createEventId('AddToCart', $product->getId());
            Mage::getSingleton('core/session')->setFbEventAddtocart($eventId);
            $data['event_id'] = $eventId;

            $content = [
                [
                    'id' => $product->getId(),
                    'item_price' => number_format($product->getFinalPrice(), 2, '.', ''),
                    'quantity' => $product->getStockItem()->getQty(),
                ]
            ];

            $customData = [
                'content_type' => 'product',
                'content_ids' => [$product->getId()],
                'value' => number_format($product->getFinalPrice(), 2, '.', ''),
                'content_name' => $product->getName(),
                'currency' => Mage::app()->getStore()->getCurrentCurrencyCode()
            ];

            $data['content'] = serialize($content);
            $data['custom_data'] = serialize($customData);

            $this->eventModel->setId(null);
            $this->eventModel->setData($data);
            $this->eventModel->save();

            if (Mage::helper('blugento_fbconversion')->getConfigurations('debug', $storeId)) {
                Mage::log($data, null, 'fb_conversion_debug.log');
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Add to cart event
     *
     * @param Varien_Event_Observer $observer
     */
    public function addToWishlist(Varien_Event_Observer $observer)
    {
        $storeId = Mage::app()->getStore()->getId();

        if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
            return;
        }

        try {
            $data = $this->createEventParams($storeId);

            $product = $observer->getEvent()->getProduct();

            $data['name'] = 'AddToWishlist';

            $eventId = Mage::helper('blugento_fbconversion')->createEventId('AddToWishlist', $product->getId());
            Mage::getSingleton('core/session')->setFbEventAddtowishlist($eventId);
            $data['event_id'] = $eventId;

            $content = [
                [
                    'id' => $product->getId(),
                    'item_price' => number_format($product->getFinalPrice(), 2, '.', ''),
                    'quantity' => $product->getStockItem()->getQty(),
                ]
            ];

            $customData = [
                'content_type' => 'product',
                'content_ids' => [$product->getId()],
                'value' => number_format($product->getFinalPrice(), 2, '.', ''),
                'content_name' => $product->getName(),
                'currency' => Mage::app()->getStore()->getCurrentCurrencyCode()
            ];

            $data['content'] = serialize($content);
            $data['custom_data'] = serialize($customData);

            $this->eventModel->setId(null);
            $this->eventModel->setData($data);
            $this->eventModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Place order event
     *
     * @param Varien_Event_Observer $observer
     */
    public function placeOrder(Varien_Event_Observer $observer)
    {
        $storeId = Mage::app()->getStore()->getId();

        if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $billingAddress = $order->getBillingAddress();

        try {
            $data = $this->createEventParams($storeId);

            $data['name'] = 'Purchase';
            $data['user_phone_hash'] = hash('sha256', $billingAddress->getTelephone()) ?: null;
            $data['user_email_hash'] = isset($data['user_email_hash']) && $data['user_email_hash']
                ? $data['user_email_hash']
                : hash('sha256', strtolower($order->getCustomerEmail()));

            $productIds = [];
            $content = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $productIds[] = $item->getProductId();

                $content[] = [
                    'id' => $item->getProductId(),
                    'item_price' => number_format($item->getPriceInclTax(), 2, '.', ''),
                    'quantity' => $item->getQtyOrdered(),
                    'delivery_category' => 'home_delivery'
                ];
            }

            $eventId = Mage::helper('blugento_fbconversion')->createEventId('Purchase', implode('.', $productIds));
            Mage::getSingleton('core/session')->setFbEventPurchase($eventId);
            $data['event_id'] = $eventId;

            if ($content) {
                $data['content'] = serialize($content);
            }

            $customData = [
                'content_ids' => $productIds,
                'value' => number_format($order->getGrandTotal(),2, '.', ''),
                'currency' => $order->getOrderCurrencyCode()
            ];

            $data['custom_data'] = serialize($customData);

            $this->eventModel->setData($data);
            $this->eventModel->save();

            if (Mage::helper('blugento_fbconversion')->getConfigurations('debug', $storeId)) {
                Mage::log($data, null, 'fb_conversion_debug.log');
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     *  Create/save page view event
     * @param int $storeId
     * @return array|mixed
     */
    private function createPageViewEvent($storeId)
    {
        $data = [];
        try {
            $data = $this->createEventParams($storeId);

            $eventId = Mage::helper('blugento_fbconversion')->createEventId('PageView');
            Mage::getSingleton('core/session')->setFbEventPageview($eventId);
            $data['event_id'] = $eventId;

            $this->eventModel->setData($data);
            $this->eventModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $data;
    }

    /**
     * Create/save view category event
     */
    private function createViewCategoryEvent()
    {
        try {
            $storeId = Mage::app()->getStore()->getId();

            if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
                return;
            }

            $category = Mage::getModel('catalog/category')->load(Mage::app()->getRequest()->getParam('id'));
            $data = $this->createEventParams($storeId);

            $data['name'] = 'ViewCategory';

            $eventId = Mage::helper('blugento_fbconversion')->createEventId('ViewCategory', $category->getName());
            Mage::getSingleton('core/session')->setFbEventViewcategory($eventId);
            $data['event_id'] = $eventId;

            $customData = [
                'content_category' => $category->getName()
            ];

            $data['custom_data'] = serialize($customData);

            $this->eventModel->setData($data);
            $this->eventModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Create/save initiate checkout event
     *
     * @param null|Mage_Sales_Model_Quote $quote $quote
     * @return array|mixed
     */
    private function createInitiateCheckoutEvent($quote)
    {
        $data = [];
        try {
            $storeId = Mage::app()->getStore()->getId();

            if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
                return;
            }

            $data = $this->createEventParams($storeId, $quote);

            $data['name'] = 'InitiateCheckout';

            $productIds = [];
            $content = [];
            foreach ($quote->getAllVisibleItems() as $item) {
                $productIds[] = $item->getProductId();

                $content[] = [
                    'id' => $item->getProductId(),
                    'item_price' => number_format($item->getPriceInclTax(),2, '.', ''),
                    'quantity' => $item->getQty(),
                ];
            }

            $eventId = Mage::helper('blugento_fbconversion')->createEventId('InitiateCheckout', implode('.', $productIds));
            Mage::getSingleton('core/session')->setFbEventInitiatecheckout($eventId);
            $data['event_id'] = $eventId;

            if ($content) {
                $data['content'] = serialize($content);
            }

            $customData = [
                'content_ids' => $productIds,
                'value' => number_format($quote->getGrandTotal(),2, '.', ''),
                'currency' => $quote->getQuoteCurrencyCode()
            ];

            $data['custom_data'] = serialize($customData);

            $this->eventModel->setData($data);
            $this->eventModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $data;
    }

    /**
     * Create/save add payment info event
     *
     * @param null|Mage_Sales_Model_Quote $quote $quote
     * @return array|mixed
     */
    private function createAddPaymentEvent($quote)
    {
        $data = [];
        try {
            $storeId = Mage::app()->getStore()->getId();

            if (!Mage::helper('blugento_fbconversion')->getConfigurations('enabled', $storeId)) {
                return;
            }

            $data = $this->createEventParams($storeId, $quote);

            $data['name'] = 'AddPaymentInfo';

            $this->eventModel->setData($data);
            $this->eventModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $data;
    }

    /**
     * Create base event parameters
     *
     * @param null|Mage_Sales_Model_Quote $quote
     * @param int|null $storeId
     * @return mixed
     */
    private function createEventParams($storeId = null, $quote = null)
    {
        $visitorData = Mage::getSingleton('core/session')->getVisitorData();

        // set data for PageView event
        $data['name'] = 'PageView';
        $data['time'] = time();
        $data['client_user_agent'] = $visitorData['http_user_agent'];
        $data['action_source'] = 'website';
        $data['source_url'] = Mage::getBaseUrl() . ltrim(Mage::app()->getRequest()->getOriginalRequest()->getPathInfo(), '/');
        $data['store_id'] = $storeId;

        if (isset($visitorData['session_id'])) {
            $data['user_external_id'] = $visitorData['session_id'] ? hash('sha256', $visitorData['session_id']) : null;
        }

        if ($fbp = $this->generateFbp()) {
            $data['fbp'] = $fbp;
        }

        if ($fbc = $this->generateFbc()) {
            $data['fbc'] = $fbc;
        }

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $data['user_email_hash'] = $customer->getEmail() ? hash('sha256', strtolower($customer->getEmail())) : null;
        }

        if ($quote) {
            $billingAddress = $quote->getBillingAddress();

            $email = null;
            if (isset($data['user_email_hash']) && $data['user_email_hash']) {
                $email = $data['user_email_hash'];
            } else if ($quote->getCustomerEmail()) {
                $email = hash('sha256', strtolower($quote->getCustomerEmail()));
            } else if ($billingAddress->getEmail()) {
                $email = hash('sha256', strtolower($billingAddress->getEmail()));
            }

            $data['user_email_hash'] = $email;
            $data['user_phone_hash'] = hash('sha256',$billingAddress->getTelephone()) ?: null;
        }

        return $data;
    }

    /**
     * Return _fbp
     *
     * @return string
     */
    private function generateFbp()
    {
        $fbp = Mage::getSingleton('core/cookie')->get('_fbp') ?: '';

        return $fbp;
    }

    /**
     * Generate and return _fbc
     *
     * @return string
     */
    private function generateFbc()
    {
        $fbc = '';

        if ($fbclid = Mage::getSingleton('core/cookie')->get('_fbc')) {
            $fbc = $fbclid;
        } else if ($fbclid = Mage::app()->getRequest()->getParam('fbclid')) {
            $fbc = 'fb.1.' . round(microtime(true) * 1000) . '.' . $fbclid;
        } else {
            // TODO get fbclid from session after is saved there
        }

        return $fbc;
    }
}
