<?php
namespace Odl\ShadowBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 */
class Character
	extends Constraint
{
    public $charset = 'UTF-8';
    public $message = 'Character {{ charname }} does not exist in database';

    public function __construct(){
    	parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
    	return 'sh_character';
    }
}