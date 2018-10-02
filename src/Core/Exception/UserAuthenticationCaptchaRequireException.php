<?php

namespace Core\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class UserAuthenticationCaptchaRequireException
 * @category GSG
 * @package Core\Exception
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UserAuthenticationCaptchaRequireException extends AuthenticationException {}