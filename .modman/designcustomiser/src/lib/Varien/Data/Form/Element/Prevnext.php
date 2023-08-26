<?php
/**
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
 * @package     Blugento_DesignCustomiser
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form next|prev element
 */
class Varien_Data_Form_Element_Prevnext extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('prevnext');
    }

    public function getElementHtml()
    {
        /** @var Blugento_DesignCustomiser_Helper_Data $helper */
        $helper = Mage::helper('blugento_designcustomiser');

        $dbCssCollection = Mage::getModel('blugento_designcustomiser/finalcss')->getCollection();

        $versionIds = array(0=>0);
        foreach ($dbCssCollection as $cssVersions) {
            $versionIds[] = $cssVersions->getId();
        }

        $totalRevisions = count($dbCssCollection);

        if ($totalRevisions <=1) {
            return $helper->__('No Previous CSS Versions Found');
        }
        $backupRevision = Mage::app()->getRequest()->getParam('revision');

        $html = $this->getBeforeElementHtml();

        $firstItemId = $dbCssCollection->getFirstItem()->getId();
        $lastItemId  = $dbCssCollection->getLastItem()->getId();

        $currentRevision = $backupRevision ? $backupRevision : $lastItemId;

        foreach ($versionIds as $key=>$id) {
            if ($currentRevision == $id) {
                $currentRevisionD = $key;
            }
        }

        $html .= '<p>'. $helper->__('CSS Version <strong>%s</strong> from <strong>%s</strong> versions', $currentRevisionD, $totalRevisions) . '</p>';

        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $currentUrl = explode('=', $currentUrl);

        $url = isset($currentUrl[0]) ? $currentUrl[0] : '';

        if ($backupRevision) {
            $curRev = isset($currentUrl[1]) ? $currentUrl[1] : '';
            foreach ($versionIds as $key=>$id) {
                if ($curRev == $id) {
                    $prevRevision = $versionIds[$key-1];
                    $nextRevision = $versionIds[$key+1];
                }
            }
            $prevUrl = $url . '=' . $prevRevision;
            $nextUrl = $url . '=' . $nextRevision;
        } else {
            $curRev = $lastItemId;
            foreach ($versionIds as $key=>$id) {
                if ($curRev == $id) {
                    $prevRevision = $versionIds[$key-1];
                    $nextRevision = $versionIds[$key+1];
                }
            }
            $prevUrl = $url . '?revision=' . $prevRevision;
            $nextUrl = $url . '?revision=' . $nextRevision;
        }

        if ($backupRevision == $firstItemId) {
            $html .=  "<a class='prev-version'></a>";
        } else {
            $html .= "<a class='prev-version' href='" . $prevUrl . "'>  </a> ";
        }

        $html .=  '&nbsp;&nbsp;';

        if ($backupRevision) {
            if ($backupRevision == $lastItemId) {
                $html .=  "<a class='next-version'></a>";
            } else {
                $html .= "<a class='next-version' href='" . $nextUrl . "'>  </a> ";
            }
        }
        $html .=  '&nbsp;&nbsp;&nbsp;&nbsp;';

	    if ($totalRevisions > 1) {
		    $verifyMode = Mage::getSingleton('admin/session')->getDesignCustomiserMode();
		    $url = $verifyMode == 'advanced' ? 'adminhtml/adminhtml_advanced/delete' : 'adminhtml/adminhtml_design/delete';
        	$delUrl = Mage::helper('adminhtml')->getUrl($url, array('version_id' => $currentRevision));
            $html .=  '<a class="delete-version" href="' . $delUrl . '">' . $helper->__('Delete Version') . '</a>';
        }

        $html .= $this->getAfterElementHtml();

        return $html;
    }
}
