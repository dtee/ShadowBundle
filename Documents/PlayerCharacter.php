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
	 * @assert:NotBlank
	 */
	protected $userId;

	/**
	 * @mongodb:String
	 * @mongodb:Index
	 * @assert:NotBlank
	 */
	protected $username;

	/**
	 * @mongodb:String
	 * @mongodb:Index
	 * @assert:NotBlank
	 */
	protected $character;

	/**
	 * @mongodb:boolean
	 * @assert:NotBlank
	 */
	public $isWin;

	/**
	 * @mongodb:boolean
	 * @assert:NotBlank
	 */
	public $isAlive;

	/**
	 * @mongodb:boolean
	 * @assert:NotBlank
	 */
	public $isLastDeath;

	/**
	 * @mongodb:NotSaved
	 */
	public $char;

	public function __construct($username, $character)
	{
		$this->username = $username;
		$this->character = $character;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getCharName()
	{
		return $this->character;
	}

	public function getCharacter()
	{
		return $this->char;
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
