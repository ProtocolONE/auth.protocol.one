<?php namespace Core\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\OAuthServerBundle\Entity\AccessToken;

/**
 * Class OAuth2AccessToken
 * @category GSG
 * @package Core\Document
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @MongoDB\Document
 */
class OAuth2AccessToken extends AccessToken
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceMany(targetDocument="OAuth2Client")
     */
    protected $client;

    /**
     * @MongoDB\ReferenceMany(targetDocument="User")
     */
    protected $user;
}