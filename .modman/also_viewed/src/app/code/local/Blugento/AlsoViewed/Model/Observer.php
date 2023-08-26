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

session_start();
class Blugento_AlsoViewed_Model_Observer
{
    public function catalogControllerProductView(Varien_Event_Observer $observer)
    {
        $getSession = $_SESSION['AlsoViewedUserCode'];
        if (!isset($getSession)) {
            $alsoViewedUserCode = $this->generateUserCode();
            $_SESSION['AlsoViewedUserCode'] = $alsoViewedUserCode;
            $getSession = $alsoViewedUserCode;
        }
        $product = $observer->getEvent()->getProduct();

        $productId = $product->getId();
        $cur_category_id = implode(",", $product->getCategoryIds());

        $model = Mage::getModel('blugento_alsoviewed/alsoviewed');

        if (!isset($_SESSION[$getSession])) {
            $user_product_view_array = array();
            array_push($user_product_view_array, $productId);
            $_SESSION[$getSession] = $user_product_view_array;
            $model->setSessionCod($getSession); //session_cod field
            $model->setProductId($productId); //product_id
            $model->setProductSku($product->getSku()); //product_sku
            $model->setProductCategories($cur_category_id);
            $model->setIp($_SERVER['REMOTE_ADDR']);
            $model->save();
        } else {
            if (!in_array($productId, $_SESSION[$getSession])) {
                array_push($_SESSION[$getSession], $productId);
                $model->setSessionCod($getSession); //session_cod field
                $model->setProductId($productId); //product_id
                $model->setProductSku($product->getSku()); //product_sku
                $model->setProductCategories($cur_category_id);
                $model->setIp($_SERVER['REMOTE_ADDR']);
                $model->save();
            }
        }
    }

    public function generateUserCode($length = 5)
    {
        $characters = '01234abcdeABCDE';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString . time();
    }
}
