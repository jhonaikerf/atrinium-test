<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class EcbClient
{
    private $client;

    private $uri;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->uri = "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist.xml";
    }

    public function fetchHistory()
    {
        return $this->client->request(
            'GET',
            "$this->uri",
        );
    }
}
