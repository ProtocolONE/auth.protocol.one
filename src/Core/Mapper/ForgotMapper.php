<?php

namespace Core\Mapper;

use Core\Manager\AbstractRequestMapper;
use Symfony\Component\Validator\Constraints;
use Core\Validator;

/**
 * Class ForgotMapper
 * @category GSG
 * @package Core\Mapper
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @Validator\UniqueData(
 *     message="validation.user.forgot.email.not_exist",
 *     fields={"email"},
 *     entityClass="Core\Document\User",
 *     isExistCheck=true,
 *     em="users"
 * )
 */
class ForgotMapper extends AbstractRequestMapper
{
    /**
     * @var string
     *
     * @Constraints\NotBlank(message="validation.user.register.email.not_blank")
     * @Constraints\Length(max=255, maxMessage="validation.user.register.email.not_valid")
     * @Constraints\Email(message="validation.user.register.email.not_valid")
     */
    private $email;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return ForgotMapper
     */
    public function setEmail($email): ForgotMapper
    {
        $this->email = $email;
        return $this;
    }
}