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
	private $playerStatss;
	private $players;
	private $factions;
	private $chars;

	public function __construct(array $games)
	{
		$this->games = $games;
	}

	protected function getFaction($name)
	{
		if (!isset($this->factions[$name]))
		{
			$this->factions[$name] = new Faction($name);
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
				$players = $game->getPlayers();
				foreach ($players as $player)
				{
					$factionName = $player->getChar()->getFaction();
					$faction = $this->getFaction($factionName);
					$faction->totalPlayed++;

					if ($player->isWin) {
						$faction->totalWin++;
					}

					if ($player->isAlive)
					{
						$faction->totalAlive++;
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
					$playerStats = $this->getChar($player->getCharacter());
					$playerStats->totalPlayed ++;
					if ($player->isAlive) {
						$playerStats->totalAlive++;
					}

					if ($player->isWin) {
						$playerStats->totalWin++;
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
			$factionStats = $this->getFactionStats();
			$this->players[$name] = new Player($name, $factionStats);
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
					$playerStats = $this->getPlayer($player->getUsername());
					$faction = $player->char->getFaction();

					$playerStats->totalPlayed ++;
					$playerStats->factions[$faction]->totalPlayed ++;
					
					if ($player->isAlive) {
						$playerStats->totalAlive++;
						$playerStats->factions[$faction]->totalAlive ++;
					}

					$playerStats->games[] = $game;

					if ($player->isWin) {
						$playerStats->totalWin++;
						$playerStats->factions[$faction]->totalWin ++;
					}
				}
			}

			ksort($this->players);
		}

		return $this->players;
	}
}