<?php

namespace App\Controller\Web;

use App\Middleware\WebAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[Route('/', name: 'home_')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // https://commonmark.thephpleague.com/2.4/extensions/heading-permalinks/
        $env = new Environment([
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'symbol' => '#',
                'insert' => 'after',
                'apply_id_to_heading' => true,
            ]
        ]);
        $env->addExtension(new CommonMarkCoreExtension());
        $env->addExtension(new HeadingPermalinkExtension());
        $converter = new MarkdownConverter($env);

        $md = $converter->convert(file_get_contents(__DIR__ . '/../../../docs/API.md'));
        
        return $this->render('index.html.twig', [
            'markdownHtml' => $md,
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function loginPage(Request $request): Response
    {
        $error = null;
        $responseFixer = null;

        if ($request->getMethod() == 'POST') {
            try {
                $responseFixer = WebAuth::login($request);
                $response = new RedirectResponse('/dashboard');
                $responseFixer($response);
                return $response;
            } catch (HttpException $e) {
                $error = $e->getMessage();
            }
        }

        $response = $this->render('login.html.twig', [
            'user' => WebAuth::getTwigUserPayload($request),
            'error' => $error
        ]);
        if ($responseFixer !== null) {
            $responseFixer($response);
        }
        return $response;
    }

    #[Route('/dashboard', name: 'dashboard', methods: 'GET')]
    public function dashboardPage(Request $request): Response
    {
        return $this->render('dashboard.html.twig', [
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/logout', name: 'logout', methods: 'GET')]
    public function logoutPage(Request $request): Response
    {
        try {
            WebAuth::assertAndLogout($request);
            // in case of log out error due to
            // malformed/missing token:
            // JUST IGNORE !
        } catch (BadRequestHttpException $e) {
            // ignore
        } catch (UnauthorizedHttpException $e) {
            // ignore
        }

        return new RedirectResponse('/');
    }
}
