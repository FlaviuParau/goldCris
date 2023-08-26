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
 * @package     Blugento_HomepageManager
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Blugento_HomepageManager_Model_Cms_Observer
{
    public function changeContent($observer)
    {
	    $this->_generateRedirectUrl($observer);
	    
        $event = $observer->getEvent();
        $cms_page = $event->getPage();

        if ($cms_page->getIdentifier() == 'home') {
            $items = Mage::helper('blugento_homepagemanager')->getLayoutNodes(Mage::app()->getStore()->getStoreId());
            $rows = isset($items->nodes->row) && $items->nodes->row ? $items->nodes->row : array();

            $html = '';

            foreach ($rows as $row) {
                $has_banner = false;
                $has_banner_wide = false;
                $col_html = '';

                foreach ($row->cols->col as $col) {
                    $col_html .= '<div class="' . $col->class . '">' .
                                     (string) $col->text .
                                 '</div>';

                    if ($col->type == 'blugento_sliders/widget_slider') {
                        $has_banner = true;
                        $params = @json_decode($col->params, true);
						if(isset($params) && count($params)){
	                        foreach ($params as $param) {
	                            if ($param['name'] == 'parameters[banner_code]') {
	                                $code = $param['value'];
	                                $model = new Blugento_Sliders_Model_Group();
	
	                                try {
	                                    $model->loadByCode($code);
	                                    if ($model->getIsWide() == 1) {
	                                        $has_banner_wide = true;
	                                    }
	                                } catch (Exception $e) {
	                                    Mage::logException($e);
	                                }
	
	                                break;
	                            }
	                        }
						}
                    }
                }

                $row_banner_class  = $has_banner ? ' row-bn' : '';
                $row_banner_class .= $has_banner_wide ? ' row-bn-wide' : '';

                $row_class  = 'row-wrapper';
                $row_class .= ($row->full_width == 1) ? ' row-wrapper-wide' : ' row-wrapper-wide';

                $html .= '<div class="' . $row_class . $row_banner_class . '">' .
                             '<div class="row">' .
                                 $col_html .
                             '</div>' .
                         '</div>';
            }

            $cms_page->setContent($html);
        }

        return $this;
    }
	
	/**
	 * Permanent (301) redirect url from /home to root
	 *
	 * @param $observer
	 */
	protected function _generateRedirectUrl($observer)
	{
		$request    = $observer->getControllerAction()->getRequest();
		$requestUri = $request->getRequestUri();
		
		if ($requestUri == '/home') {
			$targetUrl = Mage::getBaseUrl();
			header('HTTP/1.1 301 Moved Permanently');
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Pragma: no-cache');
			header('Location: ' . $targetUrl);
			exit;
		}
	}
}
