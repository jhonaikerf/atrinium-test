<?php

namespace App\Command;

use App\Document\Rate;
use App\Service\EcbClient;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncEcbHistoryCommand extends Command
{
    protected static $defaultName = 'app:sync-ecb-history';
    protected static $defaultDescription = 'Sync ecb rates history';

    private $ecbClient;

    private $dm;

    public function __construct(EcbClient $ecbClient, DocumentManager $dm)
    {
        parent::__construct();
        $this->ecbClient = $ecbClient;
        $this->dm = $dm;
    }

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->ecbClient->fetchHistory();
        $content = $response->getContent();
        $xml = simplexml_load_string($content);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        foreach($array['Cube']['Cube'] as $dataByDate) {
            $date = $dataByDate['@attributes']['time'];
            foreach ($dataByDate['Cube'] as $data) {
                $currency = $data['@attributes']['currency'];
                $rate = new Rate();
                $rate->setCurrency($currency);
                $rate->setDate($date);
                $rate->setRate($data['@attributes']['rate']);
                $this->dm->persist($rate);
            }
        }
        try {
            $this->dm->flush(['continueOnError' => true]);
        } catch (\Exception $e) {}
        return 0;
    }
}
