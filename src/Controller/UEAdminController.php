<?php

namespace App\Controller;

use App\Entity\UE;
use App\Form\UEType;
use App\Repository\UERepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;

final class UEAdminController extends AbstractController
{
    #[Route('/ue/admin', name: 'app_u_e_admin_index', methods: ['GET'])]
    public function index(UERepository $ueRepository, Request $request): Response
    {
        $ues = $ueRepository->findAll();

        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView('ue_admin/_table.html.twig', [
                'u_es' => $ues,
            ]);
            return $this->json([
                'success' => true,
                'html' => $html,
            ]);
        }

        return $this->render('ue_admin/index.html.twig', [
            'u_es' => $ues,
        ]);
    }

    #[Route('/ue/new', name: 'app_u_e_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ue = new UE();
        $form = $this->createForm(UEType::class, $ue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ue);
            $entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'UE créée avec succès'
            ]);
        }

        return $this->json([
            'form' => $this->renderView('ue_admin/_form.html.twig', [
                'u_e' => $ue,
                'form' => $form->createView(),
            ])
        ]);
    }

    #[Route('/ue/{id}', name: 'app_u_e_admin_show', methods: ['GET'])]
    public function show(UE $ue, UserRepository $userRepository): Response
    {
        $enrolledIds = array_map(
            fn($ins) => $ins->getUserId()->getId(),
            $ue->getInscriptions()->toArray()
        );

        $availableStudents = $userRepository->createQueryBuilder('u')
            ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
            ->setParameter('role', '"ROLE_STUDENT"')
            ->andWhere('u.id NOT IN (:ids)')
            ->setParameter('ids', $enrolledIds ?: [0])
            ->getQuery()
            ->getResult();

        return $this->render('ue_admin/show.html.twig', [
            'u_e' => $ue,
            'availableStudents' => $availableStudents,
        ]);
    }

    #[Route('/ue/{id}/edit', name: 'app_u_e_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UE $ue, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UEType::class, $ue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->json([
                'success' => true,
                'message' => 'UE modifiée avec succès'
            ]);
        }

        return $this->json([
            'form' => $this->renderView('ue_admin/_form.html.twig', [
                'u_e' => $ue,
                'form' => $form->createView(),
            ])
        ]);
    }

    #[Route('/ue/{id}', name: 'app_u_e_admin_delete', methods: ['POST'])]
    public function delete(Request $request, UE $ue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ue->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ue);
            $entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'UE supprimée avec succès'
            ]);
        }

        return $this->json([
            'success' => false,
            'message' => 'Token invalide'
        ], Response::HTTP_FORBIDDEN);
    }
}
