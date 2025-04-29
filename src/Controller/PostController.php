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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
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

            //return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                
                'success' => true,
                'message' => 'Post created successfully'
            ]);
        }

        /*return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);*/
        return $this->json([
            'success' => true,
            'form' => $this->renderView('post/_form.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ])
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    //add logic to modify the file
    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $uploadedFile = $form->get('file_path')->getData();

            if ($uploadedFile) {
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
                    // Handle upload exception if needed
                }

                $file = new File();
                $file->setFilePath($newFilename);

                $post->addFile($file);
                $entityManager->persist($file);
            }

            $entityManager->flush();

            //return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
            return $this->json([
                'success' => true,
                'message' => 'Post updated successfully'
            ]);
        }

        /*return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);*/

        return $this->json([
            'success' => true,
            'form' => $this->renderView('post/_form.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ])
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $ueId = $post->getUeId()->getId();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_u_e_dashboard_show', ['id' => $ueId]);        //HAVE TO FIX HERE 
        /*return $this->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);*/
    }

    #[Route('/download/{filename}', name: 'post_download_file', methods: ['GET'])]
    public function downloadFile(string $filename): Response
    {
        $uploadsDir = $this->getParameter('uploads_directory');
        $filePath = $uploadsDir . '/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found.');
        }

        $mimeType = mime_content_type($filePath);
        $originalFilename = pathinfo($filename, PATHINFO_BASENAME);

        return $this->file($filePath, $originalFilename, ResponseHeaderBag::DISPOSITION_ATTACHMENT, [
            'Content-Type' => $mimeType,
        ]);
    }
}

