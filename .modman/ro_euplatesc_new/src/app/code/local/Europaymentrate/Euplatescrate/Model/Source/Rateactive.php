<?php

class europaymentrate_euplatescrate_Model_Source_RateActive
{
    public function toOptionArray()
	{
    return array(
      array('value'=>'no', 'label'=>'Dezactivat'),
      array('value'=>'apb', 'label'=>'Alpha Bank'),
      array('value'=>'bcr', 'label'=>'Banca Comerciala Romana'),
      array('value'=>'btrl', 'label'=>'Banca Transilvania'),            
      array('value'=>'brdf', 'label'=>'BRD Finance'),         
      array('value'=>'rzb', 'label'=>'Raiffeisen Bank')                     
    );
  }
}

