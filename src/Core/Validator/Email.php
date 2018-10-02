<?php

namespace Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Email
 * @category GSG
 * @package Core\Validator
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @Annotation
 */
class Email extends Constraint
{
    /**
     * @var string
     */
    public $message = '';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return self::class.'Validator';
    }
}