<?php
namespace Odl\ShadowBundle\Stats;

class Player
	extends Stats
{
	public $totalAlive = 0;
	public $factions;
	public $games;
	public $class = "player";
	
	public function __construct($name)
	{
		$factions = array('hunter', 'shadow', 'neutral');
		foreach ($factions as $factionName)
		{
			$this->factions[$factionName] = new Faction($factionName);
		}
		
		parent::__construct($name);
	}
}
