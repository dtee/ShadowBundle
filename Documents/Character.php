<?php
namespace Odl\ShadowBundle\Documents;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ODM\Document(db="shadow_hunters", collection="char")
 */
class Character
{
	/**
	 * @ODM\id(strategy="NONE")
	 */
	protected $name;

	/**
	 * @ODM\String
	 */
	protected $imageUrl;

	/**
	 * @ODM\int
	 */
	protected $hitPoint;

	/**
	 * @ODM\String
	 * @Assert\Choice(
     *     choices = { "shadow", "hunter", "neutral"},
     *     message = "Choose a valid faction."
     * )
	 */
	protected $faction;	// shadow/hunter/netural

	/**
	 * @ODM\Field(
	 * 	type="string",
	 * 	options={"label"="Description"}
	 * )
	 */
	protected $description;

	/**
	 * @ODM\Field(
	 * 	type="string",
	 * 	options={"label"="Ability Description"}
	 * )
	 */
	protected $ability;

	/**
	 * @ODM\String
	 */
	protected $abilityName;

	/**
	 * @ODM\String
	 */
	protected $winCondition;

	/**
	 * @return the $abilityName
	 */
	public function getAbilityName() {
		return $this->abilityName;
	}

	/**
	 * @param field_type $abilityName
	 */
	public function setAbilityName($abilityName) {
		$this->abilityName = $abilityName;
	}

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
