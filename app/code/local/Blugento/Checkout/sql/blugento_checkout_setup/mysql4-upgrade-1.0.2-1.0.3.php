<?php

$installer = $this;

$installer->startSetup();

try {
	$block = Mage::getModel('cms/block')->load('blugento_success_page', 'identifier');
	if ($block) {
		$content = getContent();
		$block->setContent($content);
		$block->save();
	}
} catch (Exception $e) {
	Mage::logException($e);
}

$installer->endSetup();

function getContent()
{
	return '
<div class="page-title">
    <h1>Comanda dvs. a fost primită.</h1>
</div>

<h2 class="sub-title">Vă mulţumim pentru achiziţie!</h2>

<p>Nr. comenzii dvs. este: {{depend can_view_order}}<a href="{{var order_view_url}}">{{/depend}}{{var order_id}}{{depend can_view_order}}</a>{{/depend}}.</p>
<p>Veţi primi un e-mail de confirmare a comenzii cu detalii despre comanda dvs. şi o legătură să îi urmăriţi progresul.</p>

{{depend can_view_order}}
<p>Click <a onclick="this.target=\'_blank\'" href="{{var order_print_url}}">aici pentru a lista</a> o copie a confirmării comenzii dvs.</p>
{{/depend}}

<div class="buttons-set">
    <button onclick="window.location=\'{{var continue_shopping_url}}\'" title="Continuare Cumpărături" class="button" type="button"><span><span>Continuare Cumpărături</span></span></button>
</div>
    ';
}
