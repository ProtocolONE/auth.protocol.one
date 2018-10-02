<?php

namespace Core\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class EmailValidator
 * @category GSG
 * @package Core\Validator
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 */
class EmailValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param Constraint|Email $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}