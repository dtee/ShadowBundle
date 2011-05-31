<?php
namespace Odl\ShadowBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class BalancedTeam
	extends Constraint
{
    public $message = 'Team is not balanced: {{ hunters }} hunters - {{ shadows }} shadows';

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
    	return 'sh_balanced_team';
    }
}