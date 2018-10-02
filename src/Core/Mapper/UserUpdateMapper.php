<?php

namespace Core\Mapper;

use Core\Manager\AbstractRequestMapper;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class UserUpdateMapper
 * @category GSG
 * @package Core\Mapper
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UserUpdateMapper extends AbstractRequestMapper
{
    /**
     * @var string
     *
     * @Constraints\Regex(pattern="/^([A-Za-z0-9\._\-\~\[\]]{4,25})$/", message="validation.user.update.nickname.pattern")
     */
    private $nickname;

    /**
     * @var bool
     */
    private $hasTechnicalEmail;

    /**
     * @var bool
     */
    private $isAccountConfirmed;

    /**
     * @var array
     */
    private $options = [
        'hasTechnicalEmail' => [AbstractRequestMapper::OPTION_MAP_SKIP => true],
        'isAccountConfirmed' => [AbstractRequestMapper::OPTION_MAP_SKIP => true]
    ];

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     * @return UserUpdateMapper
     */
    public function setNickname($nickname): UserUpdateMapper
    {
        $this->nickname = $nickname;
        return $this;
    }

    /**
     * @param bool $hasTechnicalEmail
     * @return UserUpdateMapper
     */
    public function setHasTechnicalEmail(bool $hasTechnicalEmail): UserUpdateMapper
    {
        $this->hasTechnicalEmail = $hasTechnicalEmail;
        return $this;
    }

    /**
     * @param bool $isAccountConfirmed
     * @return UserUpdateMapper
     */
    public function setIsAccountConfirmed(bool $isAccountConfirmed): UserUpdateMapper
    {
        $this->isAccountConfirmed = $isAccountConfirmed;
        return $this;
    }

    /**
     * @Constraints\Callback
     *
     * {@inheritdoc}
     */
    public function validate(ExecutionContextInterface $context)
    {
        $properties = get_object_vars($this);

        foreach ($properties as $key => $value) {
            if (null !== $value) {
                continue;
            }

            $this->options[$key] = [AbstractRequestMapper::OPTION_MAP_SKIP => true];
        }

        if (null !== $this->nickname && empty($this->nickname)) {
            $context
                ->buildViolation('validation.user.update.nickname.not_blank')
                ->atPath('nickname')
                ->setParameter('{{ value }}', $this->nickname)
                ->addViolation();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getOptions(): array
    {
        return $this->options;
    }
}