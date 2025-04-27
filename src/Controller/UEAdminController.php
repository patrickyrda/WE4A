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

final class UEAdminController extends AbstractController{
    #[Route('/ue/admin',name: 'app_u_e_admin_index', methods: ['GET'])]
    public function index(UERepository $ueRepository, Request $request): Response
    {
        /*$ues = $uERepository->createQueryBuilder('u')
            ->select('u.id, u.code, u.title, u.image_path')
            ->getQuery()
            ->getResult();
                
        $content = [
            'data' => $ues
        ];

        $data = $serializer->serialize($content, 'json'); 

        return JsonResponse::fromJsonString($data);*/
        /*return $this->render('ue_admin/index.html.twig', [
            'u_es' => $uERepository->findAll(),
        ]);*/

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
    #[Route('/new', name: 'app_u_e_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uE = new UE();
        $form = $this->createForm(UEType::class, $uE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($uE);
            $entityManager->flush();
            
             //TODO: Answer to Ajax request, when receiving succes you need to reload the window
            return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                'success' => true,
                'message' => 'UE created successfully']);
        }

        return $this->render('ue_admin/new.html.twig', [
            'u_e' => $uE,
            'form' => $form,
        ]);
        return $this->json([
            'form' => $this->renderView('ue_admin/_form.html.twig', [
                'u_e' => $uE,
                'form' => $form->createView()
            ])
        ]);
    }

    #[Route('/{id}', name: 'app_u_e_admin_show', methods: ['GET'])]
    public function show(UE $uE): Response
    {
        return $this->render('ue_admin/show.html.twig', [
            'u_e' => $uE,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_u_e_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UE $uE, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UEType::class, $uE);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_admin_dashboard');
        }
        return $this->render('ue_admin/edit.html.twig', [
            'u_e' => $uE,
            'form' => $form->createView(),
        ]);
        return $this->render('ue_admin/edit.html.twig', [
            'ue'   => $ue,
            'form' => $form->createView(),
        ]);
    }
    /*
    *   In here, after deleting use the Ajax request to reload the page    
    *   Maybe add Flash message to inform the user that the deletion was successful
    */
    #[Route('/{id}', name: 'app_u_e_admin_delete', methods: ['POST'])]
    public function delete(Request $request, UE $uE, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$uE->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($uE);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_dashboard', []);
    }
}
