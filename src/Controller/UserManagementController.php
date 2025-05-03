<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;
use App\Form\UserManagementType;


final class UserManagementController extends AbstractController{
    #[Route('/user/management/{id}', name: 'app_user_management')]
    public function index(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {   
        $form = $this->createForm(UserManagementType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            } 
           
            $entityManager->flush();

            return $this->redirectToRoute('app_user_management', [], Response::HTTP_SEE_OTHER);
            
        }
        
        return $this->render('user_management/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
