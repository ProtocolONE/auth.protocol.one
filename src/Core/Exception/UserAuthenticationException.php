<?php

namespace Core\Exception;

use Throwable;

/**
 * Class UserAuthenticationException
 * @category GSG
 * @package Core\Exception
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UserAuthenticationException extends CoreException
{
    /**
     * @var array
     */
    private $data;

    /**
     * UserAuthenticationException constructor.
     *
     * @param array $data
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(array $data = [], string $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}