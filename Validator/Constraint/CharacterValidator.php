<?php
namespace Odl\ShadowBundle\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Doctrine\ODM\MongoDB\DocumentManager;

class CharacterValidator
	extends ConstraintValidator
{
	/**
	 * @var Doctrine\ODM\MongoDB\DocumentManager
	 */
	protected $dm;

	public function __construct(DocumentManager $dm) {
		ve('am i hit??');
		$this->dm = $dm;
	}

	public function isValid($value, Constraint $constraint)
    {
    	$repository = $this->dm->getRepository('Odl\ShadowBundle\Documents\Character');

    	$char = $repository->find($constraint->charname);
        if ($char->count() == 0) {
            $this->setMessage($constraint->message);
            return false;
        }

        return true;
    }
}