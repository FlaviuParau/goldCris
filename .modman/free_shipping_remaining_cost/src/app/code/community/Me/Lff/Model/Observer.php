<?php
/**
 * Class Me_Lff_Model_Observer
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Lff_Model_Observer
 */
class Me_Lff_Model_Observer
{
    /**
     * Event before show cart on frontend
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|bool
     */
    public function beforeCartDisplay(Varien_Event_Observer $observer)
    {
        if (!$this->_getLffHelper()->isFreeShippingNotificationAvailable()) {
            return false;
        }

        try {
            $controller = $observer->getAction();

            if ($controller->getFullActionName() == 'checkout_cart_index') {

                $layout = $controller->getLayout();
                $block = $layout->getBlock('checkout.cart.form.before');
                if ($block) {

                    $infoBlock = $layout->createBlock('me_lff/checkout_cart_leftamountinfo')
                        ->setName('me.lff.amount.info')
                        ->setTemplate('me/lff/checkout/cart/left-amount-info.phtml');

                    $block->append($infoBlock);
                }

            }
        } catch (Mage_Core_Exception $e) {
            Mage::log($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Event show free shipping notification block in the sidebar
     *
     * @param Varien_Event_Observer $observer observer
     * @return $this|bool
     */
    public function addToSidebar(Varien_Event_Observer $observer)
    {
        $_helper = $this->_getLffHelper();
        if (!$_helper->isFreeShippingNotificationAvailable() || !$_helper->showInSidebar() || $_helper->isEnterprise()) {
            return false;
        }

        try {

            $controller = $observer->getAction();
            $layout = $controller->getLayout();

            $sidebarBlock = $layout->getBlock($_helper->getPositionInSidebar());

            if ($sidebarBlock) {

                $infoBlock = $layout->createBlock('me_lff/checkout_cart_leftamountinfo')
                    ->setName('me.lff.sidebar.info')
                    ->setTemplate('me/lff/sidebar/sidebar-info.phtml');

                $sidebarBlock->append($infoBlock);
            }

        } catch (Mage_Core_Exception $e) {
            Mage::log($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Get extension helper
     *
     * @return Me_Lff_Helper_Data
     */
    protected function _getLffHelper()
    {
        return Mage::helper('me_lff');
    }
}
