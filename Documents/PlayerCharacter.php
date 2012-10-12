<?php
namespace Odl\ShadowBundle\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Odl\ShadowBundle\Validator\Constraints as AssertShadow;

/**
/** @ODM\EmbeddedDocument
 */
class PlayerCharacter
{
	/**
	 * @ODM\String
	 * @ODM\Index
	 *
	 * @Assert\NotBlank()
	 * @Assert\MinLength(3)
	 */
	protected $username;

	/**
	 * @ODM\String
	 * @ODM\Index
	 *
	 * @Assert\NotBlank()
	 * @Assert\MinLength(3)
	 *
	 * @AssertShadow\Character()
	 */
	protected $character;

	/**
	 * @ODM\boolean
	 */
	public $isWin;

	/**
	 * @ODM\boolean
	 */
	public $isAlive;

	/**
	 * @ODM\boolean
	 */
	public $isLastDeath;

	/**
	 * @ODM\NotSaved
	 */
	public $char;

	public function __construct($username = null, $character = null)
	{
		$this->username = $username;
		$this->character = $character;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}

	public function getCharacter()
	{
		return $this->character;
	}

	public function setCharacter($character)
	{
		$this->character = $character;
	}

	/**
	 * @return the $userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return the $isWin
	 */
	public function getIsWin()
	{
		return $this->isWin;
	}

	/**
	 * @return the $isAlive
	 */
	public function getIsAlive()
	{
		return $this->isAlive;
	}

	/**
	 * @return the $isLastDeath
	 */
	public function getIsLastDeath()
	{
		return $this->isLastDeath;
	}

	/**
	 * @return the $char
	 */
	public function getChar()
	{
		return $this->char;
	}

}
