<?php namespace Core\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class UserAgent
 * @category GSG
 * @package Core\Document
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @MongoDB\Document(repositoryClass="\Core\Document\Repository\UserAgentRepository")
 */
class UserAgent
{
    /**
     * @var string
     * @MongoDB\Id(strategy="auto")
     */
    private $id;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $value;

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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return UserAgent
     */
    public function setValue(string $value): UserAgent
    {
        $this->value = $value;
        return $this;
    }
}
