<?php

namespace App\EventSubscriber;

use PDOException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use App\Utils\Response\NormalizedJsonResponse;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $m = $exception->getMessage();

        $status = 500;
        $json = [
            'message' => $m,
        ];

        // Handle PDO Exception:
        if ($exception instanceof PDOException) {
            if (strstr($m, 'SQLSTATE[23000]') !== false) {
                if (strstr($m, 'Duplicate entry') !== false) {
                    // The message has the form:
                    // ... Duplicate entry 'registereduser@email.com' for key 'users.email_UNIQUE'
                    // So we have to extract the duplicate field name, i.e. 'email', from the word 'users.email_UNIQUE'.
                    $x = strstr($m, 'for key');                                 // $x = for key 'table.field_UNIQUE'
                    $x = str_replace('for key ', '', $x);                       // $x = 'table.field_UNIQUE'
                    $x = preg_replace('/^[\'][^.]+\\.([^_]+?)_.*$/', '$1', $x); // $x = field
                    $json['message'] = "Duplicate $x.";
                    $status = 400;
                }
            }
        }
        elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
        }

        // https://symfony.com/doc/current/routing.html#getting-the-route-name-and-parameters
        if (str_starts_with(
            $event->getRequest()->attributes->get('_route'),
            'home_')
        ) {
            // This is a web route
            $response = new Response('
            <html>
                <head>
                    <title>Error</title>
                </head>
                <body>
                    <div style="color: red;">
                    <b>ERROR:</b> ' . $json['message'] . '
                    </div>
                </body>
            </html>
            ', $status);
            $event->setResponse($response);
        } else {
            // Regular API routes
            $event->setResponse(new NormalizedJsonResponse($json, $status));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
