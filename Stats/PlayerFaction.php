<?php
namespace Odl\ShadowBundle\Stats;

/**
/** @mongodb:EmbeddedDocument
 */
class PlayerFaction
	extends Faction
{
	/**
	 * @mongodb:collection
	 */
	public $overAll;

	public function __construct($name, Faction $overAll)
	{
		$this->name = $name;
		$this->overAll = $overAll;
	}

	public function getRelativeChanceToWin()
	{
		$self = $this->getChanceToWin();
		$overAll = $this->overAll->getChanceToWin();

		if ($overAll > 0)
		{
			$percentage = $self / $overAll * 100;
			return ceil($percentage);
		}

		return 0;
	}

	public function getRelativeChanceToLive()
	{
		$self = $this->getChanceToLive();
		$overAll = $this->overAll->getChanceToLive();

		if ($overAll > 0)
		{
			$percentage = $self / $overAll * 100;
			return ceil($percentage);
		}

		return 0;
	}
}