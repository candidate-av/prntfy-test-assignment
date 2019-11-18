<?php

namespace App\Service\IpToGeoLocation;

use App\Service\IpToGeoLocation\Provider\IpToGeoLocationProvider;

class IpToGeoLocation
{
    /**
     * @var
     */
    private $provider;

    /**
     * @var string
     */
    private $defaultCountry;

    /**
     * IpToGeoLocation constructor.
     * @param IpToGeoLocationProvider $provider
     * @param string $defaultCountry
     */
    public function __construct(IpToGeoLocationProvider $provider, $defaultCountry)
    {
        $this->provider = $provider;
        $this->defaultCountry = $defaultCountry;
    }

    /**
     * @param string $ip
     * @return string
     */
    public function getCountryCodeByIp($ip)
    {
        return $this->provider->getCountryCodeByIp($ip) ?? $this->defaultCountry;
    }
}