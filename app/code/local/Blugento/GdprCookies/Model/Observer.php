<?php

class Blugento_GdprCookies_Model_Observer
{
    public function updateCookiesListBlock()
    {
        $helper = Mage::helper('gdprcookies');

        $necessaryCookies = $this->_getCookies(1);
        $analyticsCookies = $this->_getCookies(2);
        $marketingCookies = $this->_getCookies(3);

        $content = '<br>';

        if ($necessaryCookies) {
            $content .= '<table style="width:100%"><caption>' . $helper->__('Necessary') . '</caption><tr><th>' . $helper->__('Cookie Name') . '</th><th>' . $helper->__('Cookie Description') . '</th><th>' . $helper->__('Expiry') . '</th></tr>';
            foreach ($necessaryCookies as $cookie) {
                $content .= '<tr><td>' . $cookie['cookie_name'] . '</td><td>' . $cookie['cookie_description'] . '</td><td>' . $this->_secondsToTime(Mage::getStoreConfig('gdpr_cookies/lifetime_cookie/lifetime_cookie_input')) . '</td></tr>';
            }
            $content .= '</table>';
        }

        if ($analyticsCookies) {
            $content .= '<table style="width:100%"><caption>' . $helper->__('Analytics') . '</caption><tr><th>' . $helper->__('Cookie Name') . '</th><th>' . $helper->__('Cookie Description') . '</th><th>' . $helper->__('Expiry') . '</th></tr>';
            foreach ($analyticsCookies as $cookie) {
                $content .= '<tr><td>' . $cookie['cookie_name'] . '</td><td>' . $cookie['cookie_description'] . '</td><td>' . $this->_secondsToTime(Mage::getStoreConfig('gdpr_cookies/lifetime_cookie/lifetime_cookie_input')) . '</td></tr>';
            }
            $content .= '</table>';
        }

        if ($marketingCookies) {
            $content .= '<table style="width:100%"><caption>' . $helper->__('Marketing') . '</caption><tr><th>' . $helper->__('Cookie Name') . '</th><th>' . $helper->__('Cookie Description') . '</th><th>' . $helper->__('Expiry') . '</th></tr>';
            foreach ($marketingCookies as $cookie) {
                $content .= '<tr><td>' . $cookie['cookie_name'] . '</td><td>' . $cookie['cookie_description'] . '</td><td>' . $this->_secondsToTime(Mage::getStoreConfig('gdpr_cookies/lifetime_cookie/lifetime_cookie_input')) . '</td></tr>';
            }
            $content .= '</table>';
        }

        Mage::getModel('cms/block')->load('cookies-list')->setContent($content)->save();

        return $this;
    }

    public function updateCookieList($observer){

        $action = $observer->getEvent()->getControllerAction()->getFullActionName();
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();

        if ($action == 'adminhtml_cookies_save' || $action == 'adminhtml_cookies_delete' || $action == 'adminhtml_cookies_index'
            || strpos($currentUrl, 'politica-de-utilizare-cookie-uri') !== false) {
            $this->updateCookiesListBlock();
        }
    }

    private function _getCookies($category)
    {
        $sql = 'SELECT * FROM gdprcookies_list WHERE cookie_category = ' . $category;

        try {
            $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $result= $conn->fetchAll($sql);
            return $result;
        } catch (Exception $e) {
            Mage::throwException($e);
        }
    }

    private function _secondsToTime($seconds)
    {
        $ret = "";

        /*** get the days ***/
        $days = intval(intval($seconds) / (3600*24));
        if($days> 0)
        {
            $ret .= "$days zile ";
        }

        /*** get the hours ***/
        $hours = (intval($seconds) / 3600) % 24;
        if($hours > 0)
        {
            $ret .= "$hours ore ";
        }

        /*** get the minutes ***/
        $minutes = (intval($seconds) / 60) % 60;
        if($minutes > 0)
        {
            $ret .= "$minutes minute ";
        }

        /*** get the seconds ***/
        $seconds = intval($seconds) % 60;
        if ($seconds > 0) {
            $ret .= "$seconds secunde";
        }

        return $ret;
    }
}
