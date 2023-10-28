<?php

namespace App\EventSubscriber;

use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BootSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        // This function gets called at the very start of the request-response
        // process, even before the controller is determined.
        // https://symfony.com/doc/current/reference/events.html#kernel-request
        if (!date_default_timezone_set($_ENV['TIME_ZONE'])) {
            throw new RuntimeException("Invalid timezone: " . $_ENV['TIME_ZONE']);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
