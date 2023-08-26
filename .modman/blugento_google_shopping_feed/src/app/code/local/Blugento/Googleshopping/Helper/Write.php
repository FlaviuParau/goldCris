<?php

class Blugento_Googleshopping_Helper_Write extends Mage_Core_Helper_Abstract
{

    /**
     * @param $config
     *
     * @return Varien_Io_File
     * @throws Exception
     */
    public function createFeed($config)
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => Mage::getBaseDir('tmp')));
        $io->streamOpen($config['file_name_temp']);
        $io->streamWrite('<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL);
        $io->streamWrite('<rss xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/ns/1.0" version="2.0">' . PHP_EOL);
        $io->streamWrite(' <channel>' . PHP_EOL);
        return $io;
    }

    /**
     * @param $config
     *
     * @return Varien_Io_File
     * @throws Exception
     */
    public function createSpecificFeed($config)
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => Mage::getBaseDir('tmp')));
        $io->streamOpen($config['file_name_temp']);
        $io->streamWrite('<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL);
        $io->streamWrite('<rss xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/ns/1.0" version="2.0">' . PHP_EOL);
        $io->streamWrite(' <channel>' . PHP_EOL);
        return $io;
    }

    /**
     * @param                $row
     * @param Varien_Io_File $io
     */
    public function writeRow($row, Varien_Io_File $io)
    {
        $io->streamWrite($this->getXmlFromArray($row, 'item'));
    }

    /**
     * @param $data
     * @param $type
     *
     * @return string
     */
    public function getXmlFromArray($data, $type)
    {
        $outputEmpty = array('Backorder', 'Weergave');
        $xml = '  <' . $type . '>' . PHP_EOL;
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $key = preg_replace('/[0-9]*$/', '', $key);
                $xml .= '   <' . $key . '>' . PHP_EOL;
                foreach ($value as $ks => $vs) {
                    if (!empty($vs)) {
                        $xml .= '      <' . $ks . '>' . $this->cleanValue($vs) . '</' . $ks . '>' . PHP_EOL;
                    }
                }

                $xml .= '   </' . $key . '>' . PHP_EOL;
            } else {
                if (substr($key, 0, 23) == 'g:additional_image_link') {
                    $key = 'g:additional_image_link';
                }

                if (substr($key, 0, 14) == 'g:promotion_id') {
                    $key = 'g:promotion_id';
                }

                if (!empty($value) || in_array($key, $outputEmpty)) {
                    $xml .= '   <' . $key . '>' . $this->cleanValue($value) . '</' . $key . '>' . PHP_EOL;
                }
            }
        }

        $xml .= '  </' . $type . '>' . PHP_EOL;

        return $xml;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function cleanValue($value)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return htmlspecialchars($value, ENT_XML1);
        } else {
            return htmlspecialchars($value);
        }
    }

    /**
     * @param Varien_Io_File $io
     * @param                $config
     * @param                $footer
     */
    public function closeFeed(Varien_Io_File $io, $config, $footer)
    {
        $footer = $this->getXmlFromArray($footer, 'config');

        $io->streamWrite(' </channel>' . PHP_EOL);
        $io->streamWrite($footer);
        $io->streamWrite('</rss>' . PHP_EOL);
        $io->streamClose();

        $tmp = Mage::getBaseDir('tmp') . DS . $config['file_name_temp'];
        $new = $config['file_path'] . DS . $config['file_name'];

        if (!file_exists($config['file_path'])) {
            mkdir($config['file_path']);
        }

        rename($tmp, $new);
    }

    /**
     * @param Varien_Io_File $io
     * @param                $config
     * @param                $footer
     */
    public function closeSpecificFeed(Varien_Io_File $io, $config, $footer)
    {
        $footer = $this->getXmlFromArray($footer, 'config');

        $io->streamWrite(' </channel>' . PHP_EOL);
        $io->streamWrite($footer);
        $io->streamWrite('</rss>' . PHP_EOL);
        $io->streamClose();

        $tmp = Mage::getBaseDir('tmp') . DS . $config['file_name_temp'];
        $new = $config['file_path'] . DS . $config['file_name'];

        if (!file_exists($config['file_path'])) {
            mkdir($config['file_path']);
        }

        rename($tmp, $new);
    }

}