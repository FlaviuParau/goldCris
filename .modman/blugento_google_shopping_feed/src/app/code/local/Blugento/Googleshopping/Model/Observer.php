<?php

class Blugento_Googleshopping_Model_Observer
{

    /**
     *
     */
    public function scheduledGenerateGoogleshopping()
    {
        Mage::getModel('googleshopping/googleshopping')->runScheduled();
    }

}