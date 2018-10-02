<?php

namespace Core\Mapper;

use Core\Manager\AbstractRequestMapper;
use Symfony\Component\Validator\Constraints;
use Core\Validator;

/**
 * Class UserRegisterType
 * @category GSG
 * @package Core\Form
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @Validator\UniqueData(
 *     message="validation.user.register.email.exist",
 *     fields={"email"},
 *     entityClass="Core\Document\User",
 *     em="users"
 * )
 */
class UserRegisterMapper extends AbstractRequestMapper
{
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
     * @var bool
     *
     * @Constraints\NotNull(message="validation.user.register.read_eula.not_bool")
     * @Constraints\Type(type="bool", message="validation.user.register.read_eula.not_bool")
     * @Constraints\IsTrue(message="validation.user.register.read_eula.not_true")
     */
    private $readEula;

    protected function getOptions(): array
    {
        return [
            'password' => [AbstractRequestMapper::OPTION_MAP_TO => 'plainPassword'],
            'readEula' => [AbstractRequestMapper::OPTION_MAP_SKIP => true],
        ];
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
     * @return UserRegisterMapper
     */
    public function setEmail(string $email): UserRegisterMapper
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
     * @return UserRegisterMapper
     */
    public function setPassword(string $password): UserRegisterMapper
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return bool
     */
    public function getReadEula(): bool
    {
        return $this->readEula;
    }

    /**
     * @param bool $readEula
     * @return UserRegisterMapper
     */
    public function setReadEula(bool $readEula): UserRegisterMapper
    {
        $this->readEula = $readEula;
        return $this;
    }
}