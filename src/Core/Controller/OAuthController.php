<?php namespace Core\Controller;

use Core\Document\User;
use Core\Manager\TokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class OAuthController
 * @category GSG
 * @package Core\Controller
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class OAuthController extends Controller
{
    /**
     * @Route("/api/v1/oauth/sources/", name="api.oauth.sources", methods="GET")
     *
     * @return JsonResponse
     */
    public function sourcesAction(): JsonResponse
    {
        return new JsonResponse($this->container->getParameter('oauth.sources'));
    }

    /**
     * @Route("/api/v1/oauth/result/{mode}/", requirements={"mode"="(websocket|postmessage)"}, name="api.oauth.result", methods="GET")
     *
     * @param Request $request
     * @param string $mode
     * @return Response
     */
    public function resultAction(Request $request, $mode): Response
    {
        $user = $this->getUser();
        $event = 'oauthCompletedWithError';
        $tokens = [];

        if ($user instanceof User) {
            /**
             * @var TokenManager $tManager
             */
            $tManager = $this->get('core.user.token_manager');
            $event = 'oauthCompletedSuccessfully';
            $tokens = [
                'accessToken' => [
                    'value' => $tManager->createAccessToken($user),
                    'exp' => $tManager->getAccessTokenExp(),
                ],
                'refreshToken' => [
                    'value' => $tManager->createRefreshToken($user),
                    'exp' => $tManager->getRefreshTokenExp(),
                ],
            ];
        }

        $params = [
            'mode' => $mode,
            'event' => $event,
            'tokens' => $tokens,
            'wsUrl' => $request->query->get('wsUrl'),
        ];

        return $this->render(':app:oauth_result.html.twig', $params);
    }
}