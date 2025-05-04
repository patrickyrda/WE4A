<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/*
* This controller is responsible for rendering the admin dashboard page.
*/

use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ADMIN')]
final class AdminDashboardController extends AbstractController{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
        ]);
    }
}