<?php

namespace Core\Listener;

use Core\Exception\FormManagerException;
use Core\Interfaces\CoreEvents;
use Core\Manager\FormManager;
use Core\Manager\UserManager;
use Core\Manager\UserCodeManager;
use Core\Mapper\CodeReceiveMapper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CodeReceiveListener
 * @category GSG
 * @package Core\Listener
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class CodeReceiveListener implements EventSubscriberInterface
{
    /**
     * @var UserCodeManager
     */
    private $ucManager;

    /**
     * @var UserManager
     */
    private $uManager;

    /**
     * @var FormManager
     */
    private $fManager;

    /**
     * CodeReceiveListener constructor.
     *
     * @param UserCodeManager $ucManager
     * @param UserManager $uManager
     * @param UserPasswordEncoderInterface $encoder
     * @param FormManager $fManager
     */
    public function __construct(
        UserCodeManager $ucManager,
        UserManager $uManager,
        UserPasswordEncoderInterface $encoder,
        FormManager $fManager
    )
    {
        $this->ucManager = $ucManager;
        $this->uManager = $uManager;
        $this->fManager = $fManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CoreEvents::EVENT_RECEIVE_CODE_FORGOT => 'onReceiveCodeForgot',
        ];
    }

    /**
     * @param CodeReceiveEvent $event
     * @throws FormManagerException
     */
    public function onReceiveCodeForgot(CodeReceiveEvent $event): void
    {
        $data = new CodeReceiveMapper();
        $form = $this->fManager->createForm(new CodeReceiveMapper(), $data)->submit($event->getRequest());

        try {
            if (!$form->isValid()) {
                $event->setResponse(new JsonResponse($form->getErrors(), Response::HTTP_BAD_REQUEST));
                return;
            }
        } catch (\Exception $e) {
            if ($e instanceof FormManagerException) {
                return;
            }
        }

        $this->uManager->updateUser($event->getUser()->setPlainPassword($data->getPassword()));
    }
}