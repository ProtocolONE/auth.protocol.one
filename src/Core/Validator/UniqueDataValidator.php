<?php

namespace Core\Validator;

use Core\Manager\AbstractRequestMapper;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Class UniqueData
 * @category GSG
 * @package Core\Validator
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class UniqueDataValidator extends ConstraintValidator
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param AbstractRequestMapper $mapper
     * @param Constraint|UniqueData $constraint
     */
    public function validate($mapper, Constraint $constraint)
    {
        if (count($constraint->fields) <= 0) {
            throw new ConstraintDefinitionException('Field for unique validation not found.');
        }

        if ('' === $constraint->entityClass || false === class_exists($constraint->entityClass)) {
            throw new ConstraintDefinitionException('Entity class set is not correct.');
        }

        $criteria = [];

        foreach ($constraint->fields as $field) {
            $method = 'get'.ucfirst($field);

            if (false === method_exists($mapper, $method)) {
                continue;
            }

            $criteria[$field] = $mapper->{$method}();
        }

        if (count($criteria) <= 0) {
            throw new ConstraintDefinitionException('Invalid columns set for unique condition selection.');
        }

        $object = $this->registry->getManager()->getRepository($constraint->entityClass)->findOneBy($criteria);
        $isValid = $constraint->isExistCheck === (null !== $object);

        if (true === $isValid) {
            return;
        }

        $errorPath = '' !== $constraint->errorPath ? $constraint->errorPath : $constraint->fields[0];

        $this->context
            ->buildViolation($constraint->message)
            ->atPath($errorPath)
            ->setParameter('{{ value }}', implode(', ', array_values($criteria)))
            ->addViolation();
    }
}