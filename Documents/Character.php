<?php
namespace Odl\ShadowBundle\Documents;

/**
 * @mongodb:Document(db="shadow_hunters", collection="char")
 */
class Character
{
	/**
	 * @mongodb:id(strategy="NONE")
	 */
	protected $name;

	/**
	 * @mongodb:String
	 */
	protected $imageUrl;

	/**
	 * @mongodb:int
	 */
	protected $hitPoint;

	/**
	 * @mongodb:String
	 * @assert:Choice(
     *     choices = { "shadow", "hunter", "netural"},
     *     message = "Choose a faction."
     * )
	 */
	protected $faction;	// shadow/hunter/netural

	/**
	 * @mongodb:String
	 */
	protected $description;

	/**
	 * @mongodb:String
	 */
	protected $ability;
	
	/**
	 * @mongodb:String
	 */
	protected $winCondition;
	
	public function __construct($name, $faction, $hitPoint)
	{
		$this->name = $name;
		$this->hitPoint = $hitPoint;
		$this->faction = $faction;
	}

	public function getFaction()
	{
		return $this->faction;
	}

	public function getName()
	{
		return $this->name;
	}
}
