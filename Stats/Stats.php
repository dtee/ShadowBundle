<?php
namespace Odl\ShadowBundle\Stats;

/** @mongodb:MappedSuperclass */
abstract class Stats
{
	/**
	 * @mongodb:String
	 */
	public $name;

	/**
	 * @mongodb:Integer
	 */
	public $totalPlayed = 0;

	/**
	 * @mongodb:Integer
	 */
	public $totalWin = 0;

	/**
	 * @mongodb:Integer
	 */
	public $totalAlive = 0;

	/**
	 * @mongodb:String
	 */
	public $class;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getChanceToWin()
	{
		if ($this->totalPlayed > 0)
		{
			$percentage = $this->totalWin / $this->totalPlayed * 100;
			return ceil($percentage);
		}

		return 0;
	}

	public function getChanceToLive()
	{
		if ($this->totalPlayed > 0)
		{
			$percentage = $this->totalAlive / $this->totalPlayed * 100;
			return ceil($percentage);
		}

		return 0;
	}
}