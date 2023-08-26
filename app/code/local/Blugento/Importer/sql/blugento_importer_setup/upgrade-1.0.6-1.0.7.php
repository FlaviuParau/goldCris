<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'run_flag', array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'nullable' => true,
        'after' => 'cron_run_frequency',
        'default' => 0,
        'comment' => 'Run Flag'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('blugento_importer/importer'), 'email', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'default' => '',
        'comment' => 'Email to send when profile is finished'
    ));

//add email template
$code = 'Importer Profile Finished';
$subject = '{{var store.getFrontendName()}}- Profile Finished';
$text = '{{template config_path="design/email/header"}}
{{inlinecss file="email-inline.css"}}

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="action-content">
            <h1>Dear {{var admin_name}},</h1>
            <ul>
                <li>{{var profile_name}} profile is finished...</li>
            </ul>
        </td>
    </tr>
</table>

{{template config_path="design/email/footer"}}';
$styles = '';

$template = Mage::getModel('adminhtml/email_template');

$template->setTemplateSubject($subject)
    ->setTemplateCode($code)
    ->setTemplateText($text)
    ->setTemplateStyles($styles)
    ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
    ->setAddedAt(Mage::getSingleton('core/date')->gmtDate())
    ->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML);

$template->save();

$installer->endSetup();

