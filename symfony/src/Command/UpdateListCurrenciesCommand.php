<?php

namespace App\Command;

use App\Entity\Currency;
use App\Service\FixerClient;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateListCurrenciesCommand extends Command
{
    protected static $defaultName = 'app:update-list-currencies';
    protected static $defaultDescription = 'Update the list of Currencies';

    private $fixerClient;

    private $em;

    public function __construct(FixerClient $fixerClient, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->fixerClient = $fixerClient;
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response  = $this->fixerClient->fetchCurrencies();
        $statusCode = $response->getStatusCode();
        if($statusCode >= 300) {
            throw new \Exception('Error fetch api', $statusCode);
        } else {
            $content = $response->toArray();
            $isos = array_keys($content['symbols']);
            $currencyRepository = $this->em->getRepository(Currency::class);
            $currencyRepository->setInactives($isos, false);

            $actualCurrencies = $currencyRepository->findByIsos($isos);
            $list = [];
            foreach ($actualCurrencies as $currency){
                $list[$currency->getIso()] = '';
            }
            foreach($content['symbols'] as $iso => $description) {
                if(!isset($list[$iso])) {
                    $currency = new Currency();
                    $currency->setIso($iso);
                }
                $currency->setDescription($description);
                $currency->setActive(true);
                $this->em->persist($currency);
            }
            $this->em->flush();
        }

        return 0;
    }
}
