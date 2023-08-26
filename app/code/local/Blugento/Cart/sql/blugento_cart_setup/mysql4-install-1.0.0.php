<?php
/**
 * Blugento cart Setup
 * installer script
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Billing
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

$installer = $this;

$installer->startSetup();

// Create contact CMS page
try {
    $cmsPage = Array(
        'title' => 'Blugento Product Inquiry',
        'identifier' => 'blugento-product-inquiry',
        'content' => getPageContent(),
        'is_active' => '1',
        'stores' => array(0),
        'root_template' => 'one_column',
    );
    Mage::getModel('cms/page')->setData($cmsPage)->save();
} catch (Exception $e) {
    Mage::logException($e);
}

// Add block permissions
try {
    /*
     * Make sure the upgrade is not performed on installations without the tables
     */
    $adminVersion = Mage::getConfig()->getModuleConfig('Mage_Admin')->version;
    if (version_compare($adminVersion, '1.6.1.2', '>=')) {
        $installer->getConnection()->insertMultiple(
            $installer->getTable('admin/permission_block'),
            array(
                array('block_name' => 'blugento_cart/form', 'is_allowed' => 1)
            )
        );
    }
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();

function getPageContent()
{
    return '<div class="row contact-page-container">
    <div class="col-6 col-sm-12 info-form">
        <div class="info">
            <h2>Company Information</h2>
            <p>Blushop Company - Registration 289.44/2016</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc a risus sit amet lorem sodales commodo quis sed nisl. Maecenas non velit in nunc condimentum fermentum vitae eget massa. Fusce interdum erat in finibus molestie. Integer egestas ornare justo, eget rutrum diam dapibus non.</p>
        </div>
        {{block type="blugento_cart/form" name="contactForm" template="blugento/cart/form.phtml"}}
    </div>

    <div class="col-6 col-sm-12 addresses">
        <div class="address">
            <h2>BluShop Name 1</h2>
            <p class="location">Street Address 1/23, CityName, Country 123456, Country</p>
            <img src="https://placeholdit.imgix.net/~text?txtsize=60&txt=560%C3%97180&w=560&h=180" alt="picture" />
            <ul class="program">
                <li>Monday - Friday -- 09:00 - 22:00</li>
                <li>Saturday - Sunday -- 09:00 - 14:00</li>
            </ul>
            <ul>
                <li><a href="mailto:contact@blushopname1.com" target="_blank">contact@blushopname1.com</a></li>
                <li>024 889 221 / 024 889 222</li>
            </ul>
        </div>
        <div class="address">
            <h2>BluShop Name 1</h2>
            <p class="location">Street Address 1/23, CityName, Country 123456, Country</p>
            <img src="https://placeholdit.imgix.net/~text?txtsize=60&txt=560%C3%97180&w=560&h=180" alt="picture"/>
            <ul class="program">
                <li>Monday - Friday -- 09:00 - 22:00</li>
                <li>Saturday - Sunday -- 09:00 - 14:00</li>
            </ul>
            <ul>
                <li><a href="mailto:contact@blushopname1.com" target="_blank">contact@blushopname1.com</a></li>
                <li>024 889 221 / 024 889 222</li>
            </ul>
        </div>
    </div>
</div>';
}
