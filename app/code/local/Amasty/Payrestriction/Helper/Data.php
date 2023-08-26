<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Payrestriction
 */ 
class Amasty_Payrestriction_Helper_Data extends Amasty_Commonrules_Helper_Data
{
    public function getAllMethods()
    {
        $hash = array();
        foreach (Mage::getStoreConfig('payment') as $code=>$config){
            if (!empty($config['title'])){
                $label = '';
                if (!empty($config['group'])){
                    $label = ucfirst($config['group']) . ' - ';
                }
                $label .= $config['title'];
                if (!empty($config['allowspecific']) && !empty($config['specificcountry'])){
                    $label .= ' in ' . $config['specificcountry'];    
                }
                $hash[$code] = $label;
                
            }
        }
        asort($hash);
        
        $methods = array();
        foreach ($hash as $code => $label){
            $methods[] = array('value' => $code, 'label' => $label);    
        }
        
        return $methods;      
    }
}