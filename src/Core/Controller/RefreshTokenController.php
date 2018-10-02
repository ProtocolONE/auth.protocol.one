<?php

namespace Core\Controller;

use Core\Manager\TokenManager;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RefreshTokenController
 * @category GSG
 * @package Core\Controller
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class RefreshTokenController extends Controller
{
    /**
     * @Route("/api/v1/token/refresh/{token}", requirements={"token"="([A-z0-9]{256})"}, name="token.refresh", methods={"GET"})
     *
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     */
    public function refreshAction(Request $request, $token): JsonResponse
    {
        /**
         * @var TokenManager $tManager
         */
        $tManager = $this->container->get('core.user.token_manager');
        $refreshToken = $tManager->findRefreshToken($token);

        if (null === $refreshToken) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        if (false === $refreshToken->isValidToken($request->getClientIp())) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        $tManager = $this->get('core.user.token_manager');

        return new JsonResponse([
            'accessToken' => [
                'value' => $tManager->createAccessToken($refreshToken->getUser()),
                'exp' => $tManager->getAccessTokenExp(),
            ],
            'refreshToken' => [
                'value' => $tManager->createRefreshToken($refreshToken->getUser(), $refreshToken),
                'exp' => $tManager->getRefreshTokenExp(),
            ],
        ]);
    }
}