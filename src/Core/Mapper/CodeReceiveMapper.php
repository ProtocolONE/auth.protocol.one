<?php

namespace Core\Mapper;

use Core\Exception\FormManagerException;
use Core\Manager\AbstractRequestMapper;
use Symfony\Component\Validator\Constraints;

/**
 * Class CodeReceiveMapper
 * @category GSG
 * @package Core\Mapper
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class CodeReceiveMapper extends AbstractRequestMapper
{
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
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return CodeReceiveMapper
     */
    public function setPassword($password): CodeReceiveMapper
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @Constraints\Callback
     *
     * @throws FormManagerException
     */
    public function validate(): void
    {
        if (null !== $this->password) {
            return;
        }

        throw new FormManagerException('Is verify code request.');
    }
}