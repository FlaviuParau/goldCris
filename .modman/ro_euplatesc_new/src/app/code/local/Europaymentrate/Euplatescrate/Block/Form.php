<?php

class europaymentrate_euplatescrate_Block_Form extends Mage_Payment_Block_Form
{

	private function get_bank(){
		return array(
		  'apb'=>'Alpha Bank',
		  'bcr'=>'Banca Comerciala Romana',
		  'btrl'=>'Banca Transilvania',            
		  'brdf'=>'BRD Finance',         
		  'rzb'=>'Raiffeisen Bank'                     
		);
	}
	
	private function get_bank2(){
		return array(
		  array('frm','unused'),
		  array('apb','Alpha Bank'),
		  array('bcr','Banca Comerciala Romana'),
		  array('btrl','Banca Transilvania'),            
		  array('brdf','BRD Finance'),         
		  array('rzb','Raiffeisen Bank')                    
		);
	}

    protected function _construct()
    {
        $this->setTemplate('euplatescrate/form.phtml');
        parent::_construct();
    }
	public function getBanksRate()
    {
		$html="";
		$banci=$this->get_bank();
		$banci2=$this->get_bank2();
		$rate_config = Mage::getStoreConfig('payment/euplatescrate/rateactive');
        $rate = explode(",", $rate_config);
				
		$rateorder = Mage::getStoreConfig('payment/euplatescrate/rateorder');
		if(strlen($rateorder)>1){
			$rateorder=explode(",",$rateorder);
			foreach($rateorder as $ord){
				if(array_search($banci2[$ord][0],$rate)!== false){
					$html.="<option value='".$banci2[$ord][0]."'>".$banci2[$ord][1]."</option>\n";
				}
			}
		}else{
			foreach($rate as $banca){
				$html.="<option value='$banca'>$banci[$banca]</option>\n";
			}
		}
			
		return $html;
	}
	
	public function getDisplayTypeRate()
    {
		return Mage::getStoreConfig('payment/euplatescrate/ratetype');
	}
	
	private function getRateTable(){
		$table["apb"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate1'));
		$table["bcr"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate2'));
		$table["btrl"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate3'));
		$table["brdf"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate4'));
		$table["rzb"]=explode(",",Mage::getStoreConfig('payment/euplatescrate/rate5'));
		return $table;
	}
	
	public function getRateDisponibile()
    {
		$table=$this->getRateTable();
		return "window.epr_table=epr_table=".json_encode($table).";";
	}
	
	public function getDisplayRate2()
    {

		$allrate=$this->getRateTable();
		
		$html="";
		
		$banci=$this->get_bank();
		$banci2=$this->get_bank2();
		$rate_config = Mage::getStoreConfig('payment/euplatescrate/rateactive');
        $rate = explode(",", $rate_config);
				
		$rateorder = Mage::getStoreConfig('payment/euplatescrate/rateorder');
		if(strlen($rateorder)>1){
			$rateorder=explode(",",$rateorder);
            $i=0;
			foreach($rateorder as $ord){
				if(array_search($banci2[$ord][0],$rate)!== false){
					foreach($allrate[$banci2[$ord][0]] as $nrrate){

						$html.="<tr><td><input name='epr_rate' type='radio'  id='option-".$i."' value='".$banci2[$ord][0]."-".$nrrate."' /><label for='option-".$i."'>&nbsp; ".$banci2[$ord][1]." in $nrrate rate</label></td></tr>";
					}
				}
			}
		}else{
			foreach($rate as $banca){
				foreach($allrate[$banca] as $nrrate){

					$html.="<tr><td><input name='epr_rate' type='radio' id='option-".$i."' value='".$banca."-".$nrrate."' /><label for='option-".$i."'>&nbsp; $banci[$banca] in $nrrate rate</label></td></tr>";
					$i++;
				}
			}
		}
		
		
		return $html;
	}
	
	public function haveRate(){
		$rate=Mage::getStoreConfig('payment/euplatescrate/rateactive');
		if(strlen($rate)==0) return false;
		$rate = explode(",", $rate);
		if(array_search("no",$rate)!== false){
			return false;
		}
		return true;
	}
}