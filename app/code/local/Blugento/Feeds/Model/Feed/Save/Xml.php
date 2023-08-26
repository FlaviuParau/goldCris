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

abstract class Blugento_Feeds_Model_Feed_Save_Xml extends Blugento_Feeds_Model_Feed_Save_Abstract
{
    /**
     * @var DOMDocument
     */
    protected $_XMLDocument = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_XMLDocument = new DOMDocument('1.0', 'UTF-8');
    }

    /**
     * Get string content
     * @param array $content
     * @return string
     */
    abstract public function buildXMLString(array $content);

    /**
     * Save the data in file
     * @param string|array $content
     * @throws Exception
     * @return Blugento_Feeds_Model_Feed_Save_Abstract|null
     */
    public function save($content)
    {
        if (!$this->_definitionFilePath) {
            return null;
        }

        if (is_array($content)) {
            $content = $this->buildXMLString($content);
        } else
        if (!is_string($content)) {
            throw new Exception('Cannot save feed file: invalid content!');
        }

        if (!$content) {
            return null;
        }

        $fh = fopen($this->_definitionFilePath, 'w');
        if ($fh === false) {
            return null;
        }

        fwrite($fh, $content);
        fclose($fh);
        
        return $this;
    }

    /**
     * Get file content
     * @return null|string
     */
    public function getContent()
    {
        if (!$this->_definitionFilePath) {
            return null;
        }

        $fd = fopen($this->_definitionFilePath, 'r');
        if ($fd === false) {
            return null;
        }
        $size = filesize($this->_definitionFilePath);
        $content = $size > 0 ? fread($fd, $size) : '';
        fclose($fd);

        return $content;

    }

    /**
     * Create node element
     *
     * @param $name
     * @param mixed $value
     * @return DOMElement|DOMNode
     */
    public function createElement($name, $value = null)
    {
        $element = $this->_XMLDocument->createElement($name);
        $element = $this->_XMLDocument->importNode($element);
        if ($value !== null) {
            $element->appendChild(new DOMText($value));
        }
        return $element;
    }

    /**
     * Create xml attribute
     *
     * @param $name
     * @param mixed $value
     * @return DOMElement|DOMNode
     */
    public function createAttribute($name, $value = null) {
        $attribute = $this->_XMLDocument->createAttribute($name);

        if ($value) {
            $attribute->value = $value;
        }

        return $attribute;
    }

    /**
     * Clear string
     * @param string $string
     * @return string
     */
    public function slugify($string)
    {
        // Lower case everything
        $string = strtolower($string);
        // Make alphanumeric (removes all other characters)
        $string = preg_replace('/[^a-z0-9_\s-]/', '', $string);
        // Clean up multiple dashes or whitespaces
        $string = preg_replace('/[\s-]+/', ' ', $string);
        // Convert whitespaces and underscore to dash
        $string = preg_replace('/[\s_]/', '-', $string);

        return $string;
    }
}
