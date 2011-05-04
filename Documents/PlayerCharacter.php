<?php
namespace Odl\ShadowBundle\Documents;

/**
/** @mongodb:EmbeddedDocument
 */
class PlayerCharacter
{
	/**
	 * @mongodb:String
	 * @mongodb:Index
	 * @assert:NotBlank()
	 * @assert:MinLength(3)
	 */
	protected $username;

	/**
	 * @mongodb:String
	 * @mongodb:Index
	 * @assert:NotBlank()
	 * @assert:MinLength(3)
	 * @assert:sh_character
	 */
	protected $character;

	/**
	 * @mongodb:boolean
	 */
	public $isWin;

	/**
	 * @mongodb:boolean
	 */
	public $isAlive;

	/**
	 * @mongodb:boolean
	 */
	public $isLastDeath;

	/**
	 * @mongodb:NotSaved
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
