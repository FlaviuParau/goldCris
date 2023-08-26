<?php
class Blugento_GdprUserData_Model_Observer
{
    const ROBOTS_CHANGE = '<reference name="head"><action method="setRobots"><value>NOINDEX,FOLLOW</value></action></reference>';

    public function changeRobots(Varien_Event_Observer $observer)
    {
        $uri = $observer->getEvent()->getAction()->getRequest()->getRequestUri();
        if (strpos($uri, 'gdpruserdata')) {
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addUpdate(self::ROBOTS_CHANGE);
            $layout->generateXml();
        }

        return $this;
    }
}

