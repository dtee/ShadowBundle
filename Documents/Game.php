<?php
namespace Odl\ShadowBundle\Documents;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;
use Odl\ShadowBundle\Validator\Constraints as AssertShadow;

use Odl\ShadowBundle\Documents\PlayerCharacter;

/**
 * @ODM\Document(db="shadow_hunters", collection="game")
 * @AssertShadow\BalancedTeam()
 */
class Game
{
    /**
     * @ODM\id
     */
    protected $id;

    /**
     * @ODM\Date
     */
    protected $playTime;

    /**
     * @ODM\String
     */
    protected $summary;

    /**
     * @ODM\String
     * @Assert\MinLength(3)
     */
    protected $name;

    /**
     * @ODM\ReferenceMany(targetDocument="GamePlayer", mappedBy="game")
     */
    protected $gamers;

    /**
     * @var PlayerCharacter
     * @ODM\EmbedMany(targetDocument="PlayerCharacter")
     */
    protected $players;

    /**
     * @ODM\Field(type="date")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @ODM\Field(type="date")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * UserId of the person that updated this last
     *
     * @ODM\String
     */
    protected $updatedBy;

    /**
     * @return the $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return the $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param field_type $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param field_type $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function __construct()
    {
        $this->playTime = new \DateTime();
        $this->players = array();
    }

    public function getPlayTime()
    {
        return $this->playTime;
    }

    public function setPlayTime($playTime)
    {
        $this->playTime = $playTime;
    }

    /**
     * @param PlayerChracter $p
     */
    public function addPlayer(PlayerCharacter $p)
    {
        $this->players[$p->getUsername()] = $p;
        ksort($this->players);
    }

    public function removePlayer(PlayerCharacter $p) {
        unset($this->players[$p->getUsername()]);
    }

    /**
     * Set players
     *
     */
    public function setPlayers(array $players)
    {
        $this->players = $players;
    }

    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @return the $summary
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param field_type $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return the $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param field_type $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @Assert\False(message = "Player much not appear more than once")
     */
    public function isDuplicatePlayers()
    {
        $cache = array();
        foreach ( $this->getPlayers() as $player )
        {
            $playerUsername = $player->getUsername();
            if (!$playerUsername)
            {
                continue;
            }

            if (isset($cache[$playerUsername]))
            {
                return true;
            }

            $cache[$playerUsername] = 1;
        }

        return false;
    }

    /**
     * @Assert\False(message = "Character must not appear more than once")
     */
    public function isDuplicateCharacters()
    {
        $cache = array();
        foreach ( $this->getPlayers() as $player )
        {
            $charname = $player->getCharacter();
            if (!$charname)
            {
                continue;
            }

            if (isset($cache[$charname]))
            {
                if ($cache[$charname] == 2)
                    return true;

                $cache[$charname] += 1;
            }
            else
            {
                $cache[$charname] = 1;
            }

        }

        return false;
    }

    /**
     *
     */
    public function isFactionBalanced()
    {
        $factions = array();
        foreach ( $this->getPlayers() as $player )
        {
            $faction = $player->getFaction();

            if (!$faction)
            {
                continue;
            }

            $cache[$faction][] = $player;
        }

        return count($cache['hunter']) == count($cache['shadow']);
    }

    public function __toString(){
        return $this->name;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return the $gamers
     */
    public function getGamers()
    {
        return $this->gamers;
    }

    /**
     * @return the $updatedBy
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param field_type $gamers
     */
    public function setGamers($gamers)
    {
        $this->gamers = $gamers;
    }

    /**
     * @param field_type $updatedBy
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }


}
