<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}/projects', name: 'user_projects', methods: ['GET'])]
    public function getUserProjects(User $user): JsonResponse
    {
        // Récupérer les projets associés à l'utilisateur
        $projects = $user->getProjects();

        // Construire la réponse sous forme de tableau JSON
        $data = [];

        foreach ($projects as $project) {
            // Récupérer les informations de l'owner (utilisateur du projet)
            $owner = $project->getUser();

            // Récupérer les membres du projet
            $members = $project->getMembers();

            // Ajouter les projets avec les données de l'owner et des membres
            $data[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'status' => $project->getStatus(),
                'createdAt' => $project->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $project->getUpdatedAt()->format('Y-m-d H:i:s'),
                'owner' => [
                    'id' => $owner->getId(),
                    'username' => $owner->getUsername(),
                ],
                'members' => array_map(function ($member) {
                    return [
                        'id' => $member->getId(),
                        'username' => $member->getUsername(),
                    ];
                }, $members->toArray())
            ];
        }

        // Retourner la liste des projets au format JSON
        return $this->json($data);
    }
}

