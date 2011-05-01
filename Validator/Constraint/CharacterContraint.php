<?php
namespace Odl\ShadowBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraints\Constraint;

class ChracterConstraint
	extends Constraint
{

    public $message = 'Character "{{ charname }}" does not exist in database';

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}