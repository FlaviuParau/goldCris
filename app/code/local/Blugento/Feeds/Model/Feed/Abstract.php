<?php
/**
 * Blugento Feeds
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

abstract class Blugento_Feeds_Model_Feed_Abstract
    extends Mage_Core_Model_Abstract implements Blugento_Feeds_Model_Feed_Interface
{
    /**
     * Feed options
     * @var array
     */
    protected $_options = array();

    /**
     * Feed name
     * @var string
     */
    protected $_feedName = '';

    /**
     * Feed save model
     * @var string
     */
    protected $_feedSaveModel = '';

    /**
     * Header content type
     * @var string
     */
    protected $_contentType = 'text/plain';

    /**
     * Data feed separator
     * @var string
     */
    protected $_dataFeedSeparator = '|';

    /**
     * Refresh feed file
     * @var bool
     */
    public $refresh = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_setOptions();
    }

    /**
     * Set feed name
     * @return mixed
     */
    abstract public function setFeedName();

    /**
     * Set feed save model
     * @return mixed
     */
    abstract public function setFeedSaveModel();

    /**
     * Get products ready for caching or printing
     * @param array $args
     * @return array
     */
    abstract public function getProducts($args);

    /**
     * Get product line feed
     *
     * @param array $product
     * @param array $args
     * @return string
     */
    abstract public function getProductFeedLine($product, $args);

    /**
     * Get feed options
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Checks if a option is set and has a certain value
     *
     * @param string $optionName
     * @param mixed $optionValue
     * @param bool $forceInteger
     * @return bool
     */
    public function isOption($optionName, $optionValue, $forceInteger = false)
    {
        if ($forceInteger) {
            return isset($this->_options[$optionName]) && intval($this->_options[$optionName]) == $optionValue;
        }

        return isset($this->_options[$optionName]) && $this->_options[$optionName] == $optionValue;
    }

    /**
     * Gets a option value
     *
     * @param string $optionName
     * @param mixed $defaultValue
     * @param bool $forceInteger
     * @return mixed
     */
    public function getOption($optionName, $defaultValue = null, $forceInteger = false)
    {
        if ($forceInteger) {
            return isset($this->_options[$optionName]) ? intval($this->_options[$optionName]) : $defaultValue;
        }

        return isset($this->_options[$optionName]) ? $this->_options[$optionName] : $defaultValue;
    }

    public function isEnabled()
    {
        return $this->getOption('enabled', 0, true);
    }

    /**
     * Generate feed content and cache it
     *
     * @param string $content
     * @return Blugento_Feeds_Model_Feed_Save_Abstract|null
     * @throws Exception
     */
    public function save($content = null)
    {
        try {
            $model = Mage::getModel($this->getFeedSaveModel());
            if ($content !== null) {
                return $model->save($content);
            }
            return $model->save($this->getProducts(array()));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get feed from cached file
     *
     * @return null|string
     * @throws Exception
     */
    public function getFromCache()
    {
        try {
            $model = Mage::getModel($this->getFeedSaveModel());
            return $model->getContent();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get feed from database
     *
     * @return string
     * @throws Exception
     */
    public function getFromDb()
    {

        try {
            if ($this->_contentType == 'text/csv') {
                return $this->getProducts(array());
            }
            return implode("\n", $this->getProducts(array()));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get feed from cache or from db
     *
     * @throws Exception
     */
    public function get()
    {
        try {
            if (!$this->refresh) {
                $content = $this->getFromCache();
                if (!$content) {
                    $content = $this->getFromDb();
                    if ($content) {
                        $this->save($content);
                    }
                }
            } else {
                $content = $this->getFromDb();
                if ($content) {
                    $this->save($content);
                }
                $content = $this->getFromCache();
            }
            return $content;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function output()
    {
        header('Content-type: ' . $this->_contentType);
        echo $this->get();
        exit();
    }

    /**
     * Get feed save model name
     * @return string
     */
    public function getFeedSaveModel()
    {
        if (!$this->_feedSaveModel) {
            $this->setFeedSaveModel();
        }
        return $this->_feedSaveModel;
    }

    /**
     * Get product URL
     *
     * @param string $productUrl
     * @return string
     */
    public function getProductUrl($productUrl)
    {
        $currentFileName = basename($_SERVER['REQUEST_URI']);
        $productUrl = str_replace($currentFileName, 'index.php', $productUrl);
        $productUrl = str_replace($_SERVER['PHP_SELF'], '/index.php', $productUrl);

        // Eliminate id session
        $posSID = strpos($productUrl, '?SID');

        if ($posSID) {
            $productUrl = substr($productUrl, 0, $posSID);
        }

        return $productUrl;
    }

    /**
     * Helper function
     *
     * @param string $findStr
     * @param string $replaceStr
     * @param string $string
     * @return string
     */
    public function replaceNotInTags($findStr, $replaceStr, $string)
    {
        $find = array($findStr);
        $replace = array($replaceStr);
        preg_match_all('#[^>]+(?=<)|[^>]+$#', $string, $matches, PREG_SET_ORDER);
        foreach ($matches as $val) {
            if (trim($val[0]) != '') {
                $string = str_replace($val[0], str_replace($find, $replace, $val[0]), $string);
            }
        }
        return $string;
    }

    /**
     * @param $refresh
     * @return $this
     */
    public function setRefresh($refresh)
    {
        $this->refresh = $refresh;
        return $this;
    }

    /**
     * Set feed options form system config
     */
    protected function _setOptions()
    {
        if (!$this->_feedName) {
            $this->setFeedName();
        }
        $this->_options = Mage::getStoreConfig('blugento_feeds/' . $this->_feedName);
    }

    /**
     * Set script execution default limits
     */
    protected function _setExecutionLimits()
    {
        // Set no time limit only if php is not running in Safe Mode
        if (!ini_get('safe_mode')) {
            @set_time_limit(0);
            if (((int)substr(ini_get('memory_limit'), 0, -1)) < 1024) {
                ini_set('memory_limit', '1024M');
            }
        }

        ignore_user_abort();
        error_reporting(E_ALL^E_NOTICE);
    }

    /**
     * Return price by special price from and to.
     *
     * @param string $price
     * @param string $special
     * @param string $from
     * @param string $to
     * @return string
     */
    protected function _establishPrice($price, $special, $from, $to)
    {
        if ($special && ($special < $price)) {
            $to = str_replace('00:00:00', '23:59:59', $to);
            $now = now();

            if ($from && $to) {
                if ($from < $now && $to > $now) {
                    $price = $special;
                }
            } else if ($from && !$to) {
                if ($from < $now) {
                    $price = $special;
                }
            } else if (!$from && $to) {
                if ($to > $now) {
                    $price = $special;
                }
            } else if (!$from && !$to) {
                $price = $special;
            }
        }

        return $price;
    }

}
