<?php
namespace Odl\ShadowBundle\Parser;

use Odl\ShadowBundle\Documents\Character;

use Odl\ShadowBundle\Documents\PlayerCharacter;
use Odl\ShadowBundle\Documents\Game;
use Symfony\Component\Yaml\Yaml;

class Parser
{
	protected static $chars = null;
	public static function loadChars($filename = null)
	{
		if (!self::$chars)
		{
			if (!$filename)
			{
				$filename = '/../Resources/chars.yml';
				$filename = __DIR__ . $filename;
			}

			$charsConfig = Yaml::load($filename);
			$chars = array();
			foreach ($charsConfig as $faction => $types)
			{
				foreach ($types as $type => $charInfos)
				{
					foreach ($charInfos as $name => $hitPoint)
					{
						$char = new Character($name, $faction, $hitPoint);
						$char->type = $type;
						$chars[$name] = $char;
					}
				}
			}

			self::$chars = $chars;
		}

		return self::$chars;
	}

	/**
	 * Parse the csv and return array of games
	 *
	 * @param String $filename
	 * @return array Odl\ShadowBundle\Documents\Game
	 */
	public static function loadCSVFile($filename)
	{
		if (!$filename)
		{
			$filename = '/../Resources/stats.csv';
			$filename = __DIR__ . $filename;
		}

		// Load csv file and read stats
		$csvString = file_get_contents($filename);
		$data = str_getcsv($csvString, "\n");
		$chars = self::loadChars();

		$useableData = array();
		$rowHeader = null;
		$games = array();
		foreach($data as $row)
		{
			$row = str_getcsv($row, ",");
			if ($row[0])
			{
				if (!$rowHeader)
				{
					$rowHeader = $row;
				}
				else
				{
					$date = new \DateTime($row[0]);
					$game = new Game($date);
					foreach ($row as $index => $charInfo)
					{
						if ($charInfo && $index != 0)
						{
							$charArray = explode(' ', $charInfo);
							$charname = $charArray[0];
							if ($charname == 'ultra')
							{
								$charname = 'ultrasoul';
							}

							if (!isset($chars[$charname]))
							{
								throw new \Exception("{$charname} does not exists");
							}

							$username = $rowHeader[$index];
							$player = new PlayerCharacter($username, $charname);

							$player->isAlive = (strpos($charInfo,'alive') > 0);
							$player->isWin = (strpos($charInfo, 'won') > 0);
							$player->isLastDeath = (strpos($charInfo, 'last') > 0);
							$player->char = $chars[$charname];

							$game->addPlayer($player);
						}
					}

					$games[] = $game;
				}
			}
		}

		return $games;
	}
}