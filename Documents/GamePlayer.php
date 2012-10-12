<?php
namespace Odl\ShadowBundle\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Odl\ShadowBundle\Validator\Constraints as AssertShadow;

/**
 * A Player in a game
 *
 * @ODM\Document(db="shadow_hunters", collection="game_player")
 */
class GamePlayer
{
    /**
     * @ODM\id
     */
    protected $id;

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
     * @ODM\ReferenceOne(targetDocument="Game", inversedBy="gamers")
     */
    protected $game;

    /**
     * @ODM\boolean
     */
    protected $isWin;

    /**
     * @ODM\boolean
     */
    protected $isAlive;

    /**
     * @ODM\boolean
     */
    protected $isLastDeath;

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
	/**
	 * @return the $id
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return the $game
	 */
	public function getGame()
	{
		return $this->game;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param field_type $game
	 */
	public function setGame($game)
	{
		$this->game = $game;
	}

	/**
	 * @param field_type $isWin
	 */
	public function setIsWin($isWin)
	{
		$this->isWin = $isWin;
	}

	/**
	 * @param field_type $isAlive
	 */
	public function setIsAlive($isAlive)
	{
		$this->isAlive = $isAlive;
	}

	/**
	 * @param field_type $isLastDeath
	 */
	public function setIsLastDeath($isLastDeath)
	{
		$this->isLastDeath = $isLastDeath;
	}

	/**
	 * @param field_type $char
	 */
	public function setChar($char)
	{
		$this->char = $char;
	}
}
