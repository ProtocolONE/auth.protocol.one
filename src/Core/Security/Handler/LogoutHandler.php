<?php

namespace Core\Security\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

/**
 * Class LogoutHandler
 * @category GSG
 * @package Core\Security\Handler
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class LogoutHandler extends DefaultLogoutSuccessHandler
{
    /**
     * @var string
     */
    private $cookieName;

    /**
     * LogoutHandler constructor.
     *
     * @param HttpUtils $httpUtils
     * @param string $targetUrl
     * @param string $cookieName
     */
    public function __construct(HttpUtils $httpUtils, string $targetUrl = '/', string $cookieName)
    {
        $this->cookieName = $cookieName;

        parent::__construct($httpUtils, $targetUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        if (true === $request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => 'Successfully logout']);
        }

        $response = parent::onLogoutSuccess($request);
        $response->headers->clearCookie($this->cookieName);

        return $response;
    }
}