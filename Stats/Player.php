<?php
namespace Odl\ShadowBundle\Stats;

class Player
	extends Stats
{
	public $totalAlive = 0;
	public $factions;
	public $games;
	public $class = "player";

	public function __construct($name, array $factionStats)
	{
		foreach ($factionStats as $faction)
		{
			$factionName = $faction->name;
			$this->factions[$factionName] = new PlayerFaction($factionName, $faction);
		}

		parent::__construct($name);
	}
}
