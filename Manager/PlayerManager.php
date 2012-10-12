<?php
namespace Odl\ShadowBundle\Manager;

use Odl\ShadowBundle\Documents\Game;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

class PlayerManager
	extends BaseManager
{
	protected $dm;
	protected $repository;

	public function __construct(DocumentManager $dm) {
		parent::__construct($dm, 'Odl\ShadowBundle\Documents\PlayerCharacter');
	}
}
