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
 * @package     Blugento_RichSnippets
 * @author      Stîncel-Toader Octavian-Cristian <tavi@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_Richsnippets_Block_Organization_Jsonld extends Blugento_Richsnippets_Block_Abstract
{
	/**
	 * Get Data for Organization schema.org
	 *
	 * @return Blugento_Richsnippets_Block_Abstract _richSnippets
	 */
	public function getStructuredData()
	{
		if ($this->_richSnippetHelper->isOrgRichSnippetEnabled()) {
			$this->_organizationData();
		}
		
		return $this->_richSnippets;
	}
	
	/**
	 * Save in _richSnippets data for Organization
	 */
	protected function _organizationData()
	{
		$this->_richSnippets = $this->_organizationGlobalInfo();
		
		$this->_richSnippets['founders']        = $this->_getFoundersData();
		$this->_richSnippets['address']         = $this->_getAddress();
		$this->_richSnippets['contactPoint']    = $this->_getContact();
		$this->_richSnippets['sameAs']          = $this->_getSocialMedia();
	}
	
	/**
	 * Return name, legal name, url, logo, founding date for organization data
	 *
	 * @return array
	 */
	protected function _organizationGlobalInfo()
	{
		$data = array(
			'@context'     => 'http://schema.org',
			'@type'        => 'Organization',
			'name'         => $this->_richSnippetHelper->organizationInfo('name') ?: ($this->_richSnippetHelper->getImprintInfo('shop_name') ?: ''),
			'legalName'    => $this->_richSnippetHelper->organizationInfo('legal_name') ?: ($this->_richSnippetHelper->getImprintInfo('company_first') ?: ($this->_richSnippetHelper->getImprintInfo('company_second') ?: '')),
			'url'          => $this->_richSnippetHelper->organizationInfo('url') ?: Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true),
			'logo'         => $this->_getLogo() ?: $this->getSkinUrl('images/' . $this->helper('blugento_theme')->getVariable('page_brand_logo', 'image'), array('_secure' => true)),
			'foundingDate' => $this->_richSnippetHelper->organizationInfo('founding_date') ?: ''
		);
		
		return $data;
	}
	
	/**
	 * Update _richSnippets with Founders Data
	 *
	 * @return array
	 */
	protected function _getFoundersData()
	{
		$foundersName = explode(',', $this->_richSnippetHelper->organizationInfo('founders'));
		$founderData  = array();
		
		if (is_array($foundersName)) {
			foreach ($foundersName as $founder) {
				if (trim($founder) != '') {
					$founderData[] = array(
						'@type' => 'Person',
						'name'  => $founder
					);
				}
			}
		} else {
			if ($founderName = $this->_richSnippetHelper->getImprintInfo('shop_name') != '') {
				$founderData = array(
					'@type' => 'Person',
					'name'  => $founderName
				);
			}
		}
		
		return $founderData;
	}
	
	/**
	 * Update _richSnippets with Address Data
	 *
	 * @return array
	 */
	protected function _getAddress()
	{
		$address       = $this->_richSnippetHelper->organizationInfo('map_address');
		$addressValues = ($address != '') ? $this->_richSnippetHelper->getCoreHelper('unserializeArray')->unserialize($address) : '';
		$addressData   = array();
		
		if (is_array($addressData) && count($addressData) > 0) {
			foreach ($addressValues as $value) {
				$addressData[] = array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => isset($value['street']) ? $value['street'] : $this->_richSnippetHelper->getImprintInfo('street'),
					'addressLocality' => isset($value['city']) ? $value['city'] : $this->_richSnippetHelper->getImprintInfo('city'),
					'addressRegion'   => isset($value['region']) ? $value['region'] : null,
					'postalCode'      => isset($value['postal_code']) ? $value['postal_code'] : $this->_richSnippetHelper->getImprintInfo('zip'),
					'addressCountry'  => isset($value['country']) ? $value['country'] : $this->_richSnippetHelper->getImprintInfo('country')
				);
			}
		}
		
		return $addressData;
	}
	
	/**
	 * Update _richSnippets with Contact Data
	 *
	 * @return array
	 */
	protected function _getContact()
	{
		$contact       = $this->_richSnippetHelper->organizationInfo('map_contact_point');
		$contactValues = ($contact != '') ? $this->_richSnippetHelper->getCoreHelper('unserializeArray')->unserialize($contact) : '';
		$contactData   = array();
		
		if (is_array($contactValues) && count($contactValues) > 0) {
			foreach ($contactValues as $value) {
				$contactData[] = array(
					'@type'       => 'ContactPoint',
					'contactType' => isset($value['type']) ? $value['type'] : null,
					'telephone'   => isset($value['telephone']) ? $value['telephone'] : $this->_richSnippetHelper->getImprintInfo('telephone'),
					'email'       => isset($value['email']) ? $value['email'] : $this->_richSnippetHelper->getImprintInfo('email')
				);
			}
		}
		
		return $contactData;
	}
	
	/**
	 * Update _richSnippets with Social Media Data
	 *
	 * @return array
	 */
	protected function _getSocialMedia()
	{
		return array(
			$this->_richSnippetHelper->organizationInfo('facebook') ?: $this->_richSnippetHelper->getSocialMediaUrl('facebook'),
			$this->_richSnippetHelper->organizationInfo('instagram') ?: $this->_richSnippetHelper->getSocialMediaUrl('instagram'),
			$this->_richSnippetHelper->organizationInfo('twitter') ?: $this->_richSnippetHelper->getSocialMediaUrl('twitter'),
			$this->_richSnippetHelper->organizationInfo('linkedin') ?: $this->_richSnippetHelper->getSocialMediaUrl('linkedin'),
			$this->_richSnippetHelper->organizationInfo('youtube') ?: $this->_richSnippetHelper->getSocialMediaUrl('youtube'),
			$this->_richSnippetHelper->organizationInfo('pinterest') ?: $this->_richSnippetHelper->getSocialMediaUrl('pinterest'),
		);
	}
	
	/**
	 * Retrieve Path to Logo
	 *
	 * @return string
	 */
	protected function _getLogo()
	{
		$folderName   = Blugento_Richsnippets_Model_System_Config_Backend_Logo::UPLOAD_DIR;
		$storeConfig  = $this->_richSnippetHelper->organizationInfo('logo');
		$logoFile     = Mage::getBaseUrl('media') . $folderName . DS . $storeConfig;
		$absolutePath = Mage::getBaseDir('media') . DS . $folderName . DS . $storeConfig;
		
		if (!is_null($storeConfig) && $this->_isFile($absolutePath)) {
			$url = $logoFile;
		} else {
			$url = $this->getSkinUrl('favicon.ico', array('_secure' => true));
		}
		
		return $url;
	}
	
	/**
	 * If DB File Storage is Enabled - find there, otherwise - just file_exists
	 *
	 * @param string $filename
	 * @return bool
	 */
	protected function _isFile($filename)
	{
		if ($this->_richSnippetHelper->getCoreHelper('file_storage_database')->checkDbUsage() && !is_file($filename)) {
			$this->_richSnippetHelper->getCoreHelper('file_storage_database')->saveFileToFilesystem($filename);
		}
		
		return is_file($filename);
	}
}
