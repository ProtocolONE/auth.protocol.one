<?php

namespace Core\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class JsonRequestTransformListener
 *
 * @category GSG
 * @package App\Listener
 * @copyright Copyright (с) 2017, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class JsonRequestTransformListener
{
    const CONTENT_TYPE_JSON = 'json';

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ('' === $request->getContent()) {
            return;
        }

        if (true === $this->isJsonRequest($request->getContentType())) {
            if (!$this->transformJson($request)) {
                $response = new JsonResponse(
                    [
                        'message' => 'Unable parse request'
                    ],
                    Response::HTTP_BAD_REQUEST
                );
                $event->setResponse($response);
            }
        }

        return;
    }

    /**
     * @param string $contentType
     * @return bool
     */
    private function isJsonRequest($contentType)
    {
        return self::CONTENT_TYPE_JSON === $contentType;
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function transformJson(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if ($data === null) {
            return true;
        }

        $request->request->replace($data);

        return true;
    }
}