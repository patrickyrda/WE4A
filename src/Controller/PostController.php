<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\File;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UERepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/post')]
final class PostController extends AbstractController{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {   
        
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }
    /*
    *   User has to be logged in for the Api to return something and the ue_id has to be sent in the GET request
    *
    */
    #[Route('/post/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UERepository $ueRepository, SluggerInterface $slugger): Response
    {   
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $ue_id = $request->query->get('ue_id');
        $ue  = $ueRepository->find($ue_id);
        if (!$ue) {
            throw $this->createNotFoundException('UE not found');
        }

        $post = new Post();
        $post->setUserId($user);
        $post->setUeId($ue);
        $post->setDate(new \DateTimeImmutable());

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file_path')->getData();

            if ($uploadedFile) {
                $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($filename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                try
                {
                    $uploadedFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception 
                }

                $file = new File();
                $file->setFilePath($newFilename);

                $post->addFile($file);
                $entityManager->persist($file);
            }

            
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_u_e_dashboard_show', ['id' => $ue->getId()]);
            return $this->json([
                'success' => true,
                'message' => 'Post created successfully'
            ]);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
        return $this->json([
            'form' => $this->renderView('post/_form.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ])
        ]);
    }

    #[Route('/post/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/post/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Post                   $post,
        EntityManagerInterface $em,
        UERepository           $ueRepository,
        SluggerInterface       $slugger
    ): Response {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $newMessage  = $form->get('message')->getData();
            $uploadedFile = $form->get('file_path')->getData();
            if ($uploadedFile) {
                foreach ($post->getFiles() as $old) {
                    $em->remove($old);
                }
                $post->setMessage(null);
                $origName    = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName    = $slugger->slug($origName);
                $newFilename = sprintf('%s-%s.%s',
                    $safeName,
                    uniqid(),
                    $uploadedFile->guessExtension()
                );
                $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads';
                try {
                    $uploadedFile->move($uploadDir, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Échec de l’upload du fichier.');
                }
                $fileEntity = new File();
                $fileEntity->setFilePath($newFilename);
                $post->addFile($fileEntity);
                $em->persist($fileEntity);
            }
            elseif ($newMessage !== null && trim($newMessage) !== '') {
                foreach ($post->getFiles() as $old) {
                    $em->remove($old);
                }
            }
            $em->flush();
            $this->addFlash('success', 'Post mis à jour avec succès.');
            return $this->redirectToRoute('app_u_e_dashboard_show', [
                'id' => $post->getUeId()->getId(),
            ]);
        }
        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
/*
    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
*/
#[Route('/post/{id}', name: 'app_post_delete', methods: ['POST'])]
public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
{
    $token = $request->request->get('_token');
    if ($this->isCsrfTokenValid('delete' . $post->getId(), $token)) {
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Post supprimé avec succès.'
        ]);
    }

    return $this->json([
        'success' => false,
        'message' => 'Token CSRF invalide.'
    ], Response::HTTP_BAD_REQUEST);
}

}