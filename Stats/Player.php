<?php
namespace Odl\ShadowBundle\Stats;

/**
 * @mongodb:Document(db="shadow_hunters", collection="stats_player")
 */
class Player
	extends Stats
{
	/**
	 * @mongodb:collection
	 */
	public $factions;

	/**
	 * @mongodb:collection
	 */
	public $games;

	/**
	 * @mongodb:String
	 */
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
