<?php
class Magecomp_Recaptcha_Model_Source_Pagessource{
	public function toOptionArray(){
		return array(
			array('value'=>'0','label'=>'--NO SELECTION--'),
			array('value'=>'1','label'=>'Contact Form'),
			array('value'=>'2','label'=>'Product Review Form'),
			array('value'=>'3','label'=>'Register Customer Form'),
			array('value'=>'4','label'=>'Customer Login / Depreciated'),
			array('value'=>'4','label'=>'Onepage Checkout'),
        );
	}
}
?>