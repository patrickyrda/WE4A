<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\File;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\UERepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UERepository $ueRepository, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $ue_id = $request->query->get('ue_id');
        if (!$ue_id) {
            return $this->json(['error' => 'Missing UE id'], Response::HTTP_BAD_REQUEST);
        }

        $ue = $ueRepository->find($ue_id);
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

                try {
                    $uploadedFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $this->json(['error' => 'Upload failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $file = new File();
                $file->setFilePath($newFilename);
                $post->addFile($file);
                $entityManager->persist($file);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->json(['success' => true, 'message' => 'Post created successfully']);
        }

        return $this->json([
            'form' => $this->renderView('post/_form.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ])
        ]);
    }

    #[Route('/{id}/show', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('file_path')->getData();

            if ($uploadedFile) {
                // Remove old files
                foreach ($post->getFiles() as $existingFile) {
                    $oldFilePath = $this->getParameter('uploads_directory').'/'.$existingFile->getFilePath();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }

                    $post->removeFile($existingFile);
                    $entityManager->remove($existingFile);
                }

                $filename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($filename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                try {
                    $uploadedFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return $this->json(['error' => 'Upload failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                $file = new File();
                $file->setFilePath($newFilename);
                $post->addFile($file);
                $entityManager->persist($file);
            }

            $entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Post updated successfully'
            ]);
        }

        return $this->json([
            'form' => $this->renderView('post/_form.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ])
        ]);
    }

    #[Route('/{id}/delete', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete' . $post->getId(), $token)) {
            return $this->json(['error' => 'Invalid CSRF token.'], Response::HTTP_FORBIDDEN);
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Post deleted successfully']);
    }
}
