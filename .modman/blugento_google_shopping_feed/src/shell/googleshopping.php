<?php

require_once 'abstract.php';

class Googleshopping_Shell_GenerateFeed extends Mage_Shell_Abstract
{

    const XPATH_ENABLED = 'googleshopping/generate/enabled';
    const XPATH_RESULT = 'googleshopping/generate/feed_result';

    /**
     *
     */
    public function run()
    {
        if ($generate = $this->getArg('generate')) {
            $storeIds = $this->getStoreIds($generate);
            foreach ($storeIds as $storeId) {
                $timeStart = microtime(true);
                $feed = Mage::getModel('googleshopping/googleshopping')->generateFeed($storeId, 'cli');
                echo $this->getResults($storeId, $feed, $timeStart) . PHP_EOL;
            }
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Returns all available storeIds for feed generation.
     *
     * @param $generate
     *
     * @return array
     */
    public function getStoreIds($generate)
    {
        $allStores = Mage::helper('googleshopping')->getStoreIds(self::XPATH_ENABLED);
        if ($generate == 'next') {
            $nextStore = Mage::helper('googleshopping')->getUncachedConfigValue('googleshopping/generate/cron_next');
            if (empty($nextStore) || ($nextStore >= count($allStores))) {
                $nextStore = 0;
            }

            Mage::getModel('core/config')->saveConfig('googleshopping/generate/cron_next', ($nextStore + 1), 'default', 0);
            return array($allStores[$nextStore]);
        }

        if ($generate == 'all') {
            return $allStores;
        }

        return explode(',', trim($generate));
    }

    /**
     * Parse and saves result.
     *
     * @param $storeId
     * @param $result
     * @param $timeStart
     *
     * @return string
     */
    public function getResults($storeId, $result, $timeStart)
    {
        if (!empty($result)) {
            $html = sprintf(
                '<a href="%s" target="_blank">%s</a><br/><small>On: %s (cli) - Products: %s/%s - Time: %s</small>',
                $result['url'],
                $result['url'],
                $result['date'],
                $result['qty'],
                $result['pages'],
                Mage::helper('googleshopping')->getTimeUsage($timeStart)
            );
            Mage::getModel('core/config')->saveConfig(self::XPATH_RESULT, $html, 'stores', $storeId);

            return sprintf(
                'Generated %s - Products: %s/%s - Time: %s',
                $result['url'],
                $result['qty'],
                $result['pages'],
                Mage::helper('googleshopping')->getTimeUsage($timeStart)
            );
        } else {
            return 'No feed found, please check storeId or is module is enabled';
        }
    }

    /**
     * Retrieve Usage Help Message.
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f googleshopping.php -- [options]
  --generate next     Generate next available store
  --generate all      Generate all stores    
  --generate <id>     Generate store <id> (comma seperated supported)

USAGE;
    }

}

$shell = new Googleshopping_Shell_GenerateFeed();
$shell->run();