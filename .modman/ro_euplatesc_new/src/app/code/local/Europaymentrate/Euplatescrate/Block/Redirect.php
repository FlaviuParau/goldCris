<?php

class europaymentrate_euplatescrate_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
      $epay = Mage::getModel('euplatescrate/standard');

      $form = Mage::getModel('euplatescrate/form');
      $form->setAction($epay->geteuplatescrateUrl())
          	->setId('frmForm')
            ->setName('frmForm')
            ->setMethod('POST')
            ->setUseContainer(true);
      foreach ($epay->getCheckoutFormFields() as $field=>$value) {
		  if(is_array($value)){
			 foreach($value as $k=>$val){
				$form->addField($field."_".$k, 'hidden', array('name'=>$field."[]", 'value'=>$val));
			 }
		  }else{
            	$form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
		  }
      }
      $form->addField('ExtraData[successurl]','hidden',array('name'=>'ExtraData[successurl]','value'=>Mage::getUrl('euplatescrate/index/success')));
      $html = '<html><body><center>';
      $html.= $this->__('Vei fi redirectionat care EuPlatesc.ro in cateva secunde.');
      $html.= '<img src="https://www.euplatesc.ro/plati-online/tdsprocess/images/progress.gif"/>';
      $html.= $form->toHtml();
      $html.= '<script type="text/javascript">document.frmForm.submit();</script>';
      $html.= '</center></body></html>';

      return $html;
    }
}