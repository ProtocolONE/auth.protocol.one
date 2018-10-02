<?php

namespace Core\Manager;

use Core\Exception\FormManagerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class FormHelper
 * @category GSG
 * @package App\Helper
 * @copyright Copyright (с) 2017, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class FormManager
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var AbstractRequestMapper
     */
    private $mapper;

    /**
     * @var object
     */
    private $object;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param ValidatorInterface $validator
     * @param TranslatorInterface $translator
     */
    public function __construct(ValidatorInterface $validator, TranslatorInterface $translator)
    {
        $this->validator = $validator;
        $this->translator = $translator;
    }

    /**
     * @param AbstractRequestMapper $mapper
     * @param object $object
     * @return FormManager
     */
    public function createForm(AbstractRequestMapper $mapper, $object): FormManager
    {
        $this->mapper = $mapper->setObject($object);
        $this->object = $object;

        return $this;
    }

    /**
     * @param Request $request
     * @return FormManager
     * @throws FormManagerException
     */
    public function submit(Request $request): FormManager
    {
        $this
            ->mapData($request->query->all())
            ->mapData($request->request->all());

        return $this;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     */
    public function isValid(): bool
    {
        $violations = $this->validator->validate($this->mapper);

        if (\count($violations) > 0) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $this->errors[$violation->getPropertyPath()] = $this->translator
                    ->trans($violation->getMessage(), $violation->getParameters(), 'core');
            }
        }

        $isValid = \count($this->errors) <= 0;

        if (true === $isValid) {
            $this->object = $this->mapper->map();
        }

        return $isValid;
    }


    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $data
     * @return FormManager
     * @throws FormManagerException
     */
    private function mapData(array $data): FormManager
    {
        if (null === $this->mapper) {
            throw new FormManagerException('Object instance not set.');
        }

        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (false === method_exists($this->mapper, $method)) {
                continue;
            }

            $this->mapper->{$method}($value);
        }

        return $this;
    }
}