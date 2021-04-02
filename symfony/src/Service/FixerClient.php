<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FixerClient
{
    private $client;

    private $uri;

    private $key;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->uri = "http://data.fixer.io/api";
        $this->key = $_ENV['FIXER_ACCESS_KEY'];
    }

    public function fetchCurrencies()
    {
        return $this->client->request(
            'GET',
            "$this->uri/symbols?access_key=$this->key",
        );
    }

    public function fetchRates(string $from, string $to, $date)
    {
        return $this->client->request(
            'GET',
            "$this->uri/$date?access_key=$this->key&base=$from&symbols=$to",
        );
    }
}
