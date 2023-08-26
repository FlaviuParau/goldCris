<?php

$installer = $this;
$installer->startSetup();

// Update "Blugento Newsletter Checkbox Consent RO" CMS Block
$query = '
    UPDATE `cms_block` SET `content`="<p>Sunt de acord cu prelucrarea de către operator a datelor mele cu caracter personal 
    privind adresa de email, nume, prenume, nr. telefon, <strong>în scop de marketing direct</strong>, respectiv pentru a primi în continuare 
    ofertele operatorului privind serviciile și produsele puse la dispoziție. Datele dvs. sunt procesate în siguranță. Pentru 
    detalii legate de politicile noastre și drepturile dvs. privind prelucrarea datelor cu caracter personal accesați <a href=\"{{store url=\"politica-de-confidentialitate\"}}\">Politica 
    de Confidențialitate</a>. Temeiul prelucrării datelor este consimțământul iar durata prelucrării este întreaga perioadă a 
    existenței acestuia. Consimțământul va putea fi retras prin accesarea link-ului de Dezabonare prezent în newsletter sau 
    prin debifarea căsuței site Newsletter, prezente în contul de utilizator care va avea ca și consecință încetarea prelucrării.</p>"
    WHERE `identifier` LIKE "blugento-newsletter-checkbox-consent-ro"; 
';
$installer->run($query);

// Update "Blugento Newsletter Checkbox Consent EN" CMS Block
$query = '
    UPDATE `cms_block` SET `content`="<p>I agree to the processing of my personal data - e-mail address, name, surname, phone number - 
    by the controller for direct marketing purposes, respectively in order to continue
     receiving the controller’s offers related to products and services. Your data are processed securely.
      For further information regarding our policies and your rights in terms of data protection, please access our <a href=\"{{store url=\"politica-de-confidentialitate\"}}\">Privacy Policy</a>.
       The grounds for data processing are represented by your consent and the duration of the processing is equal to the existence of such consent.
        You can withdraw your consent by accessing the Unsubscribe link in the newsletter or by unchecking the Newsletter box on your user account,
         which will result in the cessation of processing.</p>"
    WHERE `identifier` LIKE "blugento-newsletter-checkbox-consent-en"; 
';
$installer->run($query);

// Update "Blugento Newsletter Checkbox Myaccountchecked RO" CMS Block
$query = '
    UPDATE `cms_block` SET `content`="<p>Prin debifarea acestei căsuțe vă dezabonați de la newsletter-ul nostru și retrageți 
    consimțământul pentru procesarea datelelor cu caracter personal (adresa de email, nume, prenume, nr. telefon) <strong>în 
    scop de marketing direct</strong>.</p>"
    WHERE `identifier` LIKE "blugento-newsletter-checkbox-myaccountchecked-ro";
';
$installer->run($query);

// Update "Blugento Checkout GDPR acknowledfement RO" CMS Block
$query = '
    UPDATE `cms_block` SET `content`="<p>Sunt de acord cu prelucrarea datelor mele cu caracter personal în vederea plasării comenzii 
    și creării opționale a contului, dacă s-a selectat opțiunea. Temeiul prelucrării îl reprezintă obligația contractuală, 
    în scopul livrării produselor comandate, durata prelucrării fiind perioada termenului de prescripție de 3 ani de la plasarea 
    comenzii. În măsura în care nu sunteți de acord cu prelucrarea datelor dvs, vă informăm că nu vom putea livra produsele 
    comandate. Drepturile dvs. în calitate de persoană vizată sunt garantate prin <a href=\"{{store url=\"politica-de-confidentialitate\"}}\">Politica de Confidențialitate</a>.</p>"
    WHERE `identifier` LIKE "blugento-checkout-gdpr-acknowledgement-ro";
';
$installer->run($query);

// Update "Blugento Checkout GDPR acknowledfement EN" CMS Block
$query = '
    UPDATE `cms_block` SET `content`="<p>I agree to the processing of my personal data in order to place an order and to create an
     account, if such option is selected. The grounds for processing reside in the contractual
      obligation regarding the delivery of the ordered products, the duration of such processing
       being equal to the limitation period of 3 years from the date of the order. Unless you agree
        to the processing of your personal data, we inform you that we will not be able to deliver
         the ordered products. Your rights as a Data Subject are secured by the <a href=\"{{store url=\"politica-de-confidentialitate\"}}\">Privacy Policy</a>.</p>"
    WHERE `identifier` LIKE "blugento-checkout-gdpr-acknowledgement-en";
';
$installer->run($query);

$installer->endSetup();