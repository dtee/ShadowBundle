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


class MainController extends AbstractController
{

    /**
     * @Route("test");
     * @Template()
     */
    public function test()
    {
        $container = $this->container;
        $domain = $container->getParameter('root_domain');

        $ids = $container->getServiceIds();
        $assetsHelper = $this->get('templating.helper.assets');

        $game = new Game();
        $game->setName('test2');
        $validator = $this->get('validator');

        $player = new PlayerCharacter();
        $player->setCharacter('fu-ka');
        $player->setUsername('dtee');
        $game->addPlayer($player);

        $player = new PlayerCharacter();
        $player->setCharacter('unknown');
        $player->setUsername('slert');
        $game->addPlayer($player);

        $errorList = $validator->validate($game);
        $dm = $this->get('doctrine.odm.mongodb.default_document_manager');
        foreach ( $this->games as $game )
        {
            $game->setCreatedAt(new \DateTime());
            $dm->persist($game);
        }
        $dm->flush();

        ve($errorList);
    }

    /**
     * @Route("");
     * @Template()
     */
    public function indexAction()
    {
        $response = parent::getGameResponse();
        if (!$response->isEmpty())
        {
            $chartProvider = $this->get('shadow.chart_provider');
            $charts = $chartProvider->getPlayerStatsOverTime();
            $charts['faction'] = $chartProvider->getPlayerFactionDistribution();
            $charts['characters'] = $chartProvider->getCharacterWinDistribution();

            $view = 'OdlShadowBundle:Main:index.html.twig';
            $content = $this->renderView($view, array(
                    'charts' => $charts
            ));

            $response->setContent($content);
        }

        return $response;
    }

    /**
     * @Route("/game-edit/{id}");
     *
     * @Secure(roles="ROLE_USER, ROLE_FOO, ROLE_ADMIN")
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
     * @Route("/game-create");
     *
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
                $retVal['href'] = $router->generate('odl_shadow_main_games');
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

            $content = $this->renderView('OdlShadowBundle:Main:gameCreate.html.twig', $params);
        }

        $response->setContent($content);
        return $response;
    }

    /**
     * @Route("/player/{id}");
     * @Template()
     */
    public function playerAction($id)
    {
        $manager = $this->get('shadow.manager');
        $response = parent::getGameResponse();

        if (!$response->isEmpty())
        {
            $manager = $this->get('shadow.manager');
            $games = $manager->getAllGames();
            $statProvider = new StatsProvider($games);

            $view = 'OdlShadowBundle:Main:player.html.twig';
            $players = $statProvider->getPlayerStats();
            $content = $this->renderView($view, array(
    			'players' => $players,
				'player' => $players[$id]
    		));

    		$response->setContent($content);
		}

		return $response;
	}

	/**
	 * @Route("/players");
	 * @Template()
	 */
	public function playersAction()
	{
    	$manager = $this->get('shadow.manager');
		$response = parent::getGameResponse();
		if (!$response->isEmpty())
		{
        	$manager = $this->get('shadow.manager');
        	$games = $manager->getAllGames();
        	$statProvider = new StatsProvider($games);

        	$view = 'OdlShadowBundle:Main:players.html.twig';
    		$content = $this->renderView($view, array(
    			'players' => $statProvider->getPlayerStats(),
    		));

    		$response->setContent($content);
		}

		return $response;
	}
}
