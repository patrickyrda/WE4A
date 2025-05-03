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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
    
final class UEAdminController extends AbstractController{
    #[Route('/ue/admin',name: 'app_u_e_admin_index', methods: ['GET'])]
    public function index(UERepository $ueRepository, Request $request): Response
    {
        $ues = $ueRepository->findAll();

        if ($request->isXmlHttpRequest()) {
            // rendu du partial uniquement
            $html = $this->renderView('ue_admin/_table.html.twig', [
                'u_es' => $ues,
            ]);

            return $this->json([
                'success' => true,
                'html'    => $html,
            ]);
        }
        return $this->render('ue_admin/index.html.twig', [
            'u_es' => $ues,
        ]);
    }
    #[Route('ue/new', name: 'app_u_e_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uE = new UE();
        $form = $this->createForm(UEType::class, $uE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($uE);
            $entityManager->flush();
             //TODO: Answer to Ajax request, when receiving succes you need to reload the window
            //return $this->redirectToRoute('app_u_e_admin_index', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                'success' => true,
                'message' => 'UE created successfully']);
        }

        
        return $this->json([
            'form' => $this->renderView('ue_admin/_form.html.twig', [
                'u_e' => $uE,
                'form' => $form->createView()
            ])
        ]);
    }

    #[Route('ue/{id}', name: 'app_u_e_admin_show', methods: ['GET'])]
    public function show(UE $uE, UserRepository $userRepository): Response
    {
        $enrolledIds = array_map(
            fn($ins) => $ins->getUserId()->getId(),
            $uE->getInscriptions()->toArray()
        );
        $availableStudents = $userRepository->createQueryBuilder('u')
            ->andWhere('JSON_CONTAINS(u.roles, :role) = 1 OR JSON_CONTAINS(u.roles, :teacherRole) = 1')
            ->setParameter('role', '"ROLE_STUDENT"')
            ->setParameter('teacherRole', '"ROLE_TEACHER"')
            ->andWhere('u.id NOT IN (:ids)')
            ->setParameter('ids', $enrolledIds ?: [0])
            ->getQuery()
            ->getResult();

        return $this->render('ue_admin/show.html.twig', [
            'u_e' => $uE,
            'availableStudents' => $availableStudents,
        ]);
    }

    #[Route('ue/{id}/edit', name: 'app_u_e_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UE $uE, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UEType::class, $uE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            //return $this->redirectToRoute('app_u_e_admin_index', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                'success' => true,
                'message' => 'UE updated successfully']);
        }

        /*return $this->render('ue_admin/edit.html.twig', [
            'u_e' => $uE,
            'form' => $form,
        ]);*/
        return $this->json([
            'form' => $this->renderView('ue_admin/_form.html.twig', [
                'u_e' => $uE,
                'form' => $form->createView()
            ])
        ]);
    }
    /*
    *   In here, after deleting use the Ajax request to reload the page    
    *   Maybe add Flash message to inform the user that the deletion was successful
    */
    #[Route('ue/{id}', name: 'app_u_e_admin_delete', methods: ['POST'])]
    public function delete(Request $request, UE $uE, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uE->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($uE);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_dashboard', []);
    }
}
