<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

#[Route('/', name: 'home_')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
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
        $md = '
        <html>
            <head>
                <style>
                    .heading-permalink {
                        color: transparent;
                        text-decoration: none;
                        padding-left: 5px;
                    }

                    h1:hover .heading-permalink,
                    h2:hover .heading-permalink,
                    h3:hover .heading-permalink,
                    h4:hover .heading-permalink,
                    h5:hover .heading-permalink,
                    h6:hover .heading-permalink,
                    .heading-permalink:hover {
                        text-decoration: underline;
                        color: #777;
                    }

                    :target {
                        animation: permalink-highlight 1s ease-out;
                    }

                    @keyframes permalink-highlight {
                        from { background-color: yellow; }
                    }

                    * {
                        /*Fonts used by GitHub repo pages*/
                        font-family: -apple-system,BlinkMacSystemFont,"Segoe UI","Noto Sans",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
                    }
                </style>
            </head>
            <body>
        ' . $md . '</body></html>';
        $res = new Response($md);
        $res->headers->set('Content-Type', 'text/html');
        return $res;
    }
}
