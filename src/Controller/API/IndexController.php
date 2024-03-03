<?php

namespace App\Controller\API;

use App\Middleware\Auth;
use App\Utils\AlgoliaUtils;
use App\Utils\Response\NormalizedJsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/index', name: 'index_')]
class IndexController extends AbstractController
{
    #[Route('/songs', name: 'songs', methods: ['PATCH'])]
    public function indexSongs(Request $request): Response
    {
        Auth::assertAdmin($request);
        
        $reset = ($request->query->get('reset') === 'true');
        if ($reset) {
            AlgoliaUtils::deleteAllSongs();
        }
        AlgoliaUtils::saveAllSongs();
        return new NormalizedJsonResponse([
            'message' => ($reset ? 'Song index reset and regenerated successfully.' : 'Song index updated successfully.')
        ], 200);
    }
}
