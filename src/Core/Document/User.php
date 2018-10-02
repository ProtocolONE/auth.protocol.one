<?php namespace Core\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class User
 * @category GSG
 * @package Core\Document
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @MongoDB\Document
 */
class User extends BaseUser
{
    /**
     * @var string
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    protected $username;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    protected $usernameCanonical;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    protected $email;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    protected $emailCanonical;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    protected $confirmationToken;

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    protected $updateAt;

    /**
     * @var \DateTime
     * @MongoDB\Field(type="date")
     */
    protected $lastLogin;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    protected $avatar;

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username): User
    {
        $this->username = $username;
        $this->setUsernameCanonical($username);
        return $this;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email): User
    {
        $this->email = $email;
        $this->setEmailCanonical($email);
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailCanonical(): string
    {
        return $this->emailCanonical;
    }

    /**
     * @param string $emailCanonical
     * @return User
     */
    public function setEmailCanonical($emailCanonical): User
    {
        $this->emailCanonical = \mb_strtolower($emailCanonical);
        return $this;
    }

    /**
     * @return string
     */
    public function getUsernameCanonical(): string
    {
        return $this->usernameCanonical;
    }

    /**
     * @param string $usernameCanonical
     * @return User
     */
    public function setUsernameCanonical($usernameCanonical): User
    {
        $this->usernameCanonical = \mb_strtolower($usernameCanonical);
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     * @return User
     */
    public function setConfirmationToken($confirmationToken): User
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }
}
