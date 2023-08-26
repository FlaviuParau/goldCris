<?php

class Blugento_Fullfeed_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Generate feed directly from link without validations.
     */
    public function generateAction()
    {
        $feedNumber = $this->getRequest()->getParam('feed');
        $allowedNumbers = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);

        if ($feedNumber && in_array($feedNumber, $allowedNumbers)){

            /** @var Blugento_FullFeed_Model_Feed $model */
            $model = Mage::getModel('blugento_fullfeed/feed');

            $model->runFeedGeneration($feedNumber);
        } else {
            echo 'Please insert feed number to run. To find the feed number go to <b>System->Configuration->Full Feed</b> and get the number of the <b>Custom Feed</b> tab.';
            echo '<br>';
            echo 'Create the link like this: [website]/fullfeed/index/generate/feed/[feed_number]';
        }
    }

    /**
     * Generate feed exactly like cron does.
     */
    public function cronAction()
    {
        /** @var Blugento_FullFeed_Model_Feed $model */
        $model = Mage::getModel('blugento_fullfeed/feed');

        $model->cronGenerateFeed();
    }

    /**
     * Set cache time to 1 if is 10000
     */
    public function timeAction()
    {
        /** @var Blugento_FullFeed_Model_Feed $model */
        $model = Mage::getModel('blugento_fullfeed/feed');

        $model->cronResetCacheTime();
    }
}