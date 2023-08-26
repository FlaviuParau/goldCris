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
 * @package     Blugento_ProductMultitabs
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_ProductMultitabs_Model_System_Config_Source_Data
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toArray()
    {
        $data = array(
            array(
                'name' => 'Description',
                'identifier' => 'description',
                'content' => 'Product Description',
                'status'  => 1,
                'type'    => 'default'
            ),
            array(
                'name' => 'Information',
                'identifier' => 'additional',
                'content' => 'Additional Infomartion',
                'status'  => 1,
                'type'    => 'default'
            ),
            array(
                'name' => 'Reviews',
                'identifier' => 'product_reviews',
                'content' => 'Product Reviews',
                'status'  => 1,
                'type'    => 'default'
            ),
            array(
                'name' => 'Related Products',
                'identifier' => 'related_products',
                'content' => 'Related Products',
                'status'  => 0,
                'type'    => 'default'
            ),
            array(
                'name' => 'Attachment',
                'identifier' => 'mediathek',
                'content' => 'Product Attachment',
                'status'  => 0,
                'type'    => 'default'
            ),
        );

        return $data;
    }

    /**
     * Return tabs sort order array.
     *
     * @return array
     */
    public function toArrayOrder()
    {
        $data = array(
            'description'      => 1,
            'additional'       => 2,
            'related_products' => 3,
            'mediathek'        => 4,
            'product_reviews'  => 99
        );

        return $data;
    }
}