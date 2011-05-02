<?php
namespace Odl\AssetBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use VendorName\MyBundle\Entity\User;
use Odl\ShadowBundle\Parser\Parser;

class LoadGameData implements FixtureInterface
{
    public function load($manager)
    {
    	$games = Parser::loadGamesFromCSV();
    	foreach ($games as $game)
    	{
        	$manager->persist($game);
    	}

        $manager->flush();
    }
}