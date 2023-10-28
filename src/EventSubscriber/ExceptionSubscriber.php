<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'success' => false,
            'message' => $m,
        ];

        // Handle PDO Exception:
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

        $event->setResponse(new JsonResponse($json, $status));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
