<?php

namespace Sparkling\VATBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidNumber extends Constraint
{
    public $message = 'This is not a valid VAT-number';
    public $ignoreInvalidCountry = false;

    public function getDefaultOption()
    {
        return 'ignoreInvalidCountry';
    }

    public function validatedBy()
    {
        return 'vat.validator';
    }
}
