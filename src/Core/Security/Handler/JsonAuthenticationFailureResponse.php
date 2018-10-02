<?php

namespace Core\Security\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JsonAuthenticationFailureResponse
 * @category GSG
 * @package Core\Security\Handler
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class JsonAuthenticationFailureResponse extends JsonResponse
{
    /**
     * The response message.
     *
     * @var string
     */
    private $message;

    /**
     * {@inheritdoc}
     */
    public function __construct($message = 'Bad credentials', $statusCode = JsonResponse::HTTP_BAD_REQUEST)
    {
        $this->message = $message;

        parent::__construct(null, $statusCode);
    }

    /**
     * @param string $message
     * @return JsonAuthenticationFailureResponse
     */
    public function setMessage(string $message): JsonAuthenticationFailureResponse
    {
        $this->message = $message;
        $this->setData();

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data = [])
    {
        parent::setData(array_merge((array)$data, ['message' => $this->message]));
    }
}