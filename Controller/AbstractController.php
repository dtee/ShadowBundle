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
    protected function getGameResponse() {

        $request = $this->get('request');
        $manager = $this->get('shadow.manager');

        $response = new Response();
//         $lastModifedDate = $manager->getLastModifiedGameTime();
//         $response->setLastModified($lastModifedDate);
//         $response->isNotModified($request);

        return $response;
    }
}
