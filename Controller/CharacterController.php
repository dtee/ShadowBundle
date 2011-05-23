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
		$dm = $this->get('doctrine.odm.mongodb.default_document_manager');
		$documentName = 'Odl\ShadowBundle\Documents\Character';
		$renderer = $this->get('grid.renderer.jq_grid');
		
		$gridSource = new DocumentGridSource($dm, $documentName);
		$renderer->bind($gridSource);
		return array(
			'grid' => $renderer,
			'chars' => $this->chars,
			'charsStats' => $this->charsStats
		);
	}
}

