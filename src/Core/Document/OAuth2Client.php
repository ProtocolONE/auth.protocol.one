<?php namespace Core\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\OAuthServerBundle\Entity\Client;

/**
 * Class OAuth2Client
 * @category GSG
 * @package Core\Document
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @MongoDB\Document
 */
class OAuth2Client extends Client
{
    const GRANT_ALLOW_SKIP_ACCEPT_PAGE = 'allow_skip_accept_page';

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @param string $grant
     * @return bool
     */
    public function hasGrant(string $grant): bool
    {
        return in_array($grant, $this->allowedGrantTypes);
    }
}