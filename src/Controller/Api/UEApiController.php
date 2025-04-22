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
    #[Route('', name: 'liste', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function liste(UeRepository $ueRepository): JsonResponse
    {
        $utilisateur = $this->getUser();
        $ues = $ueRepository->findBy(['owner' => $utilisateur]);

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

    #[Route('/{id}/ajouter-etudiant', name: 'ajouter_etudiant', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function ajouterEtudiant(
        int $id,
        Request $requete,
        UeRepository $ueRepository,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        $IDetudiant = $requete->request->get('IDetudiant');

        if (!$IDetudiant) {
            return $this->json(['erreur' => 'IDetudiant manquant'], 400);
        }

        $ue = $ueRepository->find($id);
        $etudiant = $userRepository->find($IDetudiant);

        if (!$ue || !$etudiant) {
            return $this->json(['erreur' => 'UE ou étudiant introuvable'], 404);
        }

        $ue->addStudent($etudiant);
        $em->flush();

        $etudiants = array_map(
            fn($e) => ['id' => $e->getId(), 'nom' => $e->getFullName()],
            $ue->getStudents()->toArray()
        );

        return $this->json(['id' => $ue->getId(), 'etudiants' => $etudiants]);
    }
}