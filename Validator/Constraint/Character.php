<?php
namespace Odl\ShadowBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraints\Constraint;

class Chracter
	extends Constraint
{
	public $charname = '';
    public $message = 'Character "{{ $charname }}" does not exist in database';
}