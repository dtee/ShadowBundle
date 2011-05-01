<?php
namespace Odl\ShadowBundle\Stats;

/**
 * @mongodb:Document(db="shadow_hunters", collection="stats_char")
 */
class Char
	extends Stats
{
	/**
	 * @mongodb:String
	 */
	public $class = 'char';
}
