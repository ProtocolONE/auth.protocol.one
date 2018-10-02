<?php namespace Core\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class UserAuth
 * @category GSG
 * @package Core\Document
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @MongoDB\Document
 */
class UserAuth
{
    /**
     * @var string
     * @MongoDB\Id(strategy="auto")
     */
    private $id;

    /**
     * @var User
     * @MongoDB\ReferenceOne(targetDocument="User")
     */
    private $user;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $ip;

    /**
     * @var UserAgent
     * @MongoDB\ReferenceOne(targetDocument="UserAgent")
     */
    private $userAgent;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $token;

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    private $expireAt;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserAuth
     */
    public function setUser(User $user): UserAuth
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return UserAuth
     */
    public function setIp(string $ip): UserAuth
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return UserAgent
     */
    public function getUserAgent(): UserAgent
    {
        return $this->userAgent;
    }

    /**
     * @param UserAgent $userAgent
     * @return UserAuth
     */
    public function setUserAgent(UserAgent $userAgent): UserAuth
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return UserAuth
     */
    public function setToken(string $token): UserAuth
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return UserAuth
     */
    public function setCreatedAt(\DateTime $createdAt): UserAuth
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpireAt(): \DateTime
    {
        return $this->expireAt;
    }

    /**
     * @param \DateTime $expireAt
     * @return UserAuth
     */
    public function setExpireAt(\DateTime $expireAt): UserAuth
    {
        $this->expireAt = $expireAt;
        return $this;
    }

    public function isValidToken($ip)
    {
        return (new \DateTime) <= $this->getExpireAt() && $ip === $this->getIp();
    }
}
