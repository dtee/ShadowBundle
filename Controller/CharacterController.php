<?php
namespace Odl\ShadowBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

class CharacterController
	extends AbstractController
{
	/**
	 * @Route("/edit/{charName}");
	 * @Template()
	 */
	public function charAction($charName)
	{
    	$manager = $this->get('shadow.manager');
		$formFactory = $this->get('form.factory');
		$request = $this->get('request');

    	$chars = $manager->getCharacters();
		$games = $manager->getAllGames();
        $statProvider = new StatsProvider($games);
        $factions = $statProvider->getFactionStats();
		$char = $chars[$charName];

		$factionChoices = array_keys($factions);
		$factionChoices = array_combine($factionChoices, $factionChoices);

		$form = $formFactory->createBuilder('form', $char)
			->add('hitPoint', 'text')
			->add('faction', 'choice', array(
				'choices' => $factionChoices
			))
			->add('abilityName', 'text', array(
				'label' => 'Ability Name'
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
		if ($request->isXmlHttpRequest())
		{
        	$errorsProvider = $this->get('form.errors');
		    $retVal['error'] = $errorsProvider->getErrors($form);
		    $content = json_encode($retVal);
		}
		else
		{
		    $charStats = $statProvider->getCharacterStats();
		    $charStats = $charStats[$charName];
    		$params = array(
    			'formView' => $form->createView(),
    		    'charStats' => $charStats,
    		    'char' => $char
    		);

			$content = $this->renderView(
				'ShadowBundle:Character:character.html.twig', $params);
		}

		$response->setContent($content);
		return $response;
	}

	/**
	 * @Route("/");
	 * @Template()
	 */
	public function indexAction()
	{
    	$manager = $this->get('shadow.manager');
		$response = parent::getGameResponse();

		if (!$response->isEmpty())
		{
    		$renderer = $this->get('grid.renderer.jq_grid');
    		$gridSource = $this->get('grid.source.character');
    		$renderer->bind($gridSource);

    		$games = $manager->getAllGames();
            $statProvider = new StatsProvider($games);
        	$view = 'ShadowBundle:Character:index.html.twig';

    		$content = $this->renderView($view, array(
    			'grid' => $renderer,
    			'chars' => $manager->getCharacters(),
    			'charsStats' => $statProvider->getCharacterStats()
    		));

    		$response->setContent($content);
		}

		return $response;
	}
}

