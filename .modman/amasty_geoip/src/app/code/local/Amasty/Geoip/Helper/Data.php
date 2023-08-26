<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */   
class Amasty_Geoip_Helper_Data extends Mage_Core_Helper_Url
{
    public function getLongIpV6($ip)
    {
        $ipN = inet_pton($ip);
        $binary = '';
        for ($bit = strlen($ipN) - 1; $bit >= 0; $bit--) {
            $binary = sprintf('%08b', ord($ipN[$bit])) . $binary;
        }

        if (function_exists('gmp_init')) {
            return gmp_strval(gmp_init($binary, 2), 10);
        } elseif (function_exists('bcadd')) {
            $decimal = '0';
            for ($i = 0; $i < strlen($binary); $i++) {
                $decimal = bcmul($decimal, '2', 0);
                $decimal = bcadd($decimal, $binary[$i], 0);
            }

            return $decimal;
        } else {
            throw new \Exception('GMP or BCMATH extension not installed!');
        }
    }
}
