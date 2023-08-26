<?php

$installer = $this;
$installer->startSetup();

$identifier = 'no-route';
$page = Mage::getModel('cms/page')
	->load($identifier, 'identifier');

$cmsPage = array(
	'title'         => '404 - Pagina nu a fost găsită',
	'identifier'    => 'no-route',
	'content'       => get404PageContent(),
	'is_active'     => 1,
	'root_template' => 'one_column',
);

if ($page->getId()) {
	$page->setContent($cmsPage['content']);
	$page->save();
} else {
	Mage::getModel('cms/page')->setData($cmsPage)->save();
}

$installer->endSetup();

function get404PageContent()
{
	return '<h2>Pagina nu a fost găsită</h2>
			<img src="/skin/frontend/base/default/localizer/images/error-404.png" alt="error 404" id="error-404-item">';
}