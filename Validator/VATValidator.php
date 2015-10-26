<?php

namespace Sparkling\VATBundle\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Sparkling\VATBundle\Exception\InvalidCountryCodeException;
use Sparkling\VATBundle\Exception\InvalidVATNumberException;
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
            if ($value == '' || $this->vat->validate($value)) {
                return true;
            }
        } catch (InvalidCountryCodeException $exception) {
            if ($constraint->ignoreInvalidCountry) {
                return true;
            }
            // Catch invalid country code format exception
        } catch (InvalidVATNumberException $exception) {
            // Catch invalid number format exception
        } catch (VATException $exception) {
            return true;
        }

        $this->context->addViolation($constraint->message);

        return false;
    }
}
