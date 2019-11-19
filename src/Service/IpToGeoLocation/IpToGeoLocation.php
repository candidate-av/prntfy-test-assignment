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
     * @param $defaultCountry
     * @param IpToGeoLocationProvider|null $provider
     */
    public function __construct($defaultCountry, IpToGeoLocationProvider $provider = null)
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
        if (is_null($this->provider)) {
            return $this->defaultCountry;
        }

        return $this->provider->getCountryCodeByIp($ip) ?? $this->defaultCountry;
    }
}