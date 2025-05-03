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
/**
 * 
 *  This is the controller for the user dashboard, the "UEs choice page". It has all of the API endpoints for the user dashboard.
 * TODO: Delete /user/api/ue_participants, Differenciate Students and Teachers in the UEs participants list [/ue/show/{id}, and get_participants]
 * 
 * 
 */
final class UEDashboardController extends AbstractController{
    #[Route('/user/dashboard', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        UERepository $ueRepository,
        UserRepository $userRepository,
        PostRepository $postRepo
    ): Response|RedirectResponse {
        // On recupere l'utilisateur connecté ou on le redirige vers la page de login
        $utilisateur = $this->getUser();
        if (!$utilisateur) {
            return $this->redirectToRoute('app_login');
        }
        // On defini la limite de posts par page et calcul le nombre total de pages
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
        // On ajuste la page si elle est en dehors des limites
        if ($page < 1) {
            return $this->redirectToRoute('app_user_dashboard', ['page' => 1]);
        }
        if ($page > $totalPages) {
            return $this->redirectToRoute('app_user_dashboard', ['page' => $totalPages]);
        }
        // On recupere les posts recents
        $offset = ($page - 1) * $limit;
        $recentPosts = $postRepo->findBy(
            ['ue_id' => $ues],
            ['date'   => 'DESC'],
            $limit,
            $offset
        );
        // On retient seulement les étudiants
        $etudiants = array_filter(
            $userRepository->findAll(),
            fn($u) => in_array('ROLE_STUDENT', $u->getRoles())
        );
        // On selectionne le template en fonction de la requete
        $template = $request->isXmlHttpRequest()
        ? 'ue_dashboard/_posts.html.twig'
        : 'ue_dashboard/index.html.twig';
        // On renvoie la page avec les données ajoutées
        return $this->render('ue_dashboard/index.html.twig', [
            'ues' => $ues,
            'etudiants' => $etudiants,
            'recent_posts' => $recentPosts,
            'current_page' => $page,
            'total_pages' => $totalPages,
        ]);
    }
    /*  
    *   This is the API responsible for fetching the UEs that the user is enrolled in.
    *   It returns the UEs data in JSON format, so that they can be used in the frontend.
    *   User has to be logged in for the Api to return something, otherwise it redirects to the login page.
    *   This API end-point is not used in the current version of the website
    */
    #[Route('/user/api/fetch_ues', name: 'app_user_api_fetch_ues')]
    public function fetch_ues(Request $request, EntityManagerInterface $entityManager, JsonResponseService $jsonResponse): Response 
    {
        // On recupere l'utilisateur connecté ou on le redirige vers la page de login
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // On recupere où l'utilisateur est inscrit
        $inscriptions = $user->getInscriptions();
        // On verifie si l'utilisateur n'est pas inscrit dans l'ue
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

    /*  
    *   This is the API responsible for fetching the posts of a specific UE.
    *   It returns the posts data in JSON format, so that they can be used in the frontend.
    *   When sending a request to this endpoint, send the ue_id in a GET parameter. 
    *   This endpoint uses our custom JsonResponseService to return the data in a consistent format.
    */
    #[Route('/user/api/ueposts', name: 'app_ue_posts', methods: ['GET'])]
    public function ueposts(PostRepository $postRepository, Request $request, JsonResponseService $jsonResponse): Response
    {
        // On recupere l'id de l'ue
        $ue_id = $request->query->get('ue_id');
        if (!$ue_id) {
            return $jsonResponse->error("UE id not provided");
        }
        // On recupere les posts de l'ue
        $posts = $postRepository->findBy(['ue_id' => $ue_id]);
        if (!$posts) {
            return $jsonResponse->success([], 'UE does not have posts yet');
        }
        // On convertit les posts en tableau
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

    /**
     * 
     *  This is the API responsible for adding a student to an UE.
     *  It expects a JSON payload with the ue_id and student_id.
     *  After adding the student, it returns a JSON response with the success status and a message.
     * 
     */
    #[Route('/user/api/add_student', name: 'user_api_add_student', methods: ['POST'])]
    public function ajouterEtudiant(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // On lit le contenu JSON de la requete
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $ueId = $data['ue_id'] ?? null;
            $studentId = $data['student_id'] ?? null;
        } catch (\JsonException $e) {
            return new JsonResponse(['success'=>false,'message'=>'JSON invalide'], 400);
        }
        // On verifie si les parametres sont presents
        if (!$ueId || !$studentId) {
            return new JsonResponse(['success'=>false,'message'=>'Paramètres manquants'], 400);
        }
        // On recupere l'ue et l'étudiant
        $ue = $em->getRepository(UE::class)->find($ueId);
        $etud = $em->getRepository(User::class)->find($studentId);
        if (!$ue || !$etud) {
            return new JsonResponse(['success'=>false,'message'=>'UE ou étudiant introuvable'], 404);
        }
        // On fait l'ajout de l'étudiant à l'ue
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
    /**
     * 
     *  This is the API responsible for removing a student from an UE.
     *  It expects a JSON payload with the ue_id and student_id.
     *  After adding the student, it returns a JSON response with the success status and a message.
     * 
     */
    #[Route('/user/api/supprimer-etudiant', name: 'supprimer_etudiant', methods: ['POST'])]
    public function supprimerEtudiant(Request $request,UeRepository $ueRepository,UserRepository $userRepository,EntityManagerInterface $em): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }
        
        $ue_id = $data['ue_id'] ?? null;
        $user_id = $data['user_id'] ?? null;
        if (!$ue_id || !$user_id) {
            return $this->json(['erreur' => 'ue_id ou user_id manquant'], 400);
        }

        $ue = $ueRepository->find($ue_id);
        $etudiant = $userRepository->find($user_id);
        if (!$ue || !$etudiant) {
            return $this->json(['erreur' => 'UE ou étudiant introuvable'], 404);
        }

        $ue->removeStudent($etudiant);
        $em->flush();

        return new JsonResponse(['success'=>true,'message'=>'Étudiant removed'], 200);
    }

    /**
     * 
     *  This is the API responsible for fetching the 15 most recent posts of the user's assigned UEs.
     *  It returns the posts data in JSON format, so that they can be used in the frontend.
     *  This endpoint uses our custom JsonResponseService to return the data in a consistent format.
     *  User has to be logged in for the Api to return something, otherwise it redirects to the login page.
     *  It returns the data in a JSON format, so that it can be used in the frontend. It is retrieved by the frontend using AJAX.
     */
    #[Route('/user/api/get_news', name: 'user_api_get_news')]
    public function getNews(EntityManagerInterface $entityManager, JsonResponseService $jsonResponse) : Response 
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $conn = $entityManager->getConnection();

        //This is the code's biggest query, it fetches data from three tables, using all of the tables of the database.
        $query = 'SELECT f.file_path, us.name, us.surname, us.email, u.code, p.message, p.date
        FROM post p
        INNER JOIN ue u ON p.ue_id_id = u.id 
        INNER JOIN inscriptions i ON i.ue_id_id = u.id
        INNER JOIN user us ON us.id = i.user_id_id 
        LEFT JOIN file f ON f.post_id = p.id
        WHERE us.id = :user_id
        ORDER BY p.date DESC
        LIMIT 15;';

        $stmt = $conn->prepare($query);
        $result = $stmt->executeQuery(['user_id' => $user->getId()]);

        $newposts = $result->fetchAllAssociative();

        return $jsonResponse->success($newposts, 'Fetched most recent posts successfully');
    }
    /**
     * 
     *  This is the API responsible for fetching the participants of a specific UE.
     *  It returns the participants data in JSON format, so that they can be used in the frontend.
     *  When sending a request to this endpoint, send the ue_id in a GET parameter. 
     *  This endpoint uses our custom JsonResponseService to return the data in a consistent format.
     */
    #[Route('/user/api/get_participants', name: 'user_api_get_participants')]
    public function getParticipants(EntityManagerInterface $entityManager, JsonResponseService $jsonResponse, Request $request) : Response 
    {   
        // On recupere l'id de l'ue
        $ue_id = $request->query->get('ue_id');
        if (!$ue_id) {
            return $jsonResponse->error("UE id not provided");
        }
        // Execution d'une requete SQL pour recuperer les participants de l'ue
        $conn = $entityManager->getConnection();
        $query = 'SELECT u.name, u.surname, u.email FROM user u INNER JOIN inscriptions i ON u.id = i.user_id_id WHERE i.ue_id_id = :ue_id;';
        $stmt = $conn->prepare($query);
        $result = $stmt->executeQuery(['ue_id' => $ue_id]);
        $participants = $result->fetchAllAssociative();
        // On verifie si l'ue a des participants
        if (!$participants) {
            return $jsonResponse->success([], 'UE does not have participants yet');
        }

        return $jsonResponse->success($participants, 'Fetched participants successfully');
    }

    /**
     * 
     * Not used in the current version of the website. 
     */
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

    /**
     * 
     *  This is the route responsible for rendering the page of an UE. It sends data of the UE, its posts and the list of students that are enrolled in the UE.
     */
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