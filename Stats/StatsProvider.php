<?php
namespace Odl\ShadowBundle\Stats;

/**
 * Takes array of games and figure out various stats
 * 
 * @author dtee
 */
class StatsProvider
{
	private $games;
	private $chars;
	private $players;
	private $factions;
	
	public function __construct(array $games)
	{
		$this->games = $games;
	}
	
	protected function getFaction($name)
	{
		if (!isset($this->factions[$name]))
		{
			$this->factions[$name] = new Char($name);
		}
		
		return $this->factions[$name];
	}
	
	/**
	 * Return Faction stats based on games played
	 * 
	 * @param array $games
	 */
	public function getFactionStats()
	{
		if (!$this->factions)
		{
			foreach ($this->games as $game)
			{
				$factions = $game->getPlayedFactions();
				foreach ($factions as $factionName => $isWin)
				{
					$faction = $this->getFaction($factionName);
					$faction->totalPlayed++;
					
					if ($isWin)
					{
						$faction->totalWin++;
					}
				}
			}
			
			ksort($this->factions);
		}
			
		return $this->factions;
	}
	
	protected function getChar($name)
	{
		if (!isset($this->chars[$name]))
		{
			$this->chars[$name] = new Char($name);
		}
		
		return $this->chars[$name];
	}
	
	public function getCharacterStats()
	{
		if (!$this->chars)
		{
			foreach ($this->games as $game)
			{
				$players = $game->getPlayers();
				foreach ($players as $player)
				{
					$char = $this->getChar($player->getCharName());
					$char->totalPlayed ++;
					if ($player->isAlive) {
						$char->totalAlive++;
					}
					
					if ($player->isWin) {
						$char->totalWin++;
					}
				}
			}
			
			ksort($this->chars);
		}
		
		return $this->chars;
	}
	
	protected function getPlayer($name)
	{
		if (!isset($this->players[$name]))
		{
			$this->players[$name] = new Player($name);
		}
		
		return $this->players[$name];
	}
	
	public function getPlayerStats()
	{
		if (!$this->players)
		{
			foreach ($this->games as $game)
			{
				$players = $game->getPlayers();
				foreach ($players as $player)
				{
					$char = $this->getPlayer($player->getUsername());
					$faction = $player->char->getFaction();
					
					$char->totalPlayed ++;
					$char->factions[$faction]->totalPlayed ++;
					if ($player->isAlive) {
						$char->totalAlive++;
					}
					
					$char->games[] = $game;
					
					if ($player->isWin) {
						$char->totalWin++;
						$char->factions[$faction]->totalWin ++;
					}
				}
			}
			
			ksort($this->players);
		}
		
		return $this->players;
	}
}