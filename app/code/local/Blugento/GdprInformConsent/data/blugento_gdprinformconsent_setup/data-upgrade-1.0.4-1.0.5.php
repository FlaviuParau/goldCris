<?php

$installer = $this;
$installer->startSetup();

$identifierEN = 'blugento-contact-gdpr-acknowledgement-en';
$blockEN = Mage::getModel('cms/block');

if (!$blockEN->load($identifierEN)->getIdentifier()) {
	$contentEN = '<p>The data filled in the form will be processed according to Regulation 2016/679 / EU on the protection
                of individuals with regard to the processing of personal data for the purpose of communication with you,
                following your request by sending it. Completing the form is your agreement to be contacted by email /
                phone by a Carel.ro representative for communication. For more details on personal data protection check
                <a href="{{store url="politica-de-confidentialitate"}}">Privacy Policy.</a></p>';
	$blockEN->setTitle('Blugento Contact Consent');
	$blockEN->setIdentifier($identifierEN);
	$blockEN->setStores(array(0));
	$blockEN->setIsActive(0);
	$blockEN->setContent($contentEN);
	$blockEN->save();
}

$identifierDE = 'blugento-contact-gdpr-acknowledgement-de';
$blockDE = Mage::getModel('cms/block');

if (!$blockDE->load($identifierDE)->getIdentifier()) {
	$contentDE = '<p>Die mit dem Formular ausgefüllten Daten werden gemäß der Verordnung 2016/679 / EU über den Schutz
                personenbezogener Daten bei der Verarbeitung personenbezogener Daten zum Zweck der Kommunikation mit
                Ihnen auf Ihren Antrag hin mit der Übermittlung verarbeitet. Ausfüllen des Formulars ist Ihre
                Zustimmung, per E-Mail / Telefon von einem Carel.ro Vertreter für die Kommunikation kontaktiert zu
                werden. Weitere Einzelheiten zum Datenschutz finden Sie in der
                <a href="{{store url="politica-de-confidentialitate"}}">Datenschutzerklärung.</a></p>';
	$blockDE->setTitle('Blugento Contact Consent');
	$blockDE->setIdentifier($identifierDE);
	$blockDE->setStores(array(0));
	$blockDE->setIsActive(0);
	$blockDE->setContent($contentDE);
	$blockDE->save();
}

$installer->endSetup();
