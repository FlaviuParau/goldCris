<?php

class Blugento_Socialmedia_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getFacebookProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_facebook/facebookproductpage');
    }

    public function getTwitterProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_twitter/twitterproductpage');
    }

    public function getLinkedinProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_linkedin/linkedinproductpage');
    }

    public function getYoutubeProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_youtube/youtubeproductpage');
    }

    public function getGoogleplusProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_googleplus/googleplusproductpage');
    }

    public function getPinterestProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_pinterest/pinterestproductpage');
    }

    public function getInstagramProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_instagram/instagramproductpage');
    }

    public function getFlickrProductPage()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_flickr/flickrproductpage');
    }

    // public function getTikTokProductPage()
    // {
    //     return Mage::getStoreConfig('blugento_socialmedia/share_media_tiktok/tiktokproductpage');
    // }

    public function getModuleStatus()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_settings/enabled');
    }

    public function getFacebookUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_facebook/facebookurl');
    }

    public function getFacebookTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_facebook/facebooktitle');
    }

    public function getLinkedinUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_linkedin/linkedinurl');
    }

    public function getLinkedinTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_linkedin/linkedintitle');
    }

    public function getTwitterUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_twitter/twitterurl');
    }

    public function getTwitterTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_twitter/twittertitle');
    }

    public function getYoutubeUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_youtube/youtubeurl');
    }

    public function getYoutubeTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_youtube/youtubetitle');
    }

    public function getGoogleUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_googleplus/googleplusurl');
    }

    public function getGoogleTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_googleplus/googleplustitle');
    }

    public function getPinterestUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_pinterest/pinteresturl');
    }

    public function getPinterestTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_pinterest/pinteresttitle');
    }

    public function getInstagramUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_instagram/instagramurl');
    }

    public function getInstagramTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_instagram/instagramtitle');
    }

    public function getFlickrUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_flickr/flickrurl');
    }

    public function getFlickrTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_flickr/flickrtitle');
    }

    public function getTikTokUrl()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_tiktok/tiktokurl');
    }

    public function getTikTokTitle()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_tiktok/tiktoktitle');
    }
	
	/**
	 * @return string|null
	 */
	public function getAddThis()
    {
    	if ($this->getFacebookProductPage() ||
		    $this->getTwitterProductPage() ||
		    $this->getLinkedinProductPage() ||
		    $this->getPinterestProductPage()
	    ) {
    		return '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51e917e36d1ebe71" async="async"></script>';
	    } else {
    		return null;
	    }
    }

    public function isWhatsappShareEnabled()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_whatsapp/enabled');
    }

    public function getWhatsappSharePhoneNumber()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_whatsapp/phone_number');
    }

    public function getWhatsappShareText()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_whatsapp/custom_text');
    }

    public function getWhatsappShareBtnText()
    {
        return Mage::getStoreConfig('blugento_socialmedia/share_media_whatsapp/btn_text');
    }
}
