<?php
namespace App\Controller\Api;

use App\Repository\UeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/ue', name: 'api_ue_')]
class UEApiController extends AbstractController
{
    // Renvoie la liste des ue de l'utilisateur, avec les étudiants inscrits
    #[Route('', name: 'liste', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function liste(UeRepository $ueRepository): JsonResponse
    {
        // On récupère l'utilisateur connecté et les ue lui concernant
        $utilisateur = $this->getUser();
        $ues = $ueRepository->findBy(['owner' => $utilisateur]);
        // Tableau de donnéees à renvoyer
        $donnees = array_map(fn($ue) => [
            'id' => $ue->getId(),
            'nom' => $ue->getName(),
            'etudiants' => array_map(
                fn($e) => ['id' => $e->getId(), 'nom' => $e->getFullName()],
                $ue->getStudents()->toArray()
            )
        ], $ues);

        return $this->json($donnees);
    }

    // Permet l'ajout d'un étudiant à une ue specifique
    #[Route('/{id}/ajouter-etudiant', name: 'ajouter_etudiant', methods: ['POST'])]
    public function ajouterEtudiant(
        int $id,
        Request $requete,
        UeRepository $ueRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        // Recupere l'id de l'etudiant depuis les données POST
        $IDetudiant = $requete->request->get('IDetudiant');
        if (!$IDetudiant) {
            return $this->json(['erreur' => 'IDetudiant manquant'], 400);
        }
        
        // Recherche ue et étudiant 
        $ue = $ueRepository->find($id);
        $etudiant = $userRepository->find($IDetudiant);

        if (!$ue || !$etudiant) {
            return $this->json(['erreur' => 'UE ou étudiant introuvable'], 404);
        }

        // Ajoute l'etudiant
        $ue->addStudent($etudiant);
        $em->flush();

        // Construit la reponse avec la liste actualisée des étudiants
        $etudiants = array_map(
            fn($e) => ['id' => $e->getId(), 'nom' => $e->getFullName()],
            $ue->getStudents()->toArray()
        );

        return $this->json(['id' => $ue->getId(), 'etudiants' => $etudiants]);
    }

    // Supprime un étudiant d'une ue
    #[Route('{id}/supprimer-etudiant', name: 'supprimer_etudiant', methods: ['POST'])]
    public function supprimerEtudiant(
        int $id,
        Request $requete,
        UeRepository $ueRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        // On recupere l'id de l'etudiant depuis les données POST
        $IDetudiant = $requete->request->get('IDetudiant');
        if (!$IDetudiant) {
            return $this->json(['erreur' => 'IDetudiant manquant'], 400);
        }

        // Recherche ue et étudiant
        $ue = $ueRepository->find($id);
        $etudiant = $userRepository->find($IDetudiant);

        if (!$ue || !$etudiant) {
            return $this->json(['erreur' => 'UE ou étudiant introuvable'], 404);
        }

        // Supprime l'etudiant
        $ue->removeStudent($etudiant);
        $em->flush();

        // Construit la reponse avec la liste actualisée des étudiants
        $etudiants = array_map(
            fn($e) => ['id' => $e->getId(), 'nom' => $e->getFullName()],
            $ue->getStudents()->toArray()
        );

        return $this->json(['id' => $ue->getId(), 'etudiants' => $etudiants]);
    }
}   