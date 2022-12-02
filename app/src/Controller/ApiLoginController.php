<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'app_api_login')]
    public function index(): Response
    {
        if (null === $user) {
                    return $this->json([
                            'message' => 'missing credentials',
                   ], Response::HTTP_UNAUTHORIZED);
                }
                $token = ""; // somehow create an API token for $user
                return $this->json([
                    'user'  => $user->getUserIdentifier(),
                    'token' => $token,
                ]);
    }
   
}
