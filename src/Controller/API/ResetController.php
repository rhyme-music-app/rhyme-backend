<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Utils\DatabaseResetter;
use App\Utils\Response\NormalizedJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
            return new NormalizedJsonResponse([
                'message' => 'Database reset successfully !'
            ], 200);
        }
        throw new UnauthorizedHttpException('None', 'Wrong APP_SECRET !');
    }
}
