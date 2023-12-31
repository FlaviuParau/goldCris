<?php

class Blugento_SeoEnhancements_Model_System_Config_Backend_Favicon extends Mage_Adminhtml_Model_System_Config_Backend_Image
{
	/**
	 * The tail part of directory path for uploading
	 */
	const UPLOAD_DIR                = 'seo_enhancements/favicon';
	
	/**
	 * Token for the root part of directory path for uploading
	 */
	const UPLOAD_ROOT_TOKEN         = 'system/filesystem/media';
	
	/**
	 * Upload max file size in kilobytes
	 *
	 * @var int
	 */
	protected $_maxFileSize         = 2048;
	
	/**
	 * Return path to directory for upload file
	 *
	 * @return string
	 */
	protected function _getUploadDir()
	{
		$uploadDir  = $this->_appendScopeInfo(self::UPLOAD_DIR);
		$uploadRoot = $this->_getUploadRoot(self::UPLOAD_ROOT_TOKEN);
		$uploadDir  = $uploadRoot . DS . $uploadDir;
		return $uploadDir;
	}
	
	/**
	 * Makes a decision about whether to add info about the scope
	 *
	 * @return boolean
	 */
	protected function _addWhetherScopeInfo()
	{
		return true;
	}
}
