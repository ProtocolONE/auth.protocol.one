<?php

namespace Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueData
 * @category GSG
 * @package Core\Validator
 * @copyright Copyright (с) 2018, ProtocolOne and/or affiliates. All rights reserved.
 * @author Vadim Sabirov <vadim.sabirov@protocol.one>
 * @version 1.0
 *
 * @Annotation
 */
class UniqueData extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This data already exist.';

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var string
     */
    public $entityClass = '';

    /**
     * @var string
     */
    public $em = null;

    /**
     * @var string
     */
    public $errorPath = '';

    /**
     * @var bool
     */
    public $isExistCheck = false;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return self::class.'Validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}