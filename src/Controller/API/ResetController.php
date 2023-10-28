<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Utils\DatabaseResetter;
use Symfony\Component\HttpFoundation\Request;

#[Route('/reset', name: 'reset_')]
class ResetController extends AbstractController
{
    // TODO: DDoS protection !!!
    #[Route('/database', name: 'database', methods: ['POST'])]
    public function resetDatabase(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (($data['APP_SECRET'] ?? '') === $_ENV['APP_SECRET']) {
            DatabaseResetter::resetDatabase();
            return $this->json([
                'success' => true,
                'message' => 'Database reset successfully !'
            ], 200);
        }
        return $this->json([
            'success' => false,
            'message' => 'Wrong secret !'
        ], 403);
    }
}
