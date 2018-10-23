<?php namespace Core\Controller;

use Core\Exception\FormManagerException;
use Core\Interfaces\CoreEvents;
use Core\Listener\CodeReceiveEvent;
use Core\Manager\TokenManager;
use Core\Mapper\UserRegisterMapper;
use Core\Mapper\ForgotMapper;
use Core\Mapper\CodeMapper;
use Core\Manager\UserManager;
use Core\Manager\FormManager;
use Core\Manager\UserCodeManager;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


/**
 * Class UserController
 * @category GSG
 * @package Core\Controller
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UserController extends Controller
{
    /**
     * @Route("/api/v1/user/create/", name="api.user.create", methods="POST")
     *
     * @param Request $request
     * @return Response
     * @throws FormManagerException
     * @throws \ReflectionException
     */
    public function createAction(Request $request): Response
    {
        /**
         * @var UserManager $uManager
         * @var FormManager $fManager
         * @var TokenManager $tManager
         */
        $uManager = $this->get('core.user_manager');
        $fManager = $this->get('core.manager.form');
        $tManager = $this->get('core.user.token_manager');

        $user = $uManager->createUser();
        $form = $fManager->createForm(new UserRegisterMapper(), $user)->submit($request);

        if (!$form->isValid()) {
            return new JsonResponse($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $user->setConfirmationToken(\sha1(time()))
            ->setUsername($user->getEmail());
        $uManager->updateUser($user);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED,
            new FilterUserResponseEvent($user, $request, new Response()));

        return new JsonResponse([
            'accessToken' => [
                'value' => $tManager->createAccessToken($user),
                'exp' => $tManager->getAccessTokenExp(),
            ],
            'refreshToken' => [
                'value' => $tManager->createRefreshToken($user),
                'exp' => $tManager->getRefreshTokenExp(),
            ],
        ]);
    }

    /**
     * @Route("/api/v1/user/oauth_sources", name="api.user.oauth_sources", methods="GET")
     *
     * @return JsonResponse
     */
    public function oauthSourcesAction(): JsonResponse
    {
        return new JsonResponse($this->container->getParameter('oauth.sources'));
    }

    /**
     * @Route(
     *     "/api/v1/user/send-email/{action}/",
     *     name="api.user.secure.send_email",
     *     methods={"GET"},
     *     requirements={"action"="forgot"}
     * )
     *
     * @param Request $request
     * @param string $action
     * @return JsonResponse
     * @throws FormManagerException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function emailAction(Request $request, string $action): JsonResponse
    {
        $sManager = $this->get('security.authorization_checker');
        $payload = [
            'host' => $request->getSchemeAndHttpHost(),
            'action' => $action,
        ];

        if (true === $sManager->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        /**
         * @var $fManager FormManager
         */
        $fManager = $this->get('core.manager.form');

        $data = new ForgotMapper();
        $form = $fManager->createForm(new ForgotMapper(), $data)->submit($request);

        if (!$form->isValid()) {
            return new JsonResponse($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $payload['email'] = $data->getEmail();

        if (true === $this->get('core.user_manager')->isTechnicalEmail($payload['email'])) {
            return new JsonResponse(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        /**
         * @var $ucManager UserCodeManager
         */
        $ucManager = $this->get('core.manager.user_code');
        $payload['code'] = $ucManager->getCode($action, $payload['email']);

        // TODO: Сделано для отладки, вместо отправки почты
        return new JsonResponse([$payload['code']], Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/v1/user/{action}/",
     *     name="api.user.secure.verify_code",
     *     methods={"POST"},
     *     requirements={"action"="change-password"}
     * )
     *
     * @param Request $request
     * @param string $action
     * @return JsonResponse|Response
     * @throws \ReflectionException
     * @throws FormManagerException
     */
    public function codeAction(Request $request, string $action)
    {
        $data = new CodeMapper();
        /**
         * @var $fManager FormManager
         * @var $form Form
         */
        $fManager = $this->get('core.manager.form');
        $form = $fManager->createForm(new CodeMapper(), $data)->submit($request);

        if (!$form->isValid()) {
            return new JsonResponse($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $eAction = UserCodeManager::$mapping[$action];

        if (!$this->get('core.manager.user_code')->isValidCode($data->getEmail(), $eAction, $data->getCode())) {
            return new JsonResponse(['message' => 'Received code is invalid.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->get('core.user_manager')->findUserByEmail($data->getEmail());
        $event = new CodeReceiveEvent($data->getCode(), $user, $request);
        $this->get('event_dispatcher')->dispatch(CoreEvents::EVENT_RECEIVE_CODE_FORGOT, $event);

        if (null !== $response = $event->getResponse()) {
            return $response;
        }

        return new JsonResponse(['result' => true]);
    }
}