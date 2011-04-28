<?php
namespace Odl\ShadowBundle\Stats;

/**
 * @mongodb:Document(db="shadow_hunters", collection="stats_faction")
 */
class Faction
	extends Stats
{
	/**
	 * @mongodb:String
	 */
	public $class = "faction";
}
