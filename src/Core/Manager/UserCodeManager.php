<?php

namespace Core\Manager;

use Ramsey\Uuid\Uuid;
use Predis\Client;

/**
 * Class UserCodeManager
 * @category GSG
 * @package Core\Manager
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UserCodeManager
{
    public const SESSION_FIELD_CODE = 'code';
    public const SESSION_FIELD_EXPIRE_AT = 'expire_at';
    public const SESSION_FIELD_EMAIL = 'email';

    public const ACTION_FORGOT = 'forgot';

    /**
     * @var array
     */
    public static $mapping = [
        'change-password' => self::ACTION_FORGOT,
    ];

    /**
     * @var Client
     */
    private $redis;

    /**
     * @var array
     */
    private $settings;

    /**
     * UserCodeManager constructor.
     *
     * @param Client $redis
     * @param array $settings
     */
    public function __construct(Client $redis, array $settings)
    {
        $this->redis = $redis;
        $this->settings = $settings;
    }

    /**
     * @param string $email
     * @param string $type
     * @param string $code
     * @return bool
     */
    public function isValidCode(string $email, string $type, string $code): bool
    {
        $key = sprintf($this->settings['storage_mask'], $type);

        if (false === $this->redis->exists($key)) {
            return false;
        }

        $data = json_decode($this->redis->get($key), true);

        if (null === $data
            || !isset($data[self::SESSION_FIELD_EMAIL], $data[self::SESSION_FIELD_CODE], $data[self::SESSION_FIELD_EXPIRE_AT])
            || $email !== $data[self::SESSION_FIELD_EMAIL]
            || $code !== $data[self::SESSION_FIELD_CODE]
            || time() > (int)$data[self::SESSION_FIELD_EXPIRE_AT]
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param string $type
     * @return UserCodeManager
     */
    public function activateCode(string $type): UserCodeManager
    {
        $this->redis->del([sprintf($this->settings['storage_mask'], $type)]);
        return $this;
    }

    /**
     * @param string $type
     * @param string $email
     * @return string
     * @throws \Exception
     */
    public function getCode(string $type, string $email): string
    {
        $data = [
            self::SESSION_FIELD_CODE => Uuid::uuid4()->toString(),
            self::SESSION_FIELD_EXPIRE_AT => time() + $this->settings['lifetime'],
            self::SESSION_FIELD_EMAIL => $email
        ];

        $this->redis->setex(sprintf($this->settings['storage_mask'], $type), $this->settings['lifetime'], json_encode($data));

        return $data['code'];
    }

    /**
     * @param string $type
     * @return array
     */
    public function getCodeData(string $type): array
    {
        $key = sprintf($this->settings['storage_mask'], $type);

        if (false === $this->redis->exists($key)) {
            return [];
        }

        $data = json_decode($this->redis->get($key), true);

        if (null === $data) {
            return [];
        }

        return $data;
    }
}