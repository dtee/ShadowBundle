<?php
namespace Odl\ShadowBundle\Validator\Constraints;

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
		$this->dm = $dm;
	}

	public function isValid($value, Constraint $constraint)
    {
    	if (!$value)
    		return true;

    	$repository = $this->dm->getRepository('Odl\ShadowBundle\Documents\Character');
    	$char = $repository->find($value);

        if (!$char) {
            $this->setMessage($constraint->message, array(
            	'{{ charname }}' => $value
            ));
            return false;
        }

        return true;
    }
}