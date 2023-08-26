<?php
/**
 * @author Alan Barber <alan@cadence-labs.com>
 */
Class Cadence_Fbpixel_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('cadence_fbpixel');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setViewCategory($data)
    {
        $this->setData('view_category', $data);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getViewCategory()
    {
        if ($this->hasViewCategory()) {
            $data = $this->getData('view_category');
            $this->unsetData('view_category');
            return $data;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasViewCategory()
    {
        return $this->hasData('view_category');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setAddToCart($data)
    {
        $this->setData('add_to_cart', $data);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getAddToCart()
    {
        if ($this->hasAddToCart()) {
            $data = $this->getData('add_to_cart');
            $this->unsetData('add_to_cart');
            return $data;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasAddToCart()
    {
        return $this->hasData('add_to_cart');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setAddToWishlist($data)
    {
        $this->setData('add_to_wishlist', $data);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getAddToWishlist()
    {
        if ($this->hasAddToWishlist()) {
            $data = $this->getData('add_to_wishlist');
            $this->unsetData('add_to_wishlist');
            return $data;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function hasAddToWishlist()
    {
        return $this->hasData('add_to_wishlist');
    }

    /**
     * @return bool
     */
    public function hasInitiateCheckout()
    {
        return $this->hasData('initiate_checkout');
    }

    /**
     * @return mixed|null
     */
    public function getInitiateCheckout()
    {
        if ($this->hasInitiateCheckout()) {
            $data = $this->getData('initiate_checkout');
            $this->unsetData('initiate_checkout');
            return $data;
        }
        return null;
    }

    /**
     * @return $this
     */
    public function setInitiateCheckout($data)
    {
        $this->setData('initiate_checkout', $data);
        return $this;
    }

    /**
     * @return bool
     */
    public function hasViewProduct()
    {
        return $this->hasData('view_product');
    }

    /**
     * @return mixed|null
     */
    public function getViewProduct()
    {
        if ($this->hasViewProduct()) {
            $data = $this->getData('view_product');
            $this->unsetData('view_product');
            return $data;
        }
        return null;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setViewProduct($data)
    {
        $this->setData('view_product', $data);
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSearch()
    {
        return $this->hasData('search');
    }

    /**
     * @return mixed|null
     */
    public function getSearch()
    {
        if ($this->hasSearch()) {
            $data = $this->getData('search');
            $this->unsetData('search');
            return $data;
        }
        return null;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setSearch($value)
    {
        $this->setData('search', $value);
        return $this;
    }

    /**
     * @return bool
     */
    public function hasPlaceOrder()
    {
        return $this->hasData('place_order');
    }

    /**
     * @return mixed|null
     */
    public function getPlaceOrder()
    {
        if ($this->hasPlaceOrder()) {
            $data = $this->getData('place_order');
            $this->unsetData('place_order');
            return $data;
        }
        return null;
    }

    /**
     * @return $this
     */
    public function setPlaceOrder($data)
    {
        $this->setData('place_order', $data);
        return $this;
    }
}