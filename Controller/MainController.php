<?php
namespace Odl\ShadowBundle\Controller;

use Odl\ShadowBundle\Chart\Chart;

use Odl\ShadowBundle\Stats\Char;

use Odl\ShadowBundle\Stats\StatsProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Odl\ShadowBundle\Parser\Parser;
use Odl\ShadowBundle\Documents\Game;

class MainController
extends Controller
{
	protected $games;
	protected $chars;
	protected $factions;
	protected $players;
	protected $charsStats;

	public function __construct()
	{
		//$dm = $this->get('doctrine.odm.mongodb.default_document_manager');
		$this->games = Parser::loadCSVFile(null);
		$this->chars = Parser::loadChars();

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
		$charts = $this->getStatsOverTime($this->games, $this->players);

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
	 * @Route("/games");
	 * @Template()
	 */
	public function gamesAction()
	{
		$formFactory = $this->get('form.factory');

		$game = current($this->games);
		$form = $formFactory->createBuilder('form', $game)
		->add('playTime', 'date')
		->getForm();

		return array(
			'games' => $this->games,
			'formView' => $form->createView(),
			'factions' => $this->factions,
			'players' => $this->players,
			'chars' => $this->chars,
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
		$charts = $this->getStatsOverTime($this->games, $this->players);

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
	 * Return player chance to win/survive over time
	 *
	 * @param array $games
	 * @param array $allPlayers
	 */
	public function getStatsOverTime(array $games, array $allPlayers)
	{
		$incrementGames = array();
		$series = array();
		$gameCount = array();

		foreach ($games as $index => $game)
		{
			$incrementGames[] = $game;
			$statProvider = new StatsProvider($incrementGames);
			$playersStats = $statProvider->getPlayerStats();

			// Init defaults

			// Keep track of totla games played
			foreach ($game->getPlayers() as $player)
			{
				$playerName = $player->getUsername();
				if (!isset($gameCount[$playerName]))
				{
					$gameCount[$playerName] = 0;
				}

				$gameCount[$playerName] += 1;
				$playerStats = $playersStats[$playerName];
				$series[$playerName][] = array($index + 1, $playerStats->getChanceToWin());
			}
		}

		foreach ($gameCount as $playerName => $total)
		{
			if ($total < 8)
			{
				unset($series[$playerName]);
			}
		}

		$categories= range(1, count($games));

		$retVal = array();
		$retVal['chance_to_win'] = new Chart('Win Rate', $series, $categories);

		return $retVal;
	}
}
