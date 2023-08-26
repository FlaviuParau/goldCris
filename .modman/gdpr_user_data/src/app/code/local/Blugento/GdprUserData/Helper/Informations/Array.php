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
 * @package     Blugento_GdprUserData
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_GdprUserData_Helper_Informations_Array extends Blugento_GdprUserData_Helper_Data
{

    /**
     *  Get an array with all needed account informations
     */
    public function getAccountInformationArray() {
        return [
            'firstname'  => 'Firstname',
            'lastname'   => 'Lastname',
            'email'      => 'Email',
            'created_at' => 'Created Date'
        ];
    }

    /**
     *  Get an array with all needed account address book informations
     */
    public function getAddressBookArray() {
        $addressArray = [
            'firstname'                     => 'Firstname',
            'lastname'                      => 'Lastname',
            'middlename'                    => 'Middlename',
            'street'                        => 'Street',
            'city'                          => 'City',
            'country_id'                    => 'Country ID',
            'region'                        => 'Region',
            'postcode'                      => 'Postcode',
            'telephone'                     => 'Telephone',
            'fax'                           => 'Fax',
            'blugento_customer_cnp'         => 'CNP',
            'company'                       => 'Company',
            'vat_id'                        => 'VAT ID',
            'blugento_customer_reg_no'      => 'Register Number',
            'blugento_customer_iban'        => 'IBAN',
            'blugento_customer_bank'        => 'Bank',
            'blugento_customer_headquarter' => 'Headquarter',
            'created_at'                    => 'Created Date'
        ];

        return $addressArray;
    }

    /**
     *  Get an array with all needed reviews informations
     */
    public function getReviewsArray() {
        return [
            'entity_pk_value' => 'Product Name',
            'title'           => 'Review Title',
            'detail'          => 'Review Detail',
            'created_at'      => 'Created Date'
        ];
    }

    /**
     *  Get an array with all needed wishlist informations
     */
    public function getWishlistArray() {
        return [
            'product_id'  => 'Product Name',
            'added_at'    => 'Added Date',
            'description' => 'Description',
            'qty'         => 'Quantity'
        ];
    }

    /**
     *  Get an array with all needed order informations
     */
    public function getOrderArray() {
        return [
            'increment_id'         => 'Order Number',
            'created_at'           => 'Created Date',
            'subtotal_incl_tax'    => 'Subtotal',
            'shipping_amount'      => 'Shipping Price',
            'grand_total'          => 'Total',
            'shipping_description' => 'Shipping Method',
            'customer_note'        => 'Order Comment'
        ];
    }

    /**
     *  Get an array with all needed order billing address informations
     */
    public function getOrderBillingAddressArray() {
        return [
            'firstname'                     => 'Billing Firstname',
            'lastname'                      => 'Billing Lastname',
            'middlename'                    => 'Billing Middlename',
            'email'                         => 'Billing Email',
            'street'                        => 'Billing Street',
            'city'                          => 'Billing City',
            'region'                        => 'Billing Region',
            'country_id'                    => 'Billing Country',
            'postcode'                      => 'Billing Postcode',
            'telephone'                     => 'Billing Telephone',
            'fax'                           => 'Billing Fax',
            'blugento_customer_cnp'         => 'Billing CNP',
            'company'                       => 'Billing Company',
            'vat_id'                        => 'Billing VAT ID',
            'blugento_customer_reg_no'      => 'Billing Register Number',
            'blugento_customer_iban'        => 'Billing IBAN',
            'blugento_customer_bank'        => 'Billing Bank',
            'blugento_customer_headquarter' => 'Billing Headquarter'
        ];
    }

    /**
     *  Get an array with all needed order shipping address informations
     */
    public function getOrderShippingAddressArray() {
        return [
            'firstname'                     => 'Shipping Firstname',
            'lastname'                      => 'Shipping Lastname',
            'middlename'                    => 'Shipping Middlename',
            'email'                         => 'Shipping Email',
            'street'                        => 'Shipping Street',
            'city'                          => 'Shipping City',
            'region'                        => 'Shipping Region',
            'country_id'                    => 'Shipping Country',
            'postcode'                      => 'Shipping Postcode',
            'telephone'                     => 'Shipping Telephone',
            'fax'                           => 'Shipping Fax',
            'blugento_customer_cnp'         => 'Shipping CNP',
            'company'                       => 'Shipping Company',
            'vat_id'                        => 'Shipping VAT ID',
            'blugento_customer_reg_no'      => 'Shipping Register Number',
            'blugento_customer_iban'        => 'Shipping IBAN',
            'blugento_customer_bank'        => 'Shipping Bank',
            'blugento_customer_headquarter' => 'Shipping Headquarter'
        ];
    }

    /**
     *  Get an array with all needed order items informations
     */
    public function getOrderItemsArray() {
        return [
            'name'         => 'Product Name',
            'weight'       => 'Weight',
            'sku'          => 'SKU',
            'qty_ordered'  => 'Quantity Ordered',
            'price'        => 'Price',
            'row_total'    => 'Total Price'
        ];
    }

    /**
     *  Get an array with all needed invoice informations
     */
    public function getInvoiceArray() {
        return [
            'increment_id'       => 'Invoice Number',
            'order_increment_id' => 'Order Number',
            'subtotal'           => 'Subtotal',
            'shipping_amount'    => 'Shipping Price',
            'base_grand_total'   => 'Total',
            'total_qty'          => 'Quantity Invoiced',
            'created_at'         => 'Created Date'
        ];
    }

    /**
     *  Get an array with all needed invoice items informations
     */
    public function getInvoiceItemsArray() {
        return [
            'name'               => 'Product Name',
            'price'              => 'Product Price',
            'qty'                => 'Quantity Invoiced',
            'row_total_incl_tax' => 'Total Price',
            'sku'                => 'SKU'
        ];
    }
}