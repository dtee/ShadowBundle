<?php
namespace Odl\ShadowBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Doctrine\ODM\MongoDB\DocumentManager;

class BalancedTeamValidator
	extends ConstraintValidator
{
	/**
	 * @var Doctrine\ODM\MongoDB\DocumentManager
	 */
	protected $dm;

	public function __construct(DocumentManager $dm) {
		$this->dm = $dm;
	}

	public function isValid($game, Constraint $constraint)
    {
    	if (!$game)
    		return true;

    	$repository = $this->dm->getRepository('Odl\ShadowBundle\Documents\Character');
    	$cursor = $repository->findAll();
    	$chars = array();
    	foreach ($cursor as $char)
    	{
    		$chars[$char->getName()] = $char;
    	}

    	$hunters = $shadows = 0;
    	foreach ($game->getPlayers() as $player)
    	{
    		if (!isset($chars[$player->getCharacter()]))
    			continue;

    		$char = $chars[$player->getCharacter()];

    		if ($char->getFaction() == 'hunter')
    		{
    			$hunters++;
    		}
    		else if ($char->getFaction() == 'shadow')
    		{
    			$shadows++;
    		}
    	}

        if ($hunters != $shadows) {
           $this->setMessage($constraint->message, array(
            	'{{ hunters }}' => $hunters,
            	'{{ shadows }}' => $shadows,
            ));

            return false;
        }

        return true;
    }
}