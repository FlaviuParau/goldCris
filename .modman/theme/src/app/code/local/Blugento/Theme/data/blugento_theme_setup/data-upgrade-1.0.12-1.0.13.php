<?php

$installer = $this;
$installer->startSetup();

//Update "Restricţii cookie" CMS Block
$query = '
    UPDATE `cms_block` SET `content`="<p>Acest site utilizează cookie-uri pentru a oferi toate funționalitățile sale. 
    Pentru detalii, citiți <a href=\"{{store url=\"politica-de-utilizare-cookie-uri\"}}\">Politica de utilizare Cookie-uri</a>. Pentru informații 
    legate de protecția datelor, accesați <a href=\"{{store url=\"politica-de-confidentialitate\"}}\">Politica de confidențialitate</a></p>"
    WHERE `identifier` LIKE "cookie_restriction_notice_block";
';
$installer->run($query);

//Update "Politica de utilizare cookie-uri" CMS Page
$query = '
    UPDATE `cms_page` SET `identifier`="politica-de-utilizare-cookie-uri"
    WHERE `identifier` LIKE "cookie-policy";
';
$installer->run($query);

//Update "Politica de confidentialitate" CMS Page
$query = '
    UPDATE `cms_page` SET `identifier`="politica-de-confidentialitate"
    WHERE `identifier` LIKE "privacy-policy";
';
$installer->run($query);

//Update "Footer Links" CMS Block
$identifier = 'footer_links';
$footerBlock = Mage::getModel('cms/block')->load($identifier);
$content = $footerBlock->getContent();

$content = str_replace('privacy-policy', 'politica-de-confidentialitate', $content);
$content = str_replace('cookie-policy', 'politica-de-utilizare-cookie-uri', $content);

$query = '
    UPDATE `cms_block` SET `content`="'.addslashes($content).'"
    WHERE `identifier` LIKE "footer_links";
';
$installer->run($query);

$installer->endSetup();