<?php

namespace Core\Controller;

use FOS\OAuthServerBundle\Controller\AuthorizeController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class AuthorizeController
 * @category GSG
 * @package Core\Controller
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class OAuthAuthorizeController extends AuthorizeController
{
    /**
     * {@inheritdoc}
     */
    public function authorizeAction(Request $request)
    {
        return parent::authorizeAction($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function processSuccess(UserInterface $user, AuthorizeFormHandler $formHandler, Request $request)
    {
        if (true === $request->query->has('q')) {
            $request->query->remove('q');
        }

        return parent::processSuccess($user, $formHandler, $request);
    }
}