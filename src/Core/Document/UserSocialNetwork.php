<?php namespace Core\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class UserSocialNetwork
 * @category GSG
 * @package Core\Document
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @MongoDB\Document
 * @MongoDB\HasLifecycleCallbacks()
 */
class UserSocialNetwork
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
    private $socialId;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $network;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $email;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $ip;

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    private $createdAt;

    /**
     * @var bool
     * @MongoDB\Field(type="boolean")
     */
    private $isActive;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $unlinkIp;

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    private $unlinkedAt;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $supportId;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSocialId(): string
    {
        return $this->socialId;
    }

    /**
     * @param string $socialId
     * @return UserSocialNetwork
     */
    public function setSocialId(string $socialId): UserSocialNetwork
    {
        $this->socialId = $socialId;
        return $this;
    }

    /**
     * @param string|null $ip
     * @return UserSocialNetwork
     */
    public function setIp($ip = null): UserSocialNetwork
    {
        $this->ip = $ip;

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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserSocialNetwork
     */
    public function setEmail(string $email): UserSocialNetwork
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param \DateTime $createdAt
     * @return UserSocialNetwork
     */
    public function setCreatedAt(\DateTime $createdAt): UserSocialNetwork
    {
        $this->createdAt = $createdAt;

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
     * @param bool $isActive
     * @return UserSocialNetwork
     */
    public function setIsActive(bool $isActive): UserSocialNetwork
    {
        $this->isActive = (int)$isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param string|null $unlinkIp
     * @return UserSocialNetwork
     */
    public function setUnlinkIp($unlinkIp = null): UserSocialNetwork
    {
        $this->unlinkIp = $unlinkIp;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUnlinkIp(): ?string
    {
        return $this->unlinkIp;
    }

    /**
     * @param \DateTime|null $unlinkedAt
     * @return UserSocialNetwork
     */
    public function setUnlinkedAt($unlinkedAt = null): UserSocialNetwork
    {
        $this->unlinkedAt = $unlinkedAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUnlinkedAt(): ?\DateTime
    {
        return $this->unlinkedAt;
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
     * @return UserSocialNetwork
     */
    public function setUser(User $user): UserSocialNetwork
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getNetwork(): string
    {
        return $this->network;
    }

    /**
     * @param string $network
     * @return UserSocialNetwork
     */
    public function setNetwork(string $network): UserSocialNetwork
    {
        $this->network = $network;
        return $this;
    }

    /**
     * @param string $supportId
     * @return UserSocialNetwork
     */
    public function setSupportId(string $supportId): UserSocialNetwork
    {
        $this->supportId = $supportId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSupportId(): ?string
    {
        return $this->supportId;
    }

    /**
     * @MongoDB\PrePersist()
     */
    public function setDates(): void
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime('now');
        }

        if (null === $this->isActive) {
            $this->isActive = true;
        }
    }
}
