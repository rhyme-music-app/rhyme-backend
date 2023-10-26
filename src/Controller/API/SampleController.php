<?php

namespace App\Controller\API;

use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sample', name: 'sample_')]
class SampleController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return new Response('
        <!DOCTYPE html>
        <html>
            <head>
                <title>SampleController::index()</title>
            </head>
            <body>
                <h1>Welcome to our first controller !</h1>
                <p>Controller Path: <code>src/Controller/API/SampleController.php</code></p>
                <p>Secret: <code>' . $_ENV['APP_SECRET']    . '</code></p>
                <p>dbname: <code>' . $_ENV['DATABASE_NAME'] . '</code></p>
                <p>dbhost: <code>' . $_ENV['DATABASE_HOST'] . '</code></p>
                <p>dbport: <code>' . $_ENV['DATABASE_PORT'] . '</code></p>
                <p>dbuser: <code>' . $_ENV['DATABASE_USER'] . '</code></p>
                <p>dbpass: <code>' . $_ENV['DATABASE_PASS'] . '</code></p>
                <p><b>NOTE: To test database connection, go to <a href="/api/sample/db">/api/sample/db</a>.</b></p>
            </body>
        </html>
        ');
    }

    #[Route('/db', name: 'db')]
    public function db(): JsonResponse
    {
        $dbname = $_ENV['DATABASE_NAME'];
        $dbhost = $_ENV['DATABASE_HOST'];
        $dbport = $_ENV['DATABASE_PORT'];
        $dbuser = $_ENV['DATABASE_USER'];
        $dbpass = $_ENV['DATABASE_PASS'];

        $connection = new PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $dbuser, $dbpass);
        $connection = null;
        return $this->json([
            'message' => 'Successfully established a connection to the database !!!!!!!'
        ]);
    }
}
