<?php
namespace Odl\ShadowBundle\Chart;

use Odl\ShadowBundle\Stats\StatsProvider;
use Odl\ShadowBundle\Model\ObjectManager;

class ChartProvider
{
    private $om;
    private $games;
    private $chars;
    private $statsProvider;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->games = $om->getAllGames();
        $this->chars = $om->getCharacters();
        $this->statsProvider = new StatsProvider($this->games);
    }

    public function getPlayerFactionDistrubution()
    {
        $allPlayers = $this->statsProvider->getPlayerStats();
        $data = array();
        $categories = array();
        foreach ($allPlayers as $player) {
            $categories[] = $player->name;

            foreach ($player->factions as $faction => $stats) {
                $faction = ucfirst($faction);
                $data['played'][$faction][] = $stats->totalPlayed;
                $data['won'][$faction][] = $stats->totalWin;
            }
        }

        $chart = new BarChart('Faction', $data['played'], $categories);

        return $chart;
    }

    /**
     * Return player chance to win/survive over time
     *
     * @param array $games
     * @param array $allPlayers
     */
    public function getPlayerStatsOverTime()
    {
        $games = $this->games;
        $allPlayers = $this->statsProvider->getPlayerStats();

        $incrementGames = array();
        $seriesHash = array();
        $gameCount = array();
        $statsType = array(
                'ChanceToWin',
                'RelativeChanceToWin',
                'ChanceToLive',
                'RelativeChanceToLive'
        );

        $symbols = array(
                'shadow' => 'red',
                'hunter' => 'green',
                'neutral' => 'gray'
        );

        $index = 0;
        foreach ( $games as $game )
        {
            $index++;
            $incrementGames[] = $game;
            $statProvider = new StatsProvider($incrementGames);
            $playersStats = $statProvider->getPlayerStats();

            // Init defaults


            // Keep track of total games played
            foreach ( $game->getPlayers() as $player )
            {
                $playerName = $player->getUsername();
                $faction = $player->getChar()
                    ->getFaction();
                $factionColor = $symbols[$faction];

                if (!isset($gameCount[$playerName]))
                {
                    $gameCount[$playerName] = 0;
                }

                $gameCount[$playerName] += 1;
                $playerStats = $playersStats[$playerName];
                $seriesHash['Win'][$playerName][] = array(
                        'x' => $index,
                        'y' => $playerStats->getChanceToWin(),
                        'factionColor' => $factionColor
                );

                $seriesHash['Survival'][$playerName][] = array(
                        'x' => $index,
                        'y' => $playerStats->getChanceToLive(),
                        'factionColor' => $factionColor
                );

                foreach ( $playerStats->factions as $factionStats )
                {
                    $factionName = ucfirst($factionStats->name);
                    /*$seriesHash[$factionName][$playerName][] = array(
						'x' => $index,
						'y' => $factionStats->getChanceToWin(),
						'factionColor' => $factionColor);*/

                    $seriesHash[$factionName][$playerName][] = array(
                            'x' => $index,
                            'y' => $factionStats->getRelativeChanceToWin(),
                            'factionColor' => $factionColor
                    );
                }
            }
        }

        // Drop low game count players + sort by names
        foreach ( $gameCount as $playerName => $total )
        {
            if ($total < 7)
            {
                foreach ( $seriesHash as $key => $type )
                {
                    // Remove players with less than 8 games
                    unset($seriesHash[$key][$playerName]);
                    ksort($seriesHash[$key]);
                }
            }
        }

        $retVal = array();
        $categories = range(1, count($games));
        foreach ( $seriesHash as $title => $series )
        {
            $key = str_replace(' ', '_', $title);
            $key = strtolower($key);
            $retVal[$key] = new Chart($title, $series, $categories);
        }

        return $retVal;
    }
}