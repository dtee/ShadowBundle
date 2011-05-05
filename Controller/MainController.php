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

class MainController
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

	/**
	 * @Route("");
	 * @Template()
	 */
	public function indexAction()
	{
		$key = 'games_' . count($this->games);
		if (function_exists('apc_fetch'))
		{
			$cache = new \Doctrine\Common\Cache\ApcCache();
		}
		else
		{
			$cache = new \Doctrine\Common\Cache\ArrayCache();
		}

		if (!$charts = $cache->fetch($key))
		{
			$charts = $this->getStatsOverTime($this->games, $this->players);
			$cache->save($key, $charts);
		}

		return array(
			'games' => $this->games,
			'factions' => $this->factions,
			'charts' => $charts,
			'players' => $this->players,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats
		);
	}

	/**
	 * @Route("/char/{charName}");
	 * @Template()
	 */
	public function charAction($charName)
	{
		$formFactory = $this->get('form.factory');
		$request = $this->get('request');
		$char = $this->chars[$charName];
		$factionChoices = array_keys($this->factions);
		$factionChoices = array_combine($factionChoices, $factionChoices);

		$form = $formFactory->createBuilder('form', $char)
			->add('hitPoint', 'text')
			->add('faction', 'choice', array(
				'choices' => $factionChoices
			))
			->add('ability', 'textarea')
			->add('winCondition', 'textarea')
			->getForm();

		$content = array();
        $response = new Response();
		if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);

			if ($form->isValid()) {
				$dm = $this->get('doctrine.odm.mongodb.default_document_manager');
				$dm->persist($char);
				$dm->flush();

				$retVal = array('success' => 'Saved.');
				$content = json_encode($retVal);
			}
		}

		$params = array(
			'char' => $char,
			'charStats' => $this->charsStats[$charName],
			'formView' => $form->createView(),

			'games' => $this->games,
			'factions' => $this->factions,
			'players' => $this->players,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats,
		);

		if ($request->isXmlHttpRequest())
		{
        	$errorsProvider = $this->get('form.errors');
		    $retVal['error'] = $errorsProvider->getErrors($form);
		    $content = json_encode($retVal);
		}
		else
		{
			$content = $this->renderView(
				'ShadowBundle:Main:char.html.twig', $params);
		}

		$response->setContent($content);
		return $response;
	}

	/**
	 * @Route("/chars");
	 * @Template()
	 */
	public function charsAction()
	{
		return array(
			'games' => $this->games,
			'factions' => $this->factions,
			'players' => $this->players,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats
		);
	}

	/**
	 * @Route("/game/{id}");
	 * @Template()
	 */
	public function gameAction($id)
	{
		return array(
			'games' => $this->games,
			'factions' => $this->factions,
			'players' => $this->players,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats
		);
	}

	/**
	 * @Route("/game-edit/{id}");
	 */
	public function gameEditAction($id)
	{
		$dm = $this->get('doctrine.odm.mongodb.default_document_manager');
		$repository = $dm->getRepository('Odl\ShadowBundle\Documents\Game');

		$game = $repository->find($id);
		if ($game)
		{
			
			// Adding more players
			$request = $this->get('request');
			$form = $request->get("form");
	
			if (isset($form['players']))
			{
				$players = array_keys($form['players']);
				
				foreach ($players as $name) {
					if (startsWith($name, 'player'))
					{
						// the following generates form name with spaces
						$game->addPlayer(new PlayerCharacter($name));
					}
				}
			}
	
			return $this->handleGameEditCreate($game);
		}
		else
		{
			throw new Exception('Game ID: {$id} not found.');
		}
	}

	/**
	 * @Route("/game-create");
	 */
	public function gameCreateAction()
	{
		$request = $this->get('request');
		$form = $request->get("form");

		if (isset($form['players']))
		{
			$players = array_keys($form['players']);
		}
		else
		{
			$players = array();
			for ($i = 1; $i <= 3; $i++)
			{
				$players[] = "Player{$i}";
			}
		}

		$game = new Game(new \DateTime());
		$game->setName('Place holder');

		// Do we know how many to start with?
		foreach ($players as $name) {
			// the following generates form name with spaces
			$game->addPlayer(new PlayerCharacter($name));
		}

		return $this->handleGameEditCreate($game);
	}

	protected function handleGameEditCreate(Game $game)
	{
		$formFactory = $this->get('form.factory');
		$request = $this->get('request');

		$form = $formFactory
			->createBuilder('form', $game, array('label' => 'New game'))
			->add('playTime', 'date')
			->add('players', 'collection', array(
				'type' => new PlayerCharacterType(),
			))
			->getForm();

        $response = new Response();
		if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);

			if ($form->isValid()) {
				$dm = $this->get('doctrine.odm.mongodb.default_document_manager');
				$repository = $dm->getRepository('Odl\ShadowBundle\Documents\Game');

				$count = $repository->findAll()->count() + 1;
				$game->setName("Game {$count}");

				$dm->persist($game);
				$dm->flush();

				// $game;	- Save game.
   				$retVal['success'] = true;
   				$router = $this->get('router');
   				$retVal['href'] = $router->generate('odl_shadow_main_games');
			}
			else
			{
	        	$errorsProvider = $this->get('form.errors');
			    $retVal['error'] = $errorsProvider->getErrors($form);
				$content = json_encode($retVal);
			}
		}

        $params =  array(
        	'formView' => $form->createView(),
        	'playerNames' => array_keys($this->players),
        	'characterNames' => array_keys($this->chars));

		if ($request->isXmlHttpRequest())
		{
        	$errorsProvider = $this->get('form.errors');
		    $retVal['error'] = $errorsProvider->getErrors($form);
		    $content = json_encode($retVal);
		}
		else
		{
			$content = $this->renderView(
				'ShadowBundle:Main:gameCreate.html.twig', $params);
		}

		$response->setContent($content);
		return $response;
	}

	/**
	 * @Route("/games");
	 * @Template()
	 */
	public function gamesAction()
	{
		return array(
			'chars' => $this->chars,
			'factions' => $this->factions,
			'games' => array_reverse($this->games, true),
			'players' => $this->players,
			'charsStats' => $this->charsStats
		);
	}

	/**
	 * @Route("/player/{id}");
	 * @Template()
	 */
	public function playerAction($id)
	{
		return array(
			'games' => $this->games,
			'factions' => $this->factions,
			'players' => $this->players,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats,
			'player' => $this->players[$id]
		);
	}

	/**
	 * @Route("/players");
	 * @Template()
	 */
	public function playersAction()
	{
		return array(
			'games' => $this->games,
			'factions' => $this->factions,
			'players' => $this->players,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats
		);
	}

	/**
	 * Return player chance to win/survive over time
	 *
	 * @param array $games
	 * @param array $allPlayers
	 */
	public function getStatsOverTime(array $games, array $allPlayers)
	{
		$incrementGames = array();
		$seriesHash = array();
		$gameCount = array();
		$statsType = array(
			'ChanceToWin', 'RelativeChanceToWin',
			'ChanceToLive', 'RelativeChanceToLive');

		$symbols = array(
			'shadow'=> 'red',
			'hunter' => 'green',
			'neutral' => 'gray'
		);

		$index = 0;
		foreach ($games as $game)
		{
			$index++;
			$incrementGames[] = $game;
			$statProvider = new StatsProvider($incrementGames);
			$playersStats = $statProvider->getPlayerStats();

			// Init defaults

			// Keep track of total games played
			foreach ($game->getPlayers() as $player)
			{
				$playerName = $player->getUsername();
				$faction = $player->getChar()->getFaction();
				$factionColor = $symbols[$faction];

				if (!isset($gameCount[$playerName]))
				{
					$gameCount[$playerName] = 0;
				}

				$gameCount[$playerName] += 1;
				$playerStats = $playersStats[$playerName];
				$seriesHash['Chance To Win'][$playerName][] = array(
					'x' => $index,
					'y' => $playerStats->getChanceToWin(),
					'factionColor' => $factionColor);

				$seriesHash['Chance To Live'][$playerName][] = array(
					'x' => $index,
					'y' => $playerStats->getChanceToLive(),
					'factionColor' => $factionColor);

				foreach ($playerStats->factions as $factionStats)
				{
					$factionName = ucfirst($factionStats->name);
					$seriesHash['Chance To Win - ' . $factionName][$playerName][] = array(
						'x' => $index,
						'y' => $factionStats->getChanceToWin(),
						'factionColor' => $factionColor);

					$seriesHash['Relative Chance To Win - ' . $factionName][$playerName][] = array(
						'x' => $index,
						'y' => $factionStats->getRelativeChanceToWin(),
						'factionColor' => $factionColor);
				}
			}
		}

		// Drop low game count players + sort by names
		foreach ($gameCount as $playerName => $total)
		{
			if ($total < 7)
			{
				foreach ($seriesHash as $key => $type)
				{
					// Remove players with less than 8 games
					unset($seriesHash[$key][$playerName]);
					ksort($seriesHash[$key]);
				}
			}
		}

		$retVal = array();
		$categories= range(1, count($games));
		foreach ($seriesHash as $title => $series)
		{
			$key = str_replace(' ', '_', $title);
			$key = strtolower($key);
			$retVal[$key] = new Chart($title, $series, $categories);
		}

		return $retVal;
	}
}
