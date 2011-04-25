<?php
namespace Odl\ShadowBundle\Documents;

/**
 * @mongodb:Document(db="shadow_hunters", collection="game") 
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
	 * @mongodb:EmbedMany(targetDocument="PlayerCharacter")
	 */
	protected $players;
	
	protected $winners;
	protected $factions;
	
	public function __construct($playTime)
	{
		$this->playTime = $playTime;
	}
	
	public function getPlayTime()
	{
		return $this->playTime;
	}
	
	/**
	 * @param PlayerChracter $p
	 */
	public function addPlayer(PlayerCharacter $p)
	{
		$this->players[] = $p;
		$faction = $p->char->getFaction();
		if ($p->isWin)
		{
			$this->winners[] = $p;
			$this->factions[$faction] = 1;
		}
		else
		{
			// One netural win all neturals?
			if (!isset($this->factions[$faction]))
			{
				$this->factions[$faction] = 0;
			}
		}
	}
	
	public function getPlayers()
	{
		return $this->players;
	}
	
	public function getWinners()
	{
		return $this->winners;
	}
	
	public function getPlayedFactions()
	{
		return $this->factions;
	}
}
