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

#[Route('/user/admin')]
final class UserAdminController extends AbstractController{
    #[Route(name: 'app_user_admin_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {   
        $html = $this->renderView('user_admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);

        // Return plain HTML instead of JSON
        return new Response($html);
    }
    //HERE HAVE TO FIX TO ADD THE HASHED PASSWORD!
    #[Route('/new', name: 'app_user_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $entityManager->persist($user);
            $entityManager->flush();
            /*return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);*/
            //TODO: Answer to Ajax request, when receiving succes you need to reload the window
            return $this->json([
                'success' => true,
                'message' => 'User created successfully']);     
        }

        
        /*{return $this->render('user_admin/new.html.twig', [
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

    #[Route('/{id}', name: 'app_user_admin_show', methods: ['GET'])]
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

    #[Route('/{id}/edit', name: 'app_user_admin_edit', methods: ['GET', 'POST'])]
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
            'form' => $this->renderView('user_admin/_edit_modal.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                ])
            ]);
    }

    /*
    *   In here, after deleting use the Ajax request to reload the page    
    *   Maybe add Flash message to inform the user that the deletion was successful
    */

    #[Route('/{id}', name: 'app_user_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }


        return $this->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
        /*return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);*/
    }
}
