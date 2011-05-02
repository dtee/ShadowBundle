<?php
namespace Odl\AssetBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use VendorName\MyBundle\Entity\User;
use Odl\ShadowBundle\Parser\Parser;

class LoadCharacterData implements FixtureInterface
{
    public function load($manager)
    {
    	$chars = Parser::loadChars();
    	foreach ($chars as $char)
    	{
        	$manager->persist($char);
    	}

        $manager->flush();
    }
}