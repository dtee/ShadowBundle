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
			$players = $statProvider->getPlayerStats();

			// Init defaults
			foreach ($allPlayers as $player)
			{
				if ($index > 0)
				{
					$series[$player->name][$index] = $series[$player->name][$index- 1];
				}
				else
				{
					$series[$player->name][$index] = 0;
				}
			}

			// Keep track of totla games played
			foreach ($game->getPlayers() as $player)
			{
				if (!isset($gameCount[$player->getUsername()]))
				{
					$gameCount[$player->getUsername()] = 0;
				}

				$gameCount[$player->getUsername()] += 1;
			}

			foreach ($players as $player)
			{
				$series[$player->name][$index] = $player->getChanceToWin();
			}
		}

		foreach ($gameCount as $playerName => $total)
		{
			if ($total < 8)
			{
				unset($series[$playerName]);
			}
		}

		$retVal = array();
		$retVal['chance_to_win'] = new Chart('Win Rate', $series);

		return $retVal;
	}
}
