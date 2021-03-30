<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Form\SectorType;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sector")
 */
class SectorController extends AbstractController
{
    /**
     * @Route("/", name="sector_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $queryBuilder = $this->get('doctrine')->getRepository(Sector::class)->findAllQueryBuilder();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );

        return $this->render(
            'sector/index.html.twig',
            [
                'pager' => $pagerfanta,
            ]
        );
    }

    /**
     * @Route("/new", name="sector_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sector = new Sector();
        $form = $this->createForm(SectorType::class, $sector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sector);
            $entityManager->flush();

            return $this->redirectToRoute('sector_index');
        }

        return $this->render('sector/new.html.twig', [
            'sector' => $sector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="sector_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sector $sector): Response
    {
        $form = $this->createForm(SectorType::class, $sector);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sector_index');
        }

        return $this->render('sector/edit.html.twig', [
            'sector' => $sector,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="sector_confirm_delete", methods={"GET"})
     */
    public function confirmDelete(Sector $sector): Response
    {
        return $this->render('sector/delete.html.twig', [
            'sector' => $sector,
        ]);
    }

    /**
     * @Route("/{id}", name="sector_delete", methods={"POST"})
     */
    public function delete(Request $request, Sector $sector): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sector->getId(), $request->request->get('_token'))) {
            if (count($sector->getCompanies()) > 0) {
                throw new \Exception('This sector is associated with a company and cannot be deleted');
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sector);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sector_index');
    }
}
