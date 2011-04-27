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
		$this->players[$p->getUsername()] = $p;
		ksort($this->players);
	}
	
	public function getPlayers()
	{
		return $this->players;
	}
}
