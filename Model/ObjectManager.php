<?php
namespace Odl\ShadowBundle\Model;

use Doctrine\ODM\MongoDB\DocumentManager;

class ObjectManager
{
    private $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function getCharacterRepository()
    {
        return $this->dm->getRepository('Odl\ShadowBundle\Documents\Character');
    }

    public function getGameRepository()
    {
        return $this->dm->getRepository('Odl\ShadowBundle\Documents\Game');
    }

    private $games;

    public function getAllGames()
    {
        if (!$this->games)
        {
            $all = $this->getGameRepository()
                ->findAll()
                ->sort(array(
                    'playTime' => 'asc',
                    'name' => 'asc'
            ));

            $chars = $this->getCharacters();
            foreach ( $all as $game )
            {
                $this->games[$game->getName()] = $game;
                foreach ( $game->getPlayers() as $player )
                {
                    $player->char = $chars[$player->getCharacter()];
                }
            }
        }

        return $this->games;
    }

    /**
     * Wrapper function which also sets chars
     *
     * @param unknown_type $id
     */
    public function getGame($id) {
        $game = $this->getGameRepository()->find($id);
        if ($game) {
            $chars = $this->getCharacters();
            foreach ( $game->getPlayers() as $player )
            {
                $player->char = $chars[$player->getCharacter()];
            }
        }

        return $game;
    }

    public function getLastModifiedGameTime()
    {
        $game = $this->dm->createQueryBuilder('Odl\ShadowBundle\Documents\Game')
            ->sort('updatedAt', 'desc')
            ->getQuery()
            ->getSingleResult();

        if ($game)
        {
            return $game->getUpdatedAt();
        }

        return new \DateTime();
    }

    private $chars = array();

    public function getCharacters()
    {
        if (!$this->chars)
        {
            $all = $this->getCharacterRepository()
                ->findAll();

            foreach ( $all as $char )
            {
                $this->chars[$char->getName()] = $char;
            }
        }

        return $this->chars;
    }

    public function getCharacterByName($name)
    {
        return $this->getCharacterRepository()
            ->findOne($name);
    }
}