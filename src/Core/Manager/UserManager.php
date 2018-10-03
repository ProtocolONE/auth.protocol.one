<?php

namespace Core\Manager;

use Core\Document\User;
use Core\Document\UserAgent;
use Core\Document\UserSocialNetwork;
use Core\Document\UserAuth;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager as FOSDoctrineUserManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Predis\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class UserManager
 * @category GSG
 * @package Core\Manager
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UserManager extends FOSDoctrineUserManager
{
    public const TECHNICAL_EMAIL_MASK = '%s_%s@protocol.one';
    public const TECHNICAL_EMAIL_PATTERN = '/^[facebook|twitter|google|vkontakte|odnoklassniki].*@protocol.one/i';
    public const USER_PROFILE_STORAGE_KEY = 'user:profile';

    /**
     * @var array
     */
    public static $socials = ['facebook', 'twitter', 'google', 'vkontakte', 'odnoklassniki'];

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * Constructor.
     *
     * @param PasswordUpdaterInterface $passwordUpdater
     * @param CanonicalFieldsUpdater $canonicalFieldsUpdater
     * @param ObjectManager $om
     * @param string $class
     */
    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater, ObjectManager $om, $class)
    {
        parent::__construct($passwordUpdater, $canonicalFieldsUpdater, $om, $class);
    }

    /**
     * @param string $secretKey
     * @return UserManager
     */
    public function setSecretKey(string $secretKey): UserManager
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    /**
     * @param RequestStack $requestStack
     * @return UserManager
     */
    public function setRequestStack(RequestStack $requestStack): UserManager
    {
        $this->request = $requestStack->getCurrentRequest();

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequestStack(): Request
    {
        return $this->request;
    }

    /**
     * @param Client $redis
     * @return UserManager
     */
    public function setRedis(Client $redis): UserManager
    {
        $this->redis = $redis;
        return $this;
    }

    /**
     * @param string $resourceName
     * @param string $resourceUid
     * @return string
     */
    public function getTechnicalEmail(string $resourceName, string $resourceUid): string
    {
        return sprintf(self::TECHNICAL_EMAIL_MASK, $resourceName, $resourceUid);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isTechnicalEmail(string $email): bool
    {
        return true === (bool)preg_match(self::TECHNICAL_EMAIL_PATTERN, $email);
    }

    /**
     * @return User
     */
    public function createUser(): User
    {
        /** @var User $user */
        $user = parent::createUser();
        $user->setEnabled(true);

        return $user;
    }

    /**
     * @param UserInterface $user
     * @param bool $andFlush
     */
    public function updateUser(UserInterface $user, $andFlush = true): void
    {
        parent::updateUser($user, $andFlush);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(['email' => $email, 'enabled' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserByEmail($username);
    }

    /**
     * @param string $network
     * @param string $sIdentifier
     * @return UserSocialNetwork|null
     */
    public function findSocialConnectionBySocialIdentifier(string $network, string $sIdentifier): ?UserSocialNetwork
    {
        return $this->objectManager->getRepository(UserSocialNetwork::class)
            ->findOneBy(
                [
                    'socialId' => $sIdentifier,
                    'network' => $network,
                    'enabled' => true
                ]
            );
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserSocialConnects(User $user): array
    {
        /**
         * @var UserSocialNetwork[] $oConnects
         */
        $oConnects = $this->objectManager->getRepository(UserSocialNetwork::class)
            ->findBy(['userId' => $user->getId(), 'enabled' => true]);
        $sConnects = [];

        foreach (self::$socials as $social) {
            $sConnects[$social] = false;
        }

        if (\count($oConnects) <= 0) {
            return $sConnects;
        }

        foreach ($oConnects as $oConnect) {
            $sConnects[$oConnect->getNetwork()] = true;
        }

        return $sConnects;
    }

    /**
     * @param UserSocialNetwork $socialNetwork
     */
    public function createSocialConnect(UserSocialNetwork $socialNetwork): void
    {
        $this->objectManager->persist($socialNetwork);
        $this->objectManager->flush();
    }

    /**
     * @param string $snCode
     * @param User $user
     */
    public function removeSocialConnection(string $snCode, User $user): void
    {
        $snConnect = $this->objectManager->getRepository(UserSocialNetwork::class)
            ->findOneBy(['userId' => $user->getId(), 'network' => $snCode, 'enabled' => true]);

        if (null === $snConnect) {
            return;
        }

        $snConnect->setUnlinkedAt(new \DateTime('now'));
        $snConnect->setUnlinkIp($this->request->getClientIp());
        $snConnect->setIsActive(false);

        $this->objectManager->persist($snConnect);
        $this->objectManager->flush();
    }

    /**
     * @param int $userIdentifier
     */
    public function removeSocialConnections(int $userIdentifier): void
    {
        $snConnections = $this->objectManager->getRepository(UserSocialNetwork::class)
            ->findBy(['userId' => $userIdentifier, 'enabled' => true]);

        if (\count($snConnections) <= 0) {
            return;
        }

        /** @var UserSocialNetwork $connection */
        foreach ($snConnections as $connection) {
            $connection->setUnlinkedAt(new \DateTime('now'));
            $connection->setUnlinkIp($this->request->getClientIp());
            $connection->setIsActive(false);

            $this->objectManager->persist($connection);
        }

        $this->objectManager->flush();
    }

    /**
     * @param int $userId
     * @return null|object|User
     */
    public function findUserById(int $userId): ?User
    {
        return $this->getRepository()->find($userId);
    }

    /**
     * @param string $identifier
     * @return null|object|User
     */
    public function findUserByIdentifier(string $identifier): ?User
    {
        return $this->getRepository()->find($identifier);
    }

    /**
     * @param User $user
     * @param string $format
     * @return string
     */
    public function serialize(User $user, string $format = JsonEncoder::FORMAT): string
    {
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes(
            [
                'username',
                'password',
                'plainPassword',
                'salt',
                'active',
                'roles',
                'accountNonExpired',
                'accountNonLocked',
                'credentialsNonExpired',
                'enabled',
                'emailCanonical',
                'confirmationToken',
                'usernameCanonical',
                'superAdmin',
                'updatedAt',
                'shortInfo'
            ]
        )->setCallbacks(
            [
                'lastLoginAt' => function ($date) {
                    if (!$date instanceof \DateTime) {
                        return $date;
                    }

                    return $date->format('Y-m-d H:i:s');
                },
                'createdAt' => function (\DateTime $date) {
                    return $date->format('Y-m-d H:i:s');
                },
                'nickname' => function ($nickname) use ($user) {
                    if (!empty($nickname)) {
                        return $nickname;
                    }

                    return $user->getEmail();
                },
            ]
        );

        return (new Serializer([$normalizer], [new JsonEncoder()]))->serialize($user, $format);
    }
}