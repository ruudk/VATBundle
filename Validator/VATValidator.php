<?php

namespace Sparkling\VATBundle\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Sparkling\VATBundle\Exception\VATException;

class VATValidator extends ConstraintValidator
{
    /**
     * @var \Sparkling\VATBundle\Service\VATService
     */
    protected $vat;

    public function __construct($vat)
    {
        $this->vat = $vat;
    }

    public function validate($value, Constraint $constraint)
    {
        try {
            if($value == '' || $this->vat->validate($value))

            return true;
        } catch (VATException $exception) {

        }

        $this->context->addViolation($constraint->message);

        return false;
    }
}
