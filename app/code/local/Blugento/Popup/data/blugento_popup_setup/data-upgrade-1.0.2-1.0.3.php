<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * @category    Blugento
 * @author      Ciprian Mariuta <ciprian.mariuta@blugento.ro>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

$installer = $this;
$installer->startSetup();

try {
    $sql = 'SELECT store_id FROM core_store';

    $result = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);

    $stores = [];
    foreach ($result as $store) {
        $stores[] = $store['store_id'];
    }

    // Create block static for age pop-up
    $block = Mage::getModel('cms/block')->load('blugento_popup_age_popup');

    if (!$block->getId()) {
        $content = '
            <div class="age-popup-box">
                <h2>CONFIRMĂ VÂRSTA</h2>
                <p>Trebuie să ai 18 ani pentru a accesa acest site.</p>
                <div class="age-popup-buttons"><a class="button" href="javascript:void(0);" id="yes">Da</a> <a class="button" href="http://www.consuma-responsabil.ro/" id="no" target="_blank">Nu</a></div>
            </div>
        ';

        $block->setTitle('Blugento Popup Age Popup');
        $block->setIdentifier('blugento_popup_age_popup');
        $block->setIsActive(1);
        $block->setContent($content);
        $block->setStores($stores);
        $block->save();
    }

    // Create block static for newsletter pop-up
    $block = Mage::getModel('cms/block')->load('blugento_popup_newsletter_popup');

    if (!$block->getId()) {
        $content = '
            <div class="newsletter-pop-up">
                <p>Ai 10% discount la prima ta comandă online dacă te abonezi la newsletter-ul nostru pentru a primi vești despre promoții, lansări de produse noi și oferte speciale!</p>
                {{widget type="blugentonewsletter/widget_newsletter"}}
            </div>
        ';

        $block->setTitle('Blugento Popup Newsletter Popup');
        $block->setIdentifier('blugento_popup_newsletter_popup');
        $block->setIsActive(1);
        $block->setContent($content);
        $block->setStores($stores);
        $block->save();
    }

    // Create age pop-up
    $agePopup = Mage::getModel('blugento_popup/popup');
    $agePopup->setTitle('Blugento Age Popup');
    $agePopup->setContent('blugento_popup_age_popup');
    $agePopup->setStatus(2);
    $agePopup->save();

    // Create newsletter pop-up
    $newsletterPopup = Mage::getModel('blugento_popup/popup');
    $newsletterPopup->setTitle('Blugento Newsletter Popup');
    $newsletterPopup->setContent('blugento_popup_newsletter_popup');
    $newsletterPopup->setStatus(2);
    $newsletterPopup->save();
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
