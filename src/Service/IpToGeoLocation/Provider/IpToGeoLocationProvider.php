<?php

namespace App\Service\IpToGeoLocation\Provider;

interface IpToGeoLocationProvider
{
    /**
     * @param string $ip
     * @return mixed
     */
    public function getCountryCodeByIp($ip);
}