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
        <!DOCTYPE html>
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
                <div style="color: red; font-size: 20; font-family: Consolas;">
                    <h1>FOR ADMINISTRATORS ONLY</h1>

                    BE CAREFUL: Resetting the database will <b>ERASE ALL<br>
                    EXISTING DATA</b> in the database !<br>
                    However, <b>IF YOU ARE RUNNING THIS APP THE FIRST TIME,<br>
                    RESET DATABASE NOW !</b><br>
                    <br>
                    To reset database, first go to your .env file, then<br>
                    copy the string after the phrase <code>APP_SECRET=</code>,<br>
                    paste into the following text box and click the button<br>
                    below:<br>

                    <label for="secret-input">APP_SECRET=</label>
                    <input type="text" id="secret-input">
                    <button id="reset-database-button" onclick="javascript:resetDatabase()">Reset database !</button>
                </div>

                <div id="markdown-area">
        ' . $md . '
                </div>

                <script>
        ' . (
            file_get_contents(__DIR__ . '/home.js') ?: 'alert("Error: home.js could not be loaded")'
        ) . '
                </script>
            </body>
        </html>';
        $res = new Response($md);
        $res->headers->set('Content-Type', 'text/html');
        return $res;
    }
}
