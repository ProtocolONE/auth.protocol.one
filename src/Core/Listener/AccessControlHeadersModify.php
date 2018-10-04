<?php namespace Core\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AccessControlHeadersModify
 *
 * @category GSG
 * @package Core\Listener
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class AccessControlHeadersModify implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $httpRequestOrigin = $event->getRequest()->headers->get('origin');
        // TODO: Trust only trusted sources
        $event->getResponse()->headers->set('Access-Control-Allow-Origin', $httpRequestOrigin);
        $event->getResponse()->headers->set('Access-Control-Allow-Credentials', 'true');
        $event->getResponse()->headers->set('Access-Control-Allow-Headers', 'origin, content-type, accept');
    }
}