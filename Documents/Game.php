<?php
namespace Odl\ShadowBundle\Documents;

use Odl\ShadowBundle\Documents\PlayerCharacter;

/**
 * @mongodb:Document(db="shadow_hunters", collection="game")
 * @assertShadow:BalancedTeam()
 */
class Game
{
	/**
	 * @mongodb:id
	 */
	protected $id;

	/**
	 * @mongodb:Date
	 */
	protected $playTime;

	/**
	 * @mongodb:String
	 */
	protected $summary;

	/**
	 * @mongodb:String
	 * @mongodb:Index(unique=true, order="asc")
	 * @assert:NotBlank()
	 * @assert:MinLength(3)
	 */
	protected $name;

	/**
	 * @var PlayerCharacter
	 * @mongodb:EmbedMany(targetDocument="PlayerCharacter")
	 */
	protected $players;

	public function __construct()
	{
		$this->playTime = new \DateTime();
		$this->players = array();
	}

	public function getPlayTime()
	{
		return $this->playTime;
	}

	public function setPlayTime($playTime)
	{
		$this->playTime = $playTime;
	}

	/**
	 * @param PlayerChracter $p
	 */
	public function addPlayer(PlayerCharacter $p)
	{
		$this->players[$p->getUsername()] = $p;
		ksort($this->players);
	}

	/**
	 * Set players
	 *
	 */
	public function setPlayers(array $players)
	{
		$this->players = $players;
	}

	public function getPlayers()
	{
		return $this->players;
	}
	/**
	 * @return the $summary
	 */
	public function getSummary()
	{
		return $this->summary;
	}

	/**
	 * @param field_type $summary
	 */
	public function setSummary($summary)
	{
		$this->summary = $summary;
	}
	/**
	 * @return the $name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @assert:False(message = "Player much not appear more than once")
	 */
	public function isDuplicatePlayers()
	{
		$cache = array();
		foreach ($this->getPlayers() as $player)
		{
			$playerUsername = $player->getUsername();
			if (!$playerUsername)
			{
				continue;
			}

			if (isset($cache[$playerUsername]))
			{
				return true;
			}

			$cache[$playerUsername] = 1;
		}

		return false;
	}

	/**
	 * @assert:False(message = "Character must not appear more than once")
	 */
	public function isDuplicateCharacters()
	{
		$cache = array();
		foreach ($this->getPlayers() as $player)
		{
			$charname = $player->getCharacter();
			if (!$charname)
			{
				continue;
			}

			if (isset($cache[$charname]))
			{
				return true;
			}

			$cache[$charname] = 1;
		}

		return false;
	}

	/**
	 *
	 */
	public function isFactionBalanced() {
		$factions = array();
		foreach ($this->getPlayers() as $player)
		{
			$faction = $player->getFaction();

			if (!$faction) {
				continue;
			}

			$cache[$faction][] = $player;
		}

		return count($cache['hunter']) == count($cache['shadow']);
	}

	public function __toString(){
		return $this->name;
	}
	/**
	 * @return the $id
	 */
	public function getId()
	{
		return $this->id;
	}

}
