<?php

namespace App\Controller;

use App\Document\Rate;
use App\Entity\FixerLog;
use App\Form\FixerLogType;
use App\Service\FixerClient;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class FixerController extends AbstractController
{
    /**
     * @Route("/fixer", name="fixer", methods={"GET","POST"})
     */
    public function index(
        Request $request,
        SessionInterface $session,
        FixerClient $fixerClient,
        DocumentManager $dm
    ): Response {
        $fixerLog = new FixerLog();
        $form = $this->createForm(FixerLogType::class, $fixerLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fromIso = $fixerLog->getFromCurrency()->getIso();
            $toIso = $fixerLog->getToCurrency()->getIso();
            $date = $fixerLog->getDate()->format('Y-m-d');
            $amount = $fixerLog->getFromAmount();
            $entityManager = $this->getDoctrine()->getManager();
            $oldFixerLog = $entityManager->getRepository(FixerLog::class)->findOneByConvert(
                $fromIso,
                $toIso,
                $date,
            );

            if(!$oldFixerLog) {
                $response = $fixerClient->fetchRates(
                    $fromIso,
                    $toIso,
                    $date,
                );
                $content = $response->toArray();
                if($response->getStatusCode() >= 300 || $content['success'] !== true) {
                    $type = $content['error']['type'];
                    $this->addFlash('danger', "The amount is not converted by api! $type");
                } else {
                    $rate = $content['rates'][$fixerLog->getToCurrency()->getIso()];
                    $fixerLog->setRate($rate);
                    $fixerLog->setToAmount($amount * $rate);
                    $this->addFlash('success', 'The amount is converted by api!');
                }
            } else {
                $fixerLog->setToAmount($amount * $oldFixerLog->getRate());
                $this->addFlash('success', 'The amount is converted by database!');
            }
            $entityManager->persist($fixerLog);
            $entityManager->flush();

            $ecbRate = null;
            $fromEcbRate = null;
            if($fromIso != 'EUR') {
                $fromEcbRate = $dm->getRepository(Rate::class)->findOneBy([
                    'currency' => $fromIso,
                    'date' => $date
                ]);
            }

            if ($toIso == 'EUR' && $fromEcbRate) {
                $ecbRate = $amount / $fromEcbRate->getRate();
            } else if ($toIso != 'EUR') {
                $toEcbRate = $dm->getRepository(Rate::class)->findOneBy([
                    'currency' => $toIso,
                    'date' => $date
                ]);
                if($fromIso == 'EUR' && $toEcbRate){
                    $ecbRate = $amount * $toEcbRate->getRate();
                } else if ($fromEcbRate && $toEcbRate){
                    $ecbRate = ($amount / $fromEcbRate->getRate())* $toEcbRate->getRate();
                }
            }

            return $this->render('fixer/result.html.twig', [
                'fixerLog' => $fixerLog,
                'ecbRate' => round($ecbRate, 2),
            ]);
        }

        return $this->render('fixer/index.html.twig', [
            'fixerLog' => $fixerLog,
            'form' => $form->createView(),
        ]);
    }
}
