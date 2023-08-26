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
 * @package     Blugento_CheckoutAutocomplete
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_CheckoutAutocomplete_AutocompleteController extends Mage_Core_Controller_Front_Action
{
    /**
     * Search city
     */
    public function cityAction()
    {
        $search = $this->getRequest()->getParam('search');
        $countryCode = $this->getRequest()->getParam('country_code');

        /** @var Blugento_CheckoutAutocomplete_Model_Autocomplete $autocomplete */
        $autocomplete = Mage::getModel('blugento_cautocomplete/autocomplete');

        $data = $autocomplete->getSearchData($search, $countryCode);

        $this->getResponse()->setBody(json_encode($data));
        return;
    }
}