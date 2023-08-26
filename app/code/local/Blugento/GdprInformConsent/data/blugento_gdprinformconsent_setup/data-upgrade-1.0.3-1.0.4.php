<?php

$installer = $this;
$installer->startSetup();

$identifier = 'blugento-contact-gdpr-acknowledgement-ro';
$block = Mage::getModel('cms/block');

if ( ! $block->load($identifier)->getIdentifier()) {
    $content = '<p>Datele completate în formular vor fi prelucrate conform Regulamentului 2016/679/UE privind protecția 
        persoanelor fizice în ceea ce privește prelucrarea datelor cu caracter personal, în scopul comunicării cu dvs., 
        în urma solicitării create prin trimiterea lui. Completarea formularului reprezintă acordul dvs. pentru a fi 
        contactat prin email/telefon de un reprezentant al companiei noastre în vederea comunicării. Mai multe detalii 
        legate de protecția datelor cu caracter personal aflați din <a href="{{store url="politica-de-confidentialitate"}}">Politica de Confidențialitate.</a></p>';
    $block->setTitle('Blugento Contact Consent');
    $block->setIdentifier($identifier);
    $block->setStores(array(0));
    $block->setIsActive(1);
    $block->setContent($content);
    $block->save();
}