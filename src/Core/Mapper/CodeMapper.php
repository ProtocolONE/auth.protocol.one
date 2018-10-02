<?php

namespace Core\Mapper;

use Core\Manager\AbstractRequestMapper;
use Symfony\Component\Validator\Constraints;
use Core\Validator;

/**
 * Class CodeMapper
 * @category GSG
 * @package Core\Mapper
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class CodeMapper extends AbstractRequestMapper
{
    /**
     * @var string
     *
     * @Constraints\NotBlank(message="validation.user.forgot.code.not_blank")
     */
    private $code;

    /**
     * @var string
     *
     * @Constraints\NotBlank(message="validation.user.register.email.not_blank")
     * @Constraints\Length(max=255, maxMessage="validation.user.register.email.not_valid")
     * @Validator\Email(message="validation.user.register.email.not_valid")
     */
    private $email;

    /**
     * @var string
     *
     * @Constraints\NotBlank(message="validation.user.register.password.min_length")
     * @Constraints\Length(min=6, minMessage="validation.user.register.password.min_length")
     */
    private $password;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return CodeMapper
     */
    public function setCode(string $code): CodeMapper
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return CodeMapper
     */
    public function setEmail(string $email): CodeMapper
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return CodeMapper
     */
    public function setPassword(string $password): CodeMapper
    {
        $this->password = $password;
        return $this;
    }
}