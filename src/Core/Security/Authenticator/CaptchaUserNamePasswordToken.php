<?php

namespace Core\Security\Authenticator;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class CaptchaUserNamePasswordToken
 * @category GSG
 * @package Core\Security\Authenticator
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class CaptchaUserNamePasswordToken extends UsernamePasswordToken
{
    /**
     * @var string
     */
    private $captcha = '';

    /**
     * @param string $captcha
     * @return CaptchaUserNamePasswordToken
     */
    public function setCaptcha(string $captcha): CaptchaUserNamePasswordToken
    {
        $this->captcha = $captcha;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptcha(): string
    {
        return $this->captcha;
    }
}