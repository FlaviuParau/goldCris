<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

// [v1.5]
class Simtech_Searchanise_Model_Mysql4_Fulltext_Collection extends Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
{
    /**
     * Searchanise Collection Product
     *
     * @var Simtech_Searchanise_Model_Searchanise
     */
    protected $_searchaniseCollection = null;

    /**
     * Initialize resource
     * @return Simtech_Searchanise_Model_Mysql4_Product_CollectionSearhanise
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_searchaniseCollection = Mage::getModel('searchanise/searchanise');
        $this->_searchaniseCollection->setCollection($this);
    }

    public function __construct($resource=null)
    {
        parent::__construct($resource);

        if (Mage::helper('searchanise/ApiSe')->checkSearchaniseResult(true)) {
            $this->setSearchaniseRequest(Mage::helper('searchanise')->getSearchaniseRequest());
            if ($this->checkSearchaniseResult()) {
                $this->addSearchaniseFilter();
            }
        }
    }

    public function initSearchaniseRequest()
    {
        return $this->_searchaniseCollection->initSearchaniseRequest();
    }

    public function checkSearchaniseResult()
    {
        return $this->_searchaniseCollection->checkSearchaniseResult();
    }

    public function setSearchaniseRequest($request)
    {
        return $this->_searchaniseCollection->setSearchaniseRequest($request);
    }

    public function getSearchaniseRequest()
    {
        return $this->_searchaniseCollection->getSearchaniseRequest();
    }

    public function addSearchaniseFilter()
    {
        return $this->_searchaniseCollection->addSearchaniseFilter();
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        $this->_searchaniseCollection->setOrder($attribute, $dir);
        return $this;
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext_Collection
     */
    public function setOrderParent($attribute, $dir = 'desc')
    {
        return parent::setOrder($attribute, $dir);
    }

    /**
     * Retrieve collection last page number
     *
     * @return int
     */
    public function getLastPageNumber()
    {
        return $this->_searchaniseCollection->getLastPageNumber();
    }

    /**
     * Retrieve collection last page number
     *
     * @return int
     */
    public function getLastPageNumberParent()
    {
        return parent::getLastPageNumber();
    }

    public function getSize()
    {
        if ($this->checkSearchaniseResult()) {
            return $this->getSearchaniseRequest()->getTotalProduct();
        } else {
            return parent::getSize();
        }
    }

    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        $args = func_get_args();

        if (!$this->checkSearchaniseResult()) {
            return call_user_func_array(array(__CLASS__, 'parent::_loadEntities'), $args);
        }

        // Load items and add them to the magento result manually
        $results = $this->_searchaniseCollection->getSearchaniseRequest()->getSearchResult();

        if (!empty($results['items'])) {
            $productIds = array_map(function($product_data) {
                return $product_data['product_id'];
            }, $results['items']);

            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addIdFilter($productIds)
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->load();

            // Sort items accoring to returning searchanise result
            foreach ($results['items'] as $item) {
                foreach ($products as $product) {
                    if ($item['product_id'] == $product->getId()) {
                        $this->addItem($product);
                        break;
                    }
                }
            }
        }

        return $this;
    }
}
