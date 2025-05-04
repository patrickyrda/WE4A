<?php
namespace App\Controller;

use App\Entity\UE;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Controleur gestion de l'affichage des details d'une ue

class UEController extends AbstractController
{
    #[Route('/ue/{id}', name: 'ue_show', methods: ['GET'])]
    public function show(UE $ue): Response
    {
        // Rend le template en fournissant l'ue à la vie
        return $this->render('ue/show.html.twig', [
            'ue' => $ue,
        ]);
    }
}