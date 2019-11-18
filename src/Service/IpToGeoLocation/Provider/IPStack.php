<?php

namespace App\Service\IpToGeoLocation\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class IPStack implements IpToGeoLocationProvider
{
    /**
     * @var string
     */
    private $endpointUrl;

    /**
     * @var string
     */
    private $accessKey;

    /**
     * IPStack constructor.
     * @param string $endpointUrl
     * @param string $accessKey
     */
    public function __construct($endpointUrl, $accessKey)
    {
        $this->accessKey = $accessKey;
        $this->endpointUrl = $endpointUrl;
    }

    /**
     * @inheritDoc
     * @throws GuzzleException
     */
    public function getCountryCodeByIp($ip)
    {
        $response = $this->callApi($ip, ['country_code']);

        if (empty($response['country_code'])) {
            return null;
        }

        return $response['country_code'];
    }

    /**
     * @param string $ip
     * @param array $fields
     * @return null|array
     * @throws GuzzleException
     */
    private function callApi($ip, $fields)
    {
        $client = new Client();
        $params = [
            'access_key' => $this->accessKey,
            'fields' => implode(',', $fields),
        ];

        $url = sprintf('%s/%s?%s', $this->endpointUrl, $ip, http_build_query($params));

        try {
            $response = $client->request('GET', $url);
        } catch (\Exception $exception) {
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $body = $response->getBody();
        $response = json_decode($body, true);

        if (empty($response)) {
            return null;
        }

        return $response;
    }
}