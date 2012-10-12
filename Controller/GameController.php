<?php
namespace Odl\ShadowBundle\Controller;

use Doctrine\Common\Util\Debug;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Odl\ShadowBundle\Documents\Character;
use Odl\ShadowBundle\Documents\PlayerCharacter;
use Odl\ShadowBundle\Form\PlayerCharacterType;
use Odl\ShadowBundle\Chart\Chart;
use Odl\ShadowBundle\Stats\Char;
use Odl\ShadowBundle\Stats\StatsProvider;
use Odl\ShadowBundle\Parser\Parser;
use Odl\ShadowBundle\Documents\Game;

use Dtc\GridBundle\Grid\Renderer\TwigGridRenderer;
use Dtc\GridBundle\Grid\Renderer\JQueryGridRenderer;
use Dtc\GridBundle\Grid\Grid;
use Dtc\GridBundle\Grid\Column\GridColumn;
use Dtc\GridBundle\Grid\Source\DocumentGridSource;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GameController
	extends AbstractController
{
	/**
	 * @Route("/list/");
	 * @Template()
	 */
	public function listAction() {
		$manager = $this->get('shadow.manager');
		$response = parent::getGameResponse();

		if (!$response->isEmpty())
		{
			$manager = $this->get('shadow.manager');
			$games = $manager->getAllGames();
			$statProvider = new StatsProvider($games);

			$renderer = $this->get('grid.renderer.jq_table_grid');
			$gridSource = $this->get('shadow.grid.source.game');
			$renderer->bind($gridSource);

			$view = 'OdlShadowBundle:Game:list.html.twig';
			$content = $this->renderView($view, array(
					'players' => $statProvider->getPlayerStats(),
					'games' => array_reverse($manager->getAllGames()),
					'grid' => $renderer
			));

			$response->setContent($content);
		}

		return $response;
	}

	/**
	 * @Route("/new/");
	 * @Template()
	 */
	public function createAction() {
		$request = $this->get('request');
		$game = new Game(new \DateTime());

		// Do we know how many to start with?
		for($i = 1; $i <= 6; $i++)
		{
			// the following generates form name with spaces
			$game->addPlayer(new PlayerCharacter("Player{$i}"));
		}

		return $this->handleGameEditCreate($game);
	}

	protected function handleGameEditCreate(Game $game)
	{
		$formFactory = $this->get('form.factory');
		$request = $this->get('request');

		$form = $formFactory->createBuilder('form', $game, array(
				'label' => 'New game',
				'csrf_protection' => false
		))
			->add('playTime', 'date')
			->add('summary', 'textarea', array('required' => false))
			->add('players', 'collection', array(
				'type' => new PlayerCharacterType(),
				'allow_add' => true,
				'allow_delete' => true,
				'prototype' => true
		))
			->getForm();

		$response = new Response();
		if ($request->getMethod() == 'POST')
		{
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$gameManager = $this->get('shadow.manager.game');
				$gameManager->save($game);

				// $game;	- Save game.
				$retVal['success'] = true;
				$router = $this->get('router');
				$retVal['href'] = $router->generate('odl_shadow_game_list');

				if (!$request->isXmlHttpRequest()) {
					return new RedirectResponse($retVal['href']);
				}
			}
			else
			{
				$errorsProvider = $this->get('form.error_provider');
				$retVal['error'] = $errorsProvider->getErrors($form);
				$content = json_encode($retVal);
			}
		}

		if ($request->isXmlHttpRequest())
		{
			$errorsProvider = $this->get('form.error_provider');
			$retVal['error'] = $errorsProvider->getErrors($form);
			$content = json_encode($retVal);
		}
		else
		{
			$manager = $this->get('shadow.manager');
			$games = $manager->getAllGames();
			$statProvider = new StatsProvider($games);
			$chars = $manager->getCharacters();

			$factionChars = array();
			foreach ($chars as $name => $char) {
				$factionChars[$name] = $char->getFaction();
			}

			$params = array(
					'formView' => $form->createView(),
					'playerNames' => array_keys($statProvider->getPlayerStats()),
					'characterNames' => $factionChars,
					'game' => $game
			);

			$content = $this->renderView('OdlShadowBundle:Game:gameCreateEdit.html.twig', $params);
		}

		$response->setContent($content);
		return $response;
	}

	/**
	 * @Route("/edit/{id}");
	 * @Template()
	 */
	public function editAction($id) {
		$gameManager = $this->get('shadow.manager.game');
		$repository = $gameManager->getRepository();
		$game = $repository->find($id);

		if ($game)
		{
			return $this->handleGameEditCreate($game);
		}
		else
		{
			throw new \Exception("Game ID: {$id} not found.");
		}
	}

	/**
	 * @Route("/view/{id}");
	 * @Template()
	 */
	public function viewAction($id) {
		$dm = $this->get('shadow.manager');

		$game = $dm->getGame($id);
		if ($game)
		{
			return array('game' => $game);
		}
		else
		{
			throw new \Exception("Game ID: {$id} not found.");
		}
	}
}
