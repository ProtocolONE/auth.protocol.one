<?php

namespace Core\Manager;

use Core\Document\User;
use Core\Document\UserSocialNetwork;
use Core\Exception\AlreadyConnectedAuthenticationException;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;

/**
 * Class UserProvider
 * @category GSG
 * @package Core\Manager
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UserProvider extends BaseFOSUBProvider
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $sIdentifier = $response->getUsername();
        $network = $response->getResourceOwner()->getName();
        $connect = $this->userManager->findSocialConnectionBySocialIdentifier($network, $sIdentifier);

        if (null !== $connect) {
            /** @var User $user */
            if ($user->getId() !== $connect->getUserId()) {
                throw new AlreadyConnectedAuthenticationException('This account already connected.');
            }

            return;
        }

        /** @var \Core\Document\User $user */
        $connect = (new UserSocialNetwork())
            ->setUser($user)
            ->setSocialId($sIdentifier)
            ->setNetwork($network)
            ->setEmail($response->getEmail())
            ->setIp($this->userManager->getRequestStack()->getClientIp());

        $this->userManager->createSocialConnect($connect);
    }

    /**
     * @param UserResponseInterface $response
     * @return User|\FOS\UserBundle\Model\UserInterface|null|object|UserInterface
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $email = $response->getEmail();
        $user = $this->getUserBySocialResponse($response);

        if (null === $user) {
            $user = $this->userManager->createUser();
            $user->setEnabled(true);

            if (empty($email)) {
                $email = $this->userManager
                    ->getTechnicalEmail($response->getResourceOwner()->getName(), $response->getUsername());
            }

            $user
                ->setUsername($email)
                ->setEmail($email)
                ->setPlainPassword($response->getUsername());

            $this->userManager->updateUser($user);
            $this->connect($user, $response);
        }

        if ($user->getEmail() === $email || empty($email)) {
            return $user;
        }

        if (true === $this->userManager->isTechnicalEmail($user->getEmail())) {
            $user->setEmail($email);
            $this->userManager->updateUser($user);
        }

        return $user;
    }

    /**
     * @param UserResponseInterface $response
     * @return User|null
     */
    protected function getUserBySocialResponse(UserResponseInterface $response)
    {
        $connect = $this->userManager->findSocialConnectionBySocialIdentifier($response->getResourceOwner()->getName(), $response->getUsername());

        if (null !== $connect) {
            $user = $this->userManager->findUserByIdentifier($connect->getUserId());

            if (null !== $user) {
                return $user;
            }

            $this->userManager->removeSocialConnections($connect->getUserId());

        }

        if (!empty($response->getEmail())) {
            /** @var User $user */
            $user = $this->userManager->findUserByEmail($response->getEmail());

            if (null !== $user) {
                return $user;
            }
        }

        $tEmail = $this->userManager
            ->getTechnicalEmail($response->getResourceOwner()->getName(), $response->getUsername());
        $user = $this->userManager->findUserByEmail($tEmail);

        if (null === $user) {
            return $user;
        }

        $this->connect($user, $response);

        return $user;
    }
}