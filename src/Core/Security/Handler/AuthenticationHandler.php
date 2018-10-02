<?php

namespace Core\Security\Handler;

use Core\Exception\UserAuthenticationCaptchaRequireException;
use Core\Exception\UserAuthenticationTemporaryLockException;
use Core\Manager\TokenManager;
use Core\Manager\UserManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class AuthenticationFailureHandler
 * @category GSG
 * @package Core\Security\Handler
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var UserManager
     */
    private $uManager;

    /**
     * @var TokenManager
     */
    private $tManager;

    /**
     * AuthenticationHandler constructor.
     *
     * @param UserManager $uManager
     * @param TokenManager $tManager
     */
    public function __construct(UserManager $uManager, TokenManager $tManager)
    {
        $this->uManager = $uManager;
        $this->tManager = $tManager;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $session = $request->getSession();

        if($session->has('_security.secured_area.target_path')) {
            return new JsonResponse(['url' => $session->get('_security.secured_area.target_path')], Response::HTTP_FOUND);
        }

        return new JsonResponse([
            'accessToken' => [
                'value' => $this->tManager->createAccessToken($token->getUser()),
                'exp' => $this->tManager->getAccessTokenExp(),
            ],
            'refreshToken' => [
                'value' => $this->tManager->createRefreshToken($token->getUser()),
                'exp' => $this->tManager->getRefreshTokenExp(),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = new JsonAuthenticationFailureResponse();
        $message = 'Bad credentials';

        if ($exception instanceof CustomUserMessageAuthenticationException) {
            $message = $exception->getMessageKey();
        }

        if ($exception instanceof UserAuthenticationCaptchaRequireException
            || $exception instanceof UserAuthenticationTemporaryLockException) {
            $message = $exception->getMessage();
        }

        if ($exception instanceof UserAuthenticationCaptchaRequireException) {
            $response->setStatusCode(Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($exception instanceof UserAuthenticationTemporaryLockException) {
            $response->setStatusCode(Response::HTTP_LOCKED);
        }

        $response->setMessage($message);

        return $response;
    }
}