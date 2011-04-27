<?php
namespace Odl\ShadowBundle\Stats;
abstract class Stats
{
	public $name;
	public $totalPlayed = 0;
	public $totalWin = 0;
	public $totalAlive = 0;
	public $class;
	
	public function __construct($name)
	{
		$this->name = $name;
	}
	
	public function getPercentage()
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