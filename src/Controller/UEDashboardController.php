<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\JsonResponseService;

use App\Entity\UEs;
use App\Entity\User;
use App\Entity\Inscriptions;
use App\Repository\PostRepository;

final class UEDashboardController extends AbstractController{
    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    public function index(): Response
    {
        return $this->render('ue_dashboard/index.html.twig', [
            'controller_name' => 'UEDashboardController',
        ]);
    }

  
    /*
    *   User has to be logged in for the Api to return something
    *   In the js or html need to add a data-attribute with the ue_id that will be retrieved and then sent to the api/ueposts via GET 
    */
    #[Route('/user/api/fetch_ues', name: 'app_user_api_fetch_ues')]
    public function fetch_ues(Request $request, EntityManagerInterface $entityManager, JsonResponseService $jsonResponse): Response 
    {
        $user = $this->getUser();
        
        if (!$user) {
            //return $this->json(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
            return $this->redirectToRoute('app_login');
        }

        $inscriptions = $user->getInscriptions();

        $data = $inscriptions->map(function (Inscriptions $inscription) {
            return [
                'id' => $inscription->getId(),
                'code' => $inscription->getUeId()->getCode(),
                'title' => $inscription->getUeId()->getTitle(),
                'image_path' => $inscription->getUeId()->getImagePath()
            ];
        })->toArray();
        
        return $jsonResponse->success($data, 'Fetched UEs successfully');

    }

    #[Route('/user/api/ueposts', name: 'app_ue_posts', methods: ['GET'])]
    /*
    *   When sending a request to this endpoint, send the ue_id in a GET parameter. After retrieving the data use AJAX to load the posts and create a new route and template or 
    *   use this route to automatically load  the posts in a page. In this case USE THE COMMENTED CODE and delete everything below
    */
    public function ueposts(PostRepository $postRepository, Request $request, JsonResponseService $jsonResponse): Response
    {

        $ue_id = $request->query->get('ue_id');
        if (!$ue_id) {
            return $jsonResponse->error("UE id not provided");
        }

        $posts = $postRepository->findBy(['ue_id' => $ue_id]);

        /* This code in case wanting to load the posts in a new page, not using AJAX
        return $this->render("desired/route/of/the/template.html.twig", [
            'posts' => $posts,
        ]);*/

        if (!$posts) {
            return $jsonResponse->success([], 'UE does not have posts yet');
        }

        $data = array_map(function ($post) 
        {
            return [
                'id' => $post->getId(),
                'user_name' => $post->getUserId()->getName(),
                'message' => $post->getMessage(),
                'date' => $post->getDate()->format('Y-m-d H:i:s'),
                'files' => $post->getFiles()->map(function ($file) {
                    return [
                        'id' => $file->getId(),
                        'path' => $file->getFilePath(),
                    ];
                })->toArray()
            ];
        }, $posts);

        return $jsonResponse->success($data, 'Fetched UEs posts successfully');
    }

    #[Route("/user/api/ue/add", name: "app_add_ue", methods: ["POST"])]
    public function addUe(Request $request, EntityManagerInterface $em): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(["error" => "Accès interdit"], 401);
        }

        $ueId = $request->request->get("ue_id");
        if (!$ueId) {
            return new JsonResponse(["error" => "ID de l\'UE invalide"], 400);
        }

        $ue = $em->getRepository(UE::class)->find($ueId);
        if (!$ue) {
            return new JsonResponse(["error" => "L\'UE n\'existe pas"], 404);
        }
        
        foreach ($user->getInscriptions() as $inscription) {
            if ($inscription->getUeId()->getId() === $ueId) {
                return new JsonResponse(["message" => "Déjà inscrit"], 200);
            }
        }

        $inscription = new Inscriptions();
        $inscription->setUserId($user);
        $inscription->setUeId($ue);

        $em->persist($inscription);
        $em->flush();

        return new JsonResponse(["success" => true, "message" => "UE ajouté"]);
    }

    #[Route("/user/api/my_ues", name: "app_my_ues", methods: ["GET"])]
    public function myUes(): JsonResponse {
        $user = $this->getUser();
        $ues = [];

        foreach ($user->getInscriptions() as $inscription) {
            $ues[] = [
                "id" => $inscription->getUeId()->getId(),
                "title" => $inscription->getUeId()->getTitle(),
            ];
        }

        return new JsonResponse($ues);
    }
}
