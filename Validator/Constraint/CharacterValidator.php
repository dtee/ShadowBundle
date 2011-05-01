<?php
namespace Odl\ShadowBundle\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;

class CharacterValidator 
	extends ConstraintValidator
{
	public function isValid($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            $this->setMessage($constraint->message);

            return false;
        }

        return true;
    }
}