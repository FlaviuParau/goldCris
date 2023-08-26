<?php
/**
 * Class Me_Lff_Model_Container_Left_Leftamountinfo
 *
 * @category  Me
 * @package   Me_Lff
 * @author    Attila Sági <sagi.attila@magevolve.com>
 * @copyright 2015 Magevolve Ltd. (http://magevolve.com)
 * @license   http://magevolve.com/terms-and-conditions Magevolve Ltd. License
 * @link      http://magevolve.com
 */

/**
 * Class Me_Lff_Model_Container_Left_Leftamountinfo
 */
class Me_Lff_Model_Container_Left_Leftamountinfo extends Enterprise_PageCache_Model_Container_Sidebar_Cart
{
    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $blockClass = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');

        $block = new $blockClass;
        $block->setTemplate($template);
        $block->setNameInLayout('left');

        return $block->toHtml();
    }

    /**
     * Save data to cache storage. Store many block instances in one cache record depending on additional cache ids.
     *
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param null|int $lifetime
     * @return Enterprise_PageCache_Model_Container_Advanced_Abstract
     */
    protected function _saveCache($data, $id, $tags = array(), $lifetime = null)
    {
        return false;
    }
}
