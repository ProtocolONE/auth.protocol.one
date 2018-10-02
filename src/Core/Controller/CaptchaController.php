<?php

namespace Core\Controller;

use Core\Manager\SecurityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CaptchaController
 * @category GSG
 * @package Core\Controller
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class CaptchaController extends Controller
{
    /**
     * @Route("/api/v1/captcha/login/", name="captcha.login", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        $login = $request->query->get('email', '');
        $mode = $request->query->get('mode', 'get');
        if (!$login) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var $sManager SecurityManager
         */
        $sManager = $this->container->get('core.manager.security');

        $response = new Response($sManager->getCaptchaImage($login, $mode));
        $response->headers
            ->add(
                [
                    'Content-type' => 'image/jpeg',
                    'Pragma' => 'no-cache',
                    'Cache-Control' => 'no-cache'
                ]
            );

        return $response;
    }
}