<?php
namespace App\Controller;

use App\Entity\UE;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UEController extends AbstractController
{
    #[Route('/ue/{id}', name: 'ue_show', methods: ['GET'])]
    public function show(UE $ue): Response
    {
        return $this->render('ue/show.html.twig', [
            'ue' => $ue,
        ]);
    }
}