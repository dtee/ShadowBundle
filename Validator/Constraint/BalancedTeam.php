<?php
namespace Odl\ShadowBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraints\Constraint;

class BalancedTeam
	extends Constraint
{
    public $message = 'Character does not exist in database';
}