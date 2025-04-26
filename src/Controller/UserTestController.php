<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserTestController extends AbstractController
{
    #[Route('/test/user/{id}', name: 'test_user_projects')]
    public function testUserProjects(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findUserWithProjects($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'projects' => array_map(function ($project) {
                return [
                    'id' => $project->getId(),
                    'name' => $project->getName(),
                    'status' => $project->getStatus()
                ];
            }, $user->getProjects()->toArray()),
        ]);
    }
}