<?php

namespace Core\Manager;

/**
 * Class SecuritySettingsManager
 * @category GSG
 * @package Core\Manager
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class SecuritySettingsManager
{
    const CAPTCHA_BUILDER_DEFAULT_SETTINGS = 'login';

    const SETTING_DEFAULT_BEFORE_CAPTCHA_TRY_COUNT = 3;
    const SETTING_DEFAULT_BEFORE_LOCK_TRY_COUNT = 10;
    const SETTING_DEFAULT_LIFETIME = 1800;

    /**
     * @var array
     */
    private $captchaBuilderSettings;

    /**
     * @var int
     */
    private $beforeCaptchaTryCount;

    /**
     * @var int
     */
    private $beforeLockTryCount;

    /**
     * @var int
     */
    private $lifetime;

    /**
     * @var array
     */
    private $captchaChars;

    /**
     * @var int
     */
    private $captchaLength;

    /**
     * SecuritySettingsManager constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->captchaBuilderSettings = $settings['captcha_builder_settings'];
        $this->captchaChars = $settings['captcha_chars'];
        $this->captchaLength = $settings['captcha_length'];

        $this->beforeCaptchaTryCount = self::SETTING_DEFAULT_BEFORE_CAPTCHA_TRY_COUNT;
        $this->beforeLockTryCount = self::SETTING_DEFAULT_BEFORE_LOCK_TRY_COUNT;
        $this->lifetime = self::SETTING_DEFAULT_LIFETIME;


        if (!isset($settings['before_captcha_try_count'])) {
            $this->beforeCaptchaTryCount = $settings['before_captcha_try_count'];
        }

        if (!isset($settings['before_lock_try_count'])) {
            $this->beforeLockTryCount = $settings['before_lock_try_count'];
        }

        if (!isset($settings['lifetime'])) {
            $this->lifetime = $settings['lifetime'];
        }
    }

    /**
     * @param string $key
     * @return array
     */
    public function getCaptchaBuilderSettings(string $key = self::CAPTCHA_BUILDER_DEFAULT_SETTINGS): array
    {
        if (!isset($this->captchaBuilderSettings[$key])) {
            return $this->captchaBuilderSettings[self::CAPTCHA_BUILDER_DEFAULT_SETTINGS];
        }

        return $this->captchaBuilderSettings[$key];
    }

    /**
     * @return int
     */
    public function getBeforeCaptchaTryCount(): int
    {
        return $this->beforeCaptchaTryCount;
    }

    /**
     * @return int
     */
    public function getBeforeLockTryCount(): int
    {
        return $this->beforeLockTryCount;
    }

    /**
     * @return int
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    /**
     * @return array
     */
    public function getCaptchaBuilderChars(): array
    {
        return $this->captchaChars;
    }

    /**
     * @return int
     */
    public function getCaptchaLength(): int
    {
        return $this->captchaLength;
    }
}