<?php
namespace Odl\ShadowBundle\Controller;

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
		return array(
			'games' => $this->games,
			'factions' => $this->factions,
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
		return array(
			'games' => $this->games,
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
		return array(
			'games' => $this->games,
			'factions' => $this->factions,
			'players' => $this->players,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats
		);
	}
}
