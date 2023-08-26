<?php

$installer = $this;
$installer->startSetup();

$identifier = 'blugento-newsletter-checkbox-consent-ro';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p>Sunt de acord cu prelucrarea de catre operator a datelor mele cu caracter personal privind adresa de 
email, nume, prenume, nr. telefon, <strong>în scop de marketing direct</strong>, respectiv pentru a primi în continuare ofertele 
operatorului privind serviciile si produsele puse la dispozitie.Datele dvs. sunt procesate în siguranta. Pentru detalii 
legate de politicile noastre si drepturile dvs. privind prelucrarea datelor cu caracter personal accesati <u>Politica de 
Confidentialitate</u>. Temeiul prelucrării datelor este consimțământul si durata prelucrării este întreaga perioadă a 
existenței acestuia. Consimțământ va putea fi retras prin accesarea link-ului de Dezabonare prezent în newsletter sau 
prin debifarea căsuței site Newsletter, prezente în contul de utilizator care va avea ca și consecință încetarea 
prelucrării.</p>';
    $block->setTitle('Blugento Newsletter Checkbox Consent');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$identifier = 'blugento-newsletter-checkbox-consent-en';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p>I agree for my personal details (email, name) to be stored, processed and used <strong>for direct marketing 
purposes</strong>, to receive offers and details about operator\'s products and services.
Data will be processed as per this explicit content and is valid for 1 year. <strong>Consent can be revoked by accessing the 
Unsubscribe link in the newsletter or through unchecking the newsletter checkbox in user\'s account on the site; all 
data processing will be stopped after that point</strong>.
All details are securely processed. For more details, you can read out <u>Privacy Policy</u>. Through expressing your consent, 
you agree that you were informed regarding your privacy rights.</p>';
    $block->setTitle('Blugento Newsletter Checkbox Consent');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(0);
    $block->setContent($content);
    $block->save();
}

$identifier = 'blugento-newsletter-checkbox-myaccountchecked-ro';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p>Prin debifarea acestei căsuțe vă dezabonați de la newsletter-ul nostru si retrageti consimtamantul 
pentru procesarea datelelor cu caracter personal (adresa de email, nume, prenume, nr. telefon) <strong>în scop de marketing direct</strong></p>';
    $block->setTitle('Blugento Newsletter Checkbox Myaccountchecked');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$identifier = 'blugento-newsletter-checkbox-myaccountchecked-en';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p>By unchecking this box, you unsubscribe from our newsletter and withdraw your consent for
 the processing of your personal data (e-mail address, name, surname, phone number) <strong>for direct marketing purposes.</strong></p>';
    $block->setTitle('Blugento Newsletter Checkbox Myaccountchecked');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(0);
    $block->setContent($content);
    $block->save();
}

$identifier = 'blugento-checkout-gdpr-acknowledgement-ro';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p>Sunt de acord cu prelucrarea datelor mele cu caracter personal în vederea plasării comenzii si creării opțional contului dacă s-a selectat opțiunea. Temeiul 
prelucrării îl reprezintă obligația contractuală, în scopul livrării produselor comandate, durata prelucrării fiind 
perioada termenului de prescripție de 3 ani de la plasarea comenzii. În măsura în care nu sunteți de acord cu prelucrarea 
datelor dvs, vă informăm că nu vom putea livra produsele comandate. Drepturile dvs. în calitate de persoană vizată sunt 
garantate prin <u>Politica de Confidențialitate</u></p>';
    $block->setTitle('Blugento Checkout GDPR acknowledfement');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}

$identifier = 'blugento-checkout-gdpr-acknowledgement-en';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p>I agree that my personal details will be processed for the order to be placed and creation of an account if option was selected. The legal basis for the 
personal details processing is our contractual requirement to deliver the ordered products, the duration for the processing 
being the prescription period of 3 years from the date the order was placed. If you do not agree with the processing of 
your personal details, we inform you that we will not be able to deliver the ordered products. Your privacy rights are 
guaranteed through our <u>Privacy Policy</u></p>';
    $block->setTitle('Blugento Checkout GDPR acknowledfement');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(0);
    $block->setContent($content);
    $block->save();
}

$installer->endSetup();
