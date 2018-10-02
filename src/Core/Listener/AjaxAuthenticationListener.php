<?php

namespace Core\Listener;

use Core\Exception\AlreadyConnectedAuthenticationException;
use Core\Security\Handler\JsonAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AjaxAuthenticationListener
 * @category GSG
 * @package Core\Listener
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class AjaxAuthenticationListener
{
    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if ($exception instanceof AlreadyConnectedAuthenticationException) {
            $response = new RedirectResponse('/account/main');
            $response
                ->headers
                ->setCookie(
                    new Cookie(
                        'EU_FLASH_MSG',
                        base64_encode($exception->getMessage()),
                        (new \DateTime('now'))->modify('+5 second'),
                        '/',
                        null,
                        false,
                        false
                    )
                );

            $event->setResponse($response);
            return;
        }

        if (false === $request->isXmlHttpRequest()) {
            return;
        }

        if (!$exception instanceof AuthenticationException && !$exception instanceof AccessDeniedException) {
            return;
        }

        $event->setResponse(new JsonAuthenticationFailureResponse('Access denied.', Response::HTTP_FORBIDDEN));
    }
}