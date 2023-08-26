<?php

$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'smartbill_external_document_url', 'varchar(255)');

$newInvoiceEmail = Mage::getModel('core/email_template');
$newInvoiceEmail->setData('template_code', 'Smartbill Invoice Email Template');
$newInvoiceEmail->setData('template_type', Mage_Core_Model_Email_Template::TYPE_HTML);
$newInvoiceEmail->setData('template_subject', '{{var store.getFrontendName()}} - Factura Smartbill');
$newInvoiceEmailContent = '{{template config_path="design/email/header"}}
{{inlinecss file="email-inline.css"}}
<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="email-heading">
                        <span class="email-heading-image">
                            <img src="{{skin url=images/icon--checked-mail.png}}" alt="Checked icon" title="Checked Icon" />
                        </span>
                        <h1>Vă mulțumim pentru comanda dumneavoastră la <br> {{var store.getFrontendName()}}.</h1>
                        <p>Puteţi verifica starea comenzii accesând <a href="{{store url="customer/account/"}}">contul dumneavoastră</a>.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <table cellpadding="0" cellspacing="0" border="0" class="order-details-wrapper">
            <tr>
                <th><h4>Comanda</h4></th>
            </tr>
            <tr>
                <td>
                    <strong><span class="no-link">#{{var orderNumber}}</span></strong>
                </td>
            </tr>
        </table>
    </tr>
    <tr class="order-information">
        <td>
            {{if comment}}
            <table cellspacing="0" cellpadding="0" class="message-container">
                <tr>
                    <td>{{var comment}}</td>
                </tr>
            </table>
            {{/if}}
            <table cellpadding="0" cellspacing="0" border="0" class="order-items-details">
                <tr>
                    <td>
                        {{layout area="frontend" handle="sales_email_order_invoice_items" invoice=$invoice order=$order}}
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" class="message-container">
                <tr>
                    <td><span>Vezi factura: <span/>
                        <p>
                            <a href="{{var external_url}}">{{var external_url}}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="store-info">
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td><h3>Întrebări despre comandă?</h3></td>
                </tr>
                <tr>
                    <td>
                        {{depend store_phone}}
                        <h4>Telefon:</h4><br />
                        <p>
                            <a href="tel:{{var phone}}">{{var store_phone}}</a>
                        </p>
                        {{/depend}}
                        {{depend store_hours}}
                        <p>
                            <span class="no-link">{{var store_hours}}</span>
                        </p>
                        {{/depend}}
                    </td>
                    <td>
                        {{depend store_email}}
                        <h4>Email:</h4>
                        <p>
                            <a href="mailto:{{var store_email}}">{{var store_email}}</a>
                        </p>
                        {{/depend}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
{{template config_path="design/email/footer"}}';
$newInvoiceEmail->setData('template_text', $newInvoiceEmailContent);
$newInvoiceEmail->setData('added_at', date('Y-m-d H:i:s', time()));
$newInvoiceEmail->save();

$installer->endSetup();
?>