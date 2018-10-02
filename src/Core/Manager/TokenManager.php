<?php

namespace Core\Manager;

use Core\Document\User;
use Core\Document\UserAgent;

use Core\Document\UserAuth;
use Doctrine\Common\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class TokenManager
 * @category GSG
 * @package Core\Manager
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class TokenManager
{
    /**
     * @var RequestStack|null
     */
    private $requestStack;

    /**
     * @param JWTManager $jwtManager
     */
    private $jwtManager;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var int
     */
    private $refreshTokenTtl;

    /**
     * @var int
     */
    private $accessTokenTtl;

    /**
     * Constructor.
     *
     * @param RequestStack $requestStack
     * @param ObjectManager $objectManager
     * @param JWTManager $jwtManager
     * @param int $refreshTokenTtl
     * @param int $accessTokenTtl
     */
    public function __construct(
        RequestStack $requestStack,
        ObjectManager $objectManager,
        JWTManager $jwtManager,
        int $refreshTokenTtl,
        int $accessTokenTtl
    ) {
        $this->requestStack = $requestStack;
        $this->objectManager = $objectManager;
        $this->jwtManager = $jwtManager;
        $this->refreshTokenTtl = $refreshTokenTtl;
        $this->accessTokenTtl = $accessTokenTtl;
    }

    /**
     * @param User $user
     * @return string
     */
    public function createAccessToken(User $user): string
    {
        return $this->jwtManager->create($user);
    }

    /**
     * @param User $user
     * @param UserAuth $refreshToken
     * @return string
     */
    public function createRefreshToken(User $user, UserAuth $refreshToken = null): string
    {
        $token = bin2hex(openssl_random_pseudo_bytes(128));
        $expireAt = (new \DateTime)->modify("+{$this->refreshTokenTtl} seconds");

        if ($refreshToken instanceof UserAuth) {
            $refreshToken->setExpireAt($expireAt);
        } else {
            $refreshToken = (new UserAuth)
                ->setUser($user)
                ->setIp($this->requestStack->getCurrentRequest()->getClientIp())
                ->setUserAgent($this->getUserAgent())
                ->setCreatedAt(new \DateTime())
                ->setExpireAt($expireAt);

            $this->objectManager->persist($refreshToken);
        }

        $refreshToken->setToken($token);
        $this->objectManager->flush();

        return $token;
    }

    /**
     * @param string $token
     * @return null|UserAuth
     */
    public function findRefreshToken(string $token): ?UserAuth
    {
        return $this->objectManager->getRepository(UserAuth::class)->findOneBy(['token' => $token]);
    }

    /**
     * @param UserAuth $userAuth
     * @return bool|string
     */
    public function refreshToken(UserAuth $userAuth)
    {
        if (false === $userAuth->isValidToken($this->requestStack->getCurrentRequest()->getClientIp())) {
            return false;
        }

        return $this->createRefreshToken($userAuth->getUser());
    }

    /**
     * @return int
     */
    public function getRefreshTokenTtl(): int
    {
        return $this->refreshTokenTtl;
    }

    /**
     * @return int
     */
    public function getAccessTokenTtl(): int
    {
        return $this->accessTokenTtl;
    }

    /**
     * @return int
     */
    public function getRefreshTokenExp(): int
    {
        return (new \DateTime)->getTimestamp() + $this->refreshTokenTtl;
    }

    /**
     * @return int
     */
    public function getAccessTokenExp(): int
    {
        return (new \DateTime)->getTimestamp() + $this->accessTokenTtl;
    }

    /**
     * @return UserAgent
     */
    private function getUserAgent()
    {
        /**
         * @var \Core\Document\Repository\UserAgentRepository $repository
         */
        $ua = $this->requestStack->getCurrentRequest()->headers->get('User-Agent');
        $repository = $this->objectManager->getRepository(UserAgent::class);
        $userAgent = $repository->findOneBy(['value' => $ua]);

        if (false === $userAgent instanceof UserAgent) {
            $userAgent = (new UserAgent)->setValue($ua);
            $this->objectManager->persist($userAgent);
            $this->objectManager->flush();
        }

        return $userAgent;
    }
}