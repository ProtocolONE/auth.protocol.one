<?php

namespace Core\Listener;

use Core\Document\OAuth2Client;
use Core\Document\User;
use FOS\OAuthServerBundle\Event\OAuthEvent;

/**
 * Class OAuthEventListener
 * @category GSG
 * @package Core\Listener
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class OAuthEventListener
{
    /**
     * @param OAuthEvent $event
     */
    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
        /** @var OAuth2Client $client */
        $client = $event->getClient();
        $isAuthorizedClient = $client->hasGrant(OAuth2Client::GRANT_ALLOW_SKIP_ACCEPT_PAGE)
            && $event->getUser() instanceof User;

        $event->setAuthorizedClient($isAuthorizedClient);
    }
}