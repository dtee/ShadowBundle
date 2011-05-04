<?php
namespace Odl\ShadowBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraints\Constraint;

class Player
	extends Constraint
{
    public $message = 'Player does not exist in database';
}