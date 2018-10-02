<?php

namespace Core\Listener;

use Core\Document\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CodeReceiveEvent
 * @category GSG
 * @package Core\Listener
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class CodeReceiveEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $code;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Response
     */
    private $response;

    /**
     * CodeReceiveEvent constructor.
     *
     * @param string $code
     * @param User|null $user
     * @param Request $request
     */
    public function __construct(string $code, User $user = null, Request $request)
    {
        $this->code = $code;
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setResponse(Response $response): CodeReceiveEvent
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}