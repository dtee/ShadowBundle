<?php
namespace Odl\ShadowBundle\Controller;

use Odl\ShadowBundle\Documents\Character;
use Odl\ShadowBundle\Documents\PlayerCharacter;
use Odl\ShadowBundle\Form\PlayerCharacterType;
use Odl\ShadowBundle\Chart\Chart;
use Odl\ShadowBundle\Stats\Char;
use Odl\ShadowBundle\Stats\StatsProvider;
use Odl\ShadowBundle\Parser\Parser;
use Odl\ShadowBundle\Documents\Game;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AbstractController 
	extends Controller
{
	public function __get($property)
	{
		if (!isset($this->$property))
		{
			$props = array('games', 'chars', 'factions', 'players', 'charsStats');
			$props = array_flip($props);

			if (!isset($props[$property]))
			{
				throw new \Exception("{$property} not found.");
			}

			$this->loadFromDB();
		}

		return $this->$property;
	}
	
	protected function loadFromDB() {
		$this->chars = array();
		$this->games = array();

		$dm = $this->get('doctrine.odm.mongodb.default_document_manager');
		$repository = $dm->getRepository('Odl\ShadowBundle\Documents\Character');

		$all = $repository->findAll();
		foreach ($all as $char)
		{
			$this->chars[$char->getName()] = $char;
		}

		$repository = $dm->getRepository('Odl\ShadowBundle\Documents\Game');

		$all = $repository->findAll()->sort(array(
			'playTime' => 'asc',
			'name' => 'asc'
		));

		foreach ($all as $game)
		{
			$this->games[$game->getName()] = $game;
			foreach ($game->getPlayers() as $player)
			{
				$player->char = $this->chars[$player->getCharacter()];
			}
		}

		$statProvider = new StatsProvider($this->games);
		$this->factions = $statProvider->getFactionStats();
		$this->charsStats = $statProvider->getCharacterStats();
		$this->players = $statProvider->getPlayerStats();
	}
	
}