<?php

class AW_Blog_Model_System_Config_Source_Description
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $array = array(
            array(
                'value' => 0,
                'label' => Mage::helper('blog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('blog')->__('Yes')
            ),
            array(
                'value' => 2,
                'label' => Mage::helper('blog')->__('Limit(150 char)')
            )
        );
        
        return $array;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array(
            0 => 'No',
            1 => 'Yes',
            2 => 'Limit(150 char)'
        );
        
        return $options;
    }
}
