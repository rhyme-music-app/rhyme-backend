<?php

namespace App\Controller\Web;

use App\Middleware\WebAuth;
use App\Utils\DatabaseResetter;
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

    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboardPage(Request $request): Response
    {
        return $this->render('dashboard.html.twig', [
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/songs', name: 'manage_songs', methods: ['GET'])]
    public function manageSongsPage(Request $request): Response
    {
        return $this->render('manage-songs.html.twig', [
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/songs/editor', name: 'edit_song', methods: ['GET'])]
    public function editSongPage(Request $request): Response
    {
        return $this->render('edit-song.html.twig', [
            'id' => $request->query->get('id'),
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/songs/add-genre', name: 'add_genre_to_song', methods: ['GET'])]
    public function addGenreToSongPage(Request $request): Response
    {
        return $this->render('add-genre-to-song.html.twig', [
            'songId' => $request->query->get('songId'),
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/songs/add-artist', name: 'add_artist_to_song', methods: ['GET'])]
    public function addArtistToSongPage(Request $request): Response
    {
        return $this->render('add-artist-to-song.html.twig', [
            'songId' => $request->query->get('songId'),
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/genres', name: 'manage_genres', methods: ['GET'])]
    public function manageGenresPage(Request $request): Response
    {
        return $this->render('manage-genres.html.twig', [
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/genres/editor', name: 'edit_genre', methods: ['GET'])]
    public function editGenrePage(Request $request): Response
    {
        return $this->render('edit-genre.html.twig', [
            'id' => $request->query->get('id'),
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/artists', name: 'manage_artists', methods: ['GET'])]
    public function manageArtistsPage(Request $request): Response
    {
        return $this->render('manage-artists.html.twig', [
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/manage/artists/editor', name: 'edit_artist', methods: ['GET'])]
    public function editArtistPage(Request $request): Response
    {
        return $this->render('edit-artist.html.twig', [
            'id' => $request->query->get('id'),
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
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

    #[Route('/signup', name: 'signup', methods: ['GET'])]
    public function signupPage(Request $request): Response
    {
        return $this->render('signup.html.twig', [
            'user' => WebAuth::getTwigUserPayload($request)
        ]);
    }

    #[Route('/reset-database', name: 'reset_database', methods: ['GET'])]
    public function resetDatabase(Request $request): Response
    {
        $user = WebAuth::assertAdmin($request);
        DatabaseResetter::resetDatabase();
        return new RedirectResponse('/dashboard');
    }
}
