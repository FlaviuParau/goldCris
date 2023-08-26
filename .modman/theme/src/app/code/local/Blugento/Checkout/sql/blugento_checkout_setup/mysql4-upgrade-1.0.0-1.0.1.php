<?php
/**
 * Blugento Checkout
 * upgrade script
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Checkout
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$installer = $this;

$installer->startSetup();

try {
    $content = getCMSBlockContent();
    $block = Mage::getModel('cms/block')->load('blugento_success_page', 'identifier');
    $block->setTitle('Blugento Success Page');
    $block->setIdentifier('blugento_success_page');
    $block->setIsActive(0);
    $block->setContent($content);
    $block->setStores(array(0));
    $block->save();
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();

function getCMSBlockContent()
{
    return '
<div class="page-title">
    <h1>Comanda dvs. a fost primită.</h1>
</div>

<h2 class="sub-title">Vă mulţumim pentru achiziţie!</h2>

<p>Nr. comenzii dvs. este: <a href="{{var order_view_url}}">{{var order_id}}</a>.</p>
<p>Veţi primi un e-mail de confirmare a comenzii cu detalii despre comanda dvs. şi o legătură să îi urmăriţi progresul.</p>

{{depend order_print_url}}
<p>Click <a onclick="this.target=\'_blank\'" href="{{var order_print_url}}">aici pentru a lista</a> o copie a confirmării comenzii dvs.</p>
{{/depend}}

<div class="buttons-set">
    <button onclick="window.location=\'{{var continue_shopping_url}}\'" title="Continuare Cumpărături" class="button" type="button"><span><span>Continuare Cumpărături</span></span></button>
</div>
    ';
}
