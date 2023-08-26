<?php
class Blugento_CategoryShowcase_Model_Catalog_Category
    extends Mage_Catalog_Model_Category
{
    /**
     * New display mode for showing subcategories
     */
    const DM_SUBCATEGORY = 'SUBCATEGORY';

    const DM_SUBCATEGORY_PRODUCTS = 'SUBCATEGORY_AND_PRODUCTS';

    const DM_SUBCATEGORY_PAGE = 'SUBCATEGORY_AND_PAGE';

    const DM_SUBCATEGORY_MIXED_ALL = 'SUBCATEGORY_MIXED_ALL';
}
