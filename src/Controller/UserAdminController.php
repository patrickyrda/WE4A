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

#[Route('/user/admin')]
final class UserAdminController extends AbstractController{
    #[Route(name: 'app_user_admin_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, SerializerInterface $serializer, Request $request): Response
    {   
        //here have to finish the request part plus pagination
        /*$users = $userRepository->createQueryBuilder('u')
            ->select('u.id, u.name, u.surname, u.email, u.roles')
            ->getQuery()
            ->getResult();
                
        $content = [                                    !!!!ONLY IMPLEMENT THAT IF WE WANT PAGINATION, BUT IT MIGHT NOT BE THE CASE
            'data' => $users
        ];

        $data = $serializer->serialize($content, 'json'); 

        return JsonResponse::fromJsonString($data);*/

        return $this->render('user_admin/index.html.twig', [   //THIS ONE IS EQUALY FUNCTIONAL
            'users' => $userRepository->findAll(),
        ]);
    }
    //HERE HAVE TO FIX TO ADD THE HASHED PASSWORD!
    #[Route('/new', name: 'app_user_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public function show(User $user): Response
    {
        return $this->render('user_admin/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            /*return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);*/
            return $this->json([
                'success' => true,
                'message' => 'User updated successfully']);
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
