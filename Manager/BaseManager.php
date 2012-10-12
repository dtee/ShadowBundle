<?php
namespace Odl\ShadowBundle\Manager;

use Odl\ShadowBundle\Documents\Game;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

abstract class BaseManager
{
	protected $dm;
	protected $repository;

	public function __construct(DocumentManager $dm, $documentName) {
		$this->dm = $dm;
		$this->repository = $dm->getRepository($documentName);
	}

	public function save($document) {
		$this->dm->persist($document);
		$this->dm->flush();
	}

	/**
	 * @return the $dm
	 */
	public function getDocumentManager()
	{
		return $this->dm;
	}

	/**
	 * @return the DocumentRepository
	 */
	public function getRepository()
	{
		return $this->repository;
	}
}
