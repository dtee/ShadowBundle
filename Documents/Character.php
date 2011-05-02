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
	 * @assert:Min(8)
	 * @assert:Max(14)
	 * @assert:Type( 
	 * 		type = "numeric",
	 * 		message = "Hit point must be a number"
	 * )
	 */
	protected $hitPoint;

	/**
	 * @mongodb:String
	 * @assert:Choice(
     *     choices = { "shadow", "hunter", "neutral"},
     *     message = "Choose a valid faction"
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
	
	/**
	 * @return the $imageUrl
	 */
	public function getImageUrl() {
		return $this->imageUrl;
	}

	/**
	 * @return the $hitPoint
	 */
	public function getHitPoint() {
		return $this->hitPoint;
	}

	/**
	 * @return the $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return the $ability
	 */
	public function getAbility() {
		return $this->ability;
	}

	/**
	 * @return the $winCondition
	 */
	public function getWinCondition() {
		return $this->winCondition;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $imageUrl
	 */
	public function setImageUrl($imageUrl) {
		$this->imageUrl = $imageUrl;
	}

	/**
	 * @param field_type $hitPoint
	 */
	public function setHitPoint($hitPoint) {
		$this->hitPoint = $hitPoint;
	}

	/**
	 * @param field_type $faction
	 */
	public function setFaction($faction) {
		$this->faction = $faction;
	}

	/**
	 * @param field_type $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param field_type $ability
	 */
	public function setAbility($ability) {
		$this->ability = $ability;
	}

	/**
	 * @param field_type $winCondition
	 */
	public function setWinCondition($winCondition) {
		$this->winCondition = $winCondition;
	}

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
