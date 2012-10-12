<?php
namespace Odl\ShadowBundle\Command;

use Odl\ShadowBundle\Documents\GamePlayer;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('shadow:migrate')
			->setDescription('Migrate Embedded documents to referenced documents')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getContainer();
		$gameManager = $container->get('shadow.manager.game');
		$dm = $gameManager->getDocumentManager();

		$games = $gameManager->getRepository()->findAll();

		foreach ($games as $game) {
			$players = $game->getPlayers();

			$gamers = array();
			foreach ($players as $player) {
				$gamePlayer = new GamePlayer();
				$gamePlayer->setCharacter($player->getCharacter());
				$gamePlayer->isWin = $player->isWin;
				$gamePlayer->isAlive = $player->isAlive;
				$gamePlayer->isLastDeath = $player->isLastDeath;
				$gamePlayer->setUsername($player->getUserName());

				$gamePlayer->setGame($game);

				$dm->persist($gamePlayer);
				$gamers[] = $gamePlayer;
			}

			$game->setGamers($gamers);
			$dm->flush();
		}

		$output->writeln('Migration Finished!');
	}
}
