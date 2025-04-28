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
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Entity\UE;
use App\Entity\User;
use App\Entity\Inscriptions;
use App\Repository\PostRepository;
use App\Repository\UERepository;
use App\Repository\UserRepository;

final class UEDashboardController extends AbstractController{
    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        UERepository $ueRepository,
        UserRepository $userRepository,
        PostRepository $postRepo
    ): Response|RedirectResponse {
        $utilisateur = $this->getUser();
        $limit = 5;
        $inscriptions = $utilisateur->getInscriptions();
        $ues = $inscriptions
            ->map(fn($insc) => $insc->getUeId())
            ->toArray();
        $totalPosts = $postRepo->count([
            'ue_id' => $ues
        ]);
        $totalPages = max(1, (int) ceil($totalPosts / $limit));
        $page = $request->query->getInt('page', 1);
        if ($page < 1) {
            return $this->redirectToRoute('app_user_dashboard', ['page' => 1]);
        }
        if ($page > $totalPages) {
            return $this->redirectToRoute('app_user_dashboard', ['page' => $totalPages]);
        }
        $offset = ($page - 1) * $limit;
        $recentPosts = $postRepo->findBy(
            ['ue_id' => $ues],
            ['date'   => 'DESC'],
            $limit,
            $offset
        );
        $etudiants = array_filter(
            $userRepository->findAll(),
            fn($u) => in_array('ROLE_STUDENT', $u->getRoles())
        );
        $template = $request->isXmlHttpRequest()
        ? 'ue_dashboard/_posts.html.twig'
        : 'ue_dashboard/index.html.twig';
        return $this->render('ue_dashboard/index.html.twig', [
            'ues' => $ues,
            'etudiants' => $etudiants,
            'recent_posts' => $recentPosts,
            'current_page' => $page,
            'total_pages' => $totalPages,
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
    *   When sending a request to this endpoint, send the ue_id in a GET parameter. This ue_id should be stored somewhere so it can be used later in the Post creation/modification. TODO:
    *   After retrieving the data use AJAX to load the posts and create a new route and template or 
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

    #[Route('/user/api/add_student', name: 'user_api_add_student', methods: ['POST'])]
    public function ajouterEtudiant(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $ueId = $data['ue_id'] ?? null;
            $studentId = $data['student_id'] ?? null;
        } catch (\JsonException $e) {
            return new JsonResponse(['success'=>false,'message'=>'JSON invalide'], 400);
        }

        if (!$ueId || !$studentId) {
            return new JsonResponse(['success'=>false,'message'=>'Paramètres manquants'], 400);
        }

        $ue = $em->getRepository(UE::class)->find($ueId);
        $etud = $em->getRepository(User::class)->find($studentId);
        if (!$ue || !$etud) {
            return new JsonResponse(['success'=>false,'message'=>'UE ou étudiant introuvable'], 404);
        }

        try {
            $insc = new Inscriptions();
            $insc->setUeId($ue)->setUserId($etud);
            $em->persist($insc);
            $em->flush();
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l’enregistrement : '.$e->getMessage()
            ], 500);
        }
            return new JsonResponse(['success'=>true,'message'=>'Étudiant ajouté'], 200);
    }


    #[Route('/user/api/get_news', name: 'user_api_get_news')]
    public function getNews(EntityManagerInterface $entityManager, JsonResponseService $jsonResponse) : Response 
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $conn = $entityManager->getConnection();

        $query = 'SELECT u.code ,p.message, p.date FROM post p 
        INNER JOIN ue u ON p.ue_id_id = u.id 
        INNER JOIN inscriptions i ON i.ue_id_id = u.id
        WHERE i.user_id_id = :user_id
        ORDER BY p.date DESC LIMIT 15';

        $stmt = $conn->prepare($query);
        $result = $stmt->executeQuery(['user_id' => $user->getId()]);

        $newposts = $result->fetchAllAssociative();

        return $jsonResponse->success($newposts, 'Fetched most recent posts successfully');
    }

    #[Route('/user/api/get_participants', name: 'user_api_get_participants')]
    public function getParticipants(EntityManagerInterface $entityManager, JsonResponseService $jsonResponse, Request $request) : Response 
    {   
        $ue_id = $request->query->get('ue_id');
        if (!$ue_id) {
            return $jsonResponse->error("UE id not provided");
        }

        $conn = $entityManager->getConnection();
        $query = 'SELECT u.name, u.surname, u.email FROM user u INNER JOIN inscriptions i ON u.id = i.user_id_id WHERE i.ue_id_id = :ue_id;';
        $stmt = $conn->prepare($query);
        $result = $stmt->executeQuery(['ue_id' => $ue_id]);
        $participants = $result->fetchAllAssociative();

        if (!$participants) {
            return $jsonResponse->success([], 'UE does not have participants yet');
        }

        return $jsonResponse->success($participants, 'Fetched participants successfully');
    }
    #[Route('/user/api/ue_participants', name: 'app_user_api_ue_participants', methods: ['GET'])]
    public function fetchParticipants(Request $request, UERepository $ueRepository): JsonResponse
    {
        $ueId = $request->query->getInt('ue_id');
        if (!$ueId) {
            return $this->json(['error' => 'ue_id manquant'], 400);
        }

        $ue = $ueRepository->find($ueId);
        if (!$ue) {
            return $this->json(['error' => 'UE introuvable'], 404);
        }

        $data = [];
        foreach ($ue->getInscriptions() as $inscription) {
            $user = $inscription->getUserId();
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'roles' => $user->getRoles(),
            ];
        }

        return $this->json($data);
    }
    #[Route('/ue/show/{id}', name: 'app_u_e_dashboard_show')]
    public function show(UE $ue, UserRepository $userRepository, PostRepository $postRepo): Response
    {
        $posts = $postRepo->findBy(
            ['ue_id' => $ue],
            ['date'  => 'DESC']
        );
        $etudiants = array_filter(
            $userRepository->findAll(),
            fn(User $u) => in_array('ROLE_STUDENT', $u->getRoles(), true)
        );
        return $this->render('ue_dashboard/show.html.twig', [
            'u_e'     => $ue,
            'posts'   => $posts,
            'etudiants' => $etudiants,
        ]);
    }
}