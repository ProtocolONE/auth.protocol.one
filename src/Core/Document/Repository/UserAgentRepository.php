<?php namespace Core\Document\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class UserAgentRepository extends DocumentRepository
{
    public function exists($userAgent)
    {
        return (bool)$this->findBy(['value' => $userAgent]);
    }
}