<?php

namespace Core\Listener;

use Core\Document\User;
use Core\Document\UserCode;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Security\LoginManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class RegistrationListener
 * @category GSG
 * @package Core\Listener
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class RegistrationListener implements EventSubscriberInterface
{
    /**
     * @var LoginManagerInterface
     */
    private $loginManager;

    /**
     * @var string
     */
    private $firewallName;

    /**
     * AuthenticationListener constructor.
     *
     * @param LoginManagerInterface $loginManager
     * @param string $firewall
     */
    public function __construct(LoginManagerInterface $loginManager, string $firewall)
    {
        $this->loginManager = $loginManager;
        $this->firewallName = $firewall;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationComplete'
        ];
    }

    /**
     * @param FilterUserResponseEvent $event
     * @param string $eventName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function onRegistrationComplete(FilterUserResponseEvent $event, string $eventName, EventDispatcherInterface $eventDispatcher): void
    {
        /** @var User $user */
        $user = $event->getUser();

        try {
            $this->loginManager->logInUser($this->firewallName, $user, $event->getResponse());

            $eventDispatcher->dispatch(FOSUserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($event->getUser(), $event->getRequest()));
        } catch (AccountStatusException $e) {}
    }
}