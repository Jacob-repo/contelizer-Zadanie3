<?php

namespace App\Controller;

use App\Service\GorestClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    #[Route('/', name: 'user_index')]
    public function index(Request $request, GorestClient $gorestClient): Response
    {
        $search = $request->query->get('name', '');
        $page = max(1, (int)$request->query->get('page', 1));
    
        [$users, $hasNext, $hasPrev] = $gorestClient->fetchUsers($search, $page);
    
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'search' => $search,
            'page' => $page,
            'hasNext' => $hasNext,
            'hasPrev' => $hasPrev,
        ]);
    }    

    #[Route('/user/{id}/edit', name: 'user_edit')]
    public function editUser(Request $request, int $id, GorestClient $client): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['name'], $data['email'], $data['status'])) {
            return new JsonResponse(['error' => 'Nieprawidłowe dane.'], 400);
        }

        $response = $client->updateUser($id, $data);

        if (isset($response['message'])) {
            return new JsonResponse(['error' => $response['message']], 400);
        }

        return new JsonResponse(['success' => true, 'user' => $response]);
    }

}
