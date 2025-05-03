<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserAdminController extends AbstractController{
    #[Route('/user/admin', name: 'app_user_admin_index')]
    public function index(UserRepository $userRepository, Request $request): Response
    {   
        $users = $userRepository->findAll();;
        if ($request->isXmlHttpRequest()) {
            $html = $this->renderView('user_admin/_table.html.twig', [
                'users' => $users,
            ]);
    
            return $this->json([
                'success' => true,
                'html'    => $html,
            ]);
        }
    
        return $this->render('user_admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('user/new', name: 'app_user_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (!$plainPassword) {
                $plainPassword = 'password120';
            }
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $entityManager->persist($user);
            $entityManager->flush();

            // Return a JSON response for AJAX requests
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => 'User created successfully',
                ]);
            }

            // Redirect for non-AJAX requests
            return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        // Render the form for GET requests or invalid submissions
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'form' => $this->renderView('user_admin/_form.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                ]),
            ]);
        }

        return $this->render('user_admin/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

#[Route('user/{id}', name: 'app_user_admin_show', methods: ['GET'])]
public function show(User $user, Request $request): Response
{
    if ($request->isXmlHttpRequest()) {
        return $this->json([
            'content' => $this->renderView('user_admin/show.html.twig', [
                'user' => $user,
            ])
        ]);
    }

    return $this->render('user_admin/show.html.twig', [
        'user' => $user,
    ]);
}

    #[Route('user/{id}/edit', name: 'app_user_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //TODO: Here have to check if a password was inserted, if not, don't change it, HAVE TO CHANGE THE FORM
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            } 
           
            $entityManager->flush();

            //return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        }

        /*return $this->render('user_admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);*/
        return $this->json([
            'form' => $this->renderView('user_admin/_form.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                ])
            ]);
    }

    /*
    *   In here, after deleting use the Ajax request to reload the page    
    *   Maybe add Flash message to inform the user that the deletion was successful
    */

    #[Route('user/{id}', name: 'app_user_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            // Return a JSON response for AJAX requests
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => 'User deleted successfully',
                ]);
            }

            // Redirect for non-AJAX requests
            return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        throw $this->createAccessDeniedException('Invalid CSRF token.');
    }
}
