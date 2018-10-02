<?php

namespace Core\Security\Authenticator;

use Core\Document\User;
use Core\Exception\UserAuthenticationCaptchaRequireException;
use Core\Exception\UserAuthenticationTemporaryLockException;
use Core\Manager\SecurityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

/**
 * Class CaptchaAuthenticator
 * @category GSG
 * @package Core\Security\Authenticator
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class CaptchaAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var SecurityManager
     */
    private $manager;

    /**
     * CaptchaAuthenticator constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param SecurityManager $manager
     */
    public function __construct(UserPasswordEncoderInterface $encoder, SecurityManager $manager)
    {
        $this->encoder = $encoder;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        try {
            /** @var User $user */
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }

        /** @var $token CaptchaUserNamePasswordToken */
        $this->manager->processAuthenticate($token);

        if (true === $this->manager->isTemporaryLock()) {
            throw new UserAuthenticationTemporaryLockException('Authentication temporary blocked for you');
        }

        $isPasswordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());

        if (true === $isPasswordValid && true === $this->manager->isCaptchaValid()) {
            $this->manager->removeAccountAttempts($token->getUsername());

            return new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());
        }


        if (true === $this->manager->isCaptchaLock()) {
            $message = 'Enter captcha code';

            if (false === $this->manager->isCaptchaValid()) {
                $message = 'Captcha invalid';
            } elseif (true !== $isPasswordValid) {
                $message = 'Invalid username or password';
            }

            throw new UserAuthenticationCaptchaRequireException($message);
        }

        throw new CustomUserMessageAuthenticationException('Invalid username or password');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof CaptchaUserNamePasswordToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        $token = new CaptchaUserNamePasswordToken($username, $password, $providerKey);

        if (true === $request->request->has('captcha')) {
            $token->setCaptcha($request->request->getAlnum('captcha'));
        }

        return $token;
    }
}