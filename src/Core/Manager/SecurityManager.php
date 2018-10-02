<?php

namespace Core\Manager;

use Gregwar\Captcha\CaptchaBuilder;
use Predis\Client;
use Core\Security\Authenticator\CaptchaUserNamePasswordToken;

/**
 * Class CaptchaManager
 * @category GSG
 * @package Core\Manager
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class SecurityManager
{
    private const FIELD_ATTEMPT_COUNT = 'attempt_count';
    private const FIELD_ATTEMPT_CAPTCHA_LOCK = 'attempt_captcha_lock';
    private const FIELD_ATTEMPT_LAST = 'attempt_last';

    private const AUTHENTICATION_ATTEMPTS_STORAGE = 'security:authentication:attempts';

    private const CAPTCHA_LOGIN_KEY = 'captcha:login:%s';
    private const CAPTCHA_LOGIN_TIME = 600;
    private const CAPTCHA_MODE_INLINE = 'inline';
    private const CAPTCHA_MODE_GET = 'get';

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var SecuritySettingsManager
     */
    private $sManager;

    /**
     * @var bool
     */
    private $isTemporaryLock = false;

    /**
     * @var bool
     */
    private $isCaptchaLock = false;

    /**
     * @var bool
     */
    private $isCaptchaValid = true;

    /**
     * @var array
     */
    private static $attemptsDefault = [
        self::FIELD_ATTEMPT_COUNT => 0,
        self::FIELD_ATTEMPT_LAST => 0,
        self::FIELD_ATTEMPT_CAPTCHA_LOCK => 0
    ];

    /**
     * SecurityManager constructor.
     *
     * @param Client $redis
     * @param array $settings
     */
    public function __construct(Client $redis, array $settings)
    {
        $this->redis = $redis;

        $this->sManager = new SecuritySettingsManager($settings);
    }

    /**
     * @param CaptchaUserNamePasswordToken $token
     */
    public function processAuthenticate(CaptchaUserNamePasswordToken $token): void
    {
        $aAttempts = $this->getAccountAttempts($token->getUsername());

        $this
            ->calculateTemporaryLock($aAttempts)
            ->calculateCaptcha($token->getUsername(), $aAttempts, $token->getCaptcha())
            ->updateAccountAttempts($token->getUsername(), $aAttempts);
    }

    /**
     * @return bool
     */
    public function isTemporaryLock(): bool
    {
        return $this->isTemporaryLock;
    }

    /**
     * @return bool
     */
    public function isCaptchaLock(): bool
    {
        return $this->isCaptchaLock;
    }

    /**
     * @return bool
     */
    public function isCaptchaValid(): bool
    {
        return $this->isCaptchaValid;
    }

    /**
     * @param string $login
     * @param array $aAttempts
     * @param string $rCaptcha
     * @return SecurityManager
     */
    protected function calculateCaptcha(string $login, array $aAttempts, string $rCaptcha): SecurityManager
    {
        $this->isCaptchaLock = $aAttempts[self::FIELD_ATTEMPT_COUNT] >= $this->sManager->getBeforeCaptchaTryCount();

        if (false === $this->isCaptchaLock) {
            $this->isCaptchaValid = true;

            return $this;
        }

        if (false === (bool)$aAttempts[self::FIELD_ATTEMPT_CAPTCHA_LOCK]) {
            return $this;
        }

        $sCaptcha = $this->redis->get($this->getCaptchaStorageKey($login));

        if (\strlen($rCaptcha) <= 0 || \strlen($sCaptcha) <= 0) {
            $this->isCaptchaValid = false;

            return $this;
        }

        $this->isCaptchaValid = strtoupper($rCaptcha) === $sCaptcha;

        return $this;
    }

    /**
     * @param array $aAttempts
     * @return SecurityManager
     */
    protected function calculateTemporaryLock(array $aAttempts): SecurityManager
    {
        $this->isTemporaryLock = $aAttempts[self::FIELD_ATTEMPT_COUNT] >= $this->sManager->getBeforeLockTryCount();

        return $this;
    }

    /**
     * @param string $account
     * @return array
     */
    protected function getAccountAttempts(string $account): array
    {
        if (false === $this->redis->hexists(self::AUTHENTICATION_ATTEMPTS_STORAGE, $account)) {
            return self::$attemptsDefault;
        }

        $data = json_decode($this->redis->hget(self::AUTHENTICATION_ATTEMPTS_STORAGE, $account), true);

        if (null === $data || !isset($data[self::FIELD_ATTEMPT_COUNT], $data[self::FIELD_ATTEMPT_LAST])) {
            return self::$attemptsDefault;
        }

        $tsDif = (new \DateTime('now'))->getTimestamp() - $data[self::FIELD_ATTEMPT_LAST];

        if ($tsDif > $this->sManager->getLifetime()) {
            return self::$attemptsDefault;
        }

        return $data;
    }

    /**
     * @param string $account
     * @param array $attempts+
     */
    protected function updateAccountAttempts(string $account, array $attempts): void
    {
        $attempts[self::FIELD_ATTEMPT_COUNT]++;
        $attempts[self::FIELD_ATTEMPT_LAST] = (new \DateTime('now'))->getTimestamp();

        if (true === $this->isCaptchaLock
            && false === (bool)$attempts[self::FIELD_ATTEMPT_CAPTCHA_LOCK]) {
            $attempts[self::FIELD_ATTEMPT_CAPTCHA_LOCK] = 1;
        }

        $this->redis->hset(self::AUTHENTICATION_ATTEMPTS_STORAGE, $account, json_encode($attempts));
    }

    /**
     * @param string $account
     */
    public function removeAccountAttempts(string $account): void
    {
        $this->redis->hdel(self::AUTHENTICATION_ATTEMPTS_STORAGE, [$account]);
    }

    /**
     * @return string
     */
    protected function getCaptchaChars(): string
    {
        $chars = $this->sManager->getCaptchaBuilderChars();
        $captcha = '';

        for ($i = 0; $i < $this->sManager->getCaptchaLength(); $i++) {
            $captcha .= $chars[array_rand($chars, 1)];
        }

        return strtoupper($captcha);
    }

    /**
     * @param string $login
     * @param string $mode
     * @return mixed
     */
    public function getCaptchaImage(string $login, string $mode)
    {
        $cbSettings = $this->sManager->getCaptchaBuilderSettings();
        $captchaChars = $this->getCaptchaChars();

        $this->redis->setex($this->getCaptchaStorageKey($login), self::CAPTCHA_LOGIN_TIME, $captchaChars);

        $captchaBuilder = new CaptchaBuilder($captchaChars);
        $captchaBuilder->setDistortion(false);

        if (isset($cbSettings['textColor'])) {
            \call_user_func_array([$captchaBuilder, 'setTextColor'], $cbSettings['textColor']);
        }

        if (isset($cbSettings['backColor'])) {
            \call_user_func_array([$captchaBuilder, 'setBackgroundColor'], $cbSettings['backColor']);
        }

        $captcha = $captchaBuilder->build($cbSettings['width'], $cbSettings['height']);

        if (self::CAPTCHA_MODE_GET === $mode) {
            return $captcha->get();
        }

        return $captcha->inline();
    }

    private function getCaptchaStorageKey($login): string
    {
        return sprintf(self::CAPTCHA_LOGIN_KEY, $login);
    }
}