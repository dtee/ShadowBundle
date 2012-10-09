<?php
namespace Odl\ShadowBundle\Controller;

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

            $view = 'OdlShadowBundle:Game:list.html.twig';
            $content = $this->renderView($view, array(
                    'players' => $statProvider->getPlayerStats(),
                    'games' => array_reverse($manager->getAllGames())
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
        $form = $request->get("form");

        if (isset($form['players']))
        {
            $players = array_keys($form['players']);
        }
        else
        {
            $players = array();
            for($i = 1; $i <= 3; $i++)
            {
                $players[] = "Player{$i}";
            }
        }

        $game = new Game(new \DateTime());
        $game->setName('__newgame__');

        // Do we know how many to start with?
        foreach ( $players as $name )
        {
            // the following generates form name with spaces
            $game->addPlayer(new PlayerCharacter($name));
        }

        return $this->handleGameEditCreate($game);
    }

    protected function handleGameEditCreate(Game $game)
    {
        $formFactory = $this->get('form.factory');
        $request = $this->get('request');

        $form = $formFactory->createBuilder('form', $game, array(
                'label' => 'New game'
        ))
            ->add('playTime', 'date')
            ->add('summary', 'textarea')
            ->add('players', 'collection', array(
                'type' => new PlayerCharacterType()
        ))
            ->getForm();

        $response = new Response();
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $dm = $this->get('doctrine.odm.mongodb.default_document_manager');
                $repository = $dm->getRepository('Odl\ShadowBundle\Documents\Game');

                if ($game->getName() == '__newgame__')
                {
                    $count = $repository->findAll()
                        ->count() + 1;
                    $game->setName("Game {$count}");
                }

                $dm->persist($game);
                $dm->flush();

                // $game;	- Save game.
                $retVal['success'] = true;
                $router = $this->get('router');
                $retVal['href'] = $router->generate('odl_shadow_game_list');
            }
            else
            {
                $errorsProvider = $this->get('form.errors');
                $retVal['error'] = $errorsProvider->getErrors($form);
                $content = json_encode($retVal);
            }
        }

        if ($request->isXmlHttpRequest())
        {
            $errorsProvider = $this->get('form.errors');
            $retVal['error'] = $errorsProvider->getErrors($form);
            $content = json_encode($retVal);
        }
        else
        {
            $manager = $this->get('shadow.manager');
            $games = $manager->getAllGames();
            $statProvider = new StatsProvider($games);
            $chars = $manager->getCharacters();

            $params = array(
                    'formView' => $form->createView(),
                    'playerNames' => array_keys($statProvider->getPlayerStats()),
                    'characterNames' => array_keys($chars)
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

                foreach ( $players as $name )
                {
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