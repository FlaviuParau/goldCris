<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the BSD-style license found in the
 * LICENSE file in the root directory of this source tree. An additional grant
 * of patent rights can be found in the PATENTS file in the code directory.
 *
 * @author      Marius Boia <marius.boia@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Facebook_AdsExtension_Model_System_Config_Source_Category extends Mage_Adminhtml_Model_System_Config_Source_Category
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('*');
        $options = array();
        foreach ($collection as $category) {
            if ($category->getName() != 'Root Catalog') {
                $options[] = array(
                    'label' => $category->getName(),
                    'value' => $category->getId()
                );
            }
        }
        return $options;
    }
}