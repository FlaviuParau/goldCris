<?php

$installer = $this;
$installer->startSetup();

//Update "Restricţii cookie" CMS Block
$query = '
    UPDATE `cms_block` SET `content`="<p>Acest site utilizează cookie-uri pentru a oferi toate funcționalitățile sale. 
    Pentru detalii, citiți <a href=\"{{store url=\"politica-de-utilizare-cookie-uri\"}}\">Politica de utilizare Cookie-uri</a>. Pentru informații 
    legate de protecția datelor, accesați <a href=\"{{store url=\"politica-de-confidentialitate\"}}\">Politica de confidențialitate</a></p>"
    WHERE `identifier` LIKE "cookie_restriction_notice_block";
';
$installer->run($query);

$installer->endSetup();