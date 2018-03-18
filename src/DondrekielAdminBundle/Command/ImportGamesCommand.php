<?php

namespace DondrekielAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use DondrekielAdminBundle\Repository\GameRepository;
use DondrekielAppBundle\Entity\Station;
use DondrekielAppBundle\Entity\Team;

class ImportGamesCommand extends ContainerAwareCommand
{
    /*
     * TODO: Run final import
     * */
    protected function configure()
    {
        $this
            ->setName('dondrekiel:importgames')
            ->setDescription('Import the games.')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_REQUIRED,
                'File to import',
                false
            )->addOption(
                'level',
                'l',
                InputOption::VALUE_REQUIRED,
                'Level of games in file',
                1
            )->addOption(
                'grade',
                'g',
                InputOption::VALUE_REQUIRED,
                'Grade of the games',
                'w'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getOption('file');
        $levelId = $input->getOption('level');
        $grade = $input->getOption('grade');

        $fs = new Filesystem();
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        if (!$levelId || $levelId > 7 || $levelId < 1) {
            $output->writeln("<fg=red>Level missing!</fg=red>");
            die();
        } else {
            $levelRepository = $em->getRepository("DondrekielAdminBundle:Level");
            $level = $levelRepository->findOneByNumber($levelId);
            if (!$level) {
                $output->writeln("<fg=red>Level missing!</fg=red>");
                die();
            }
        }

        if (!in_array($grade, ['w', 'j', 'p', 'r', 'wj', 'pr', 'wjpr'])) {
            $output->writeln("<fg=red>Grade missing!</fg=red>");
            die();
        }

        $output->writeln('Importing file ' . $file);

        try {
            $fs->exists($file);
        } catch (IOExceptionInterface $e) {
            $output->writeln("<fg=red>File not found!</fg=red>");
        }

        $gameRepository = $em->getRepository("DondrekielAdminBundle:Game");
        $locationRepository = $em->getRepository("DondrekielAdminBundle:Location");

        try {
            $row = 1;
            if (($handle = fopen($file, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    if ($data[0] != "" && $data[0] != "Spielnummer" && $data[0] != "Spielnr" && $data[3] != "0") {

                        $game = $gameRepository->findOneByIdentifier($data[0]);

                        if (!$game) {
                            $output->writeln('Importing game ' . $data[0]);

                            $team = new Team();
                            $team->setPasscode(md5($data[0]));
                            $team->setStatus(Team::STATUS_GAME);
                            $team->setGrade('g');
                            $em->persist($team);

                            $em->flush();

                            $game = new Station();
                            $game->setIdentifier($data[0]);
                        } else {
                            $output->writeln('Updating game ' . $data[0]);
                        }

                        $game->setName($data[1]);
                        if ($data[9] != "" && is_int($data[9])) {
                            $game->setDuration($data[9]);
                        } else {
                            $game->setDuration(5);
                        }
                        $game->setLevel($level);

                        $location = false;
                        if ($data[10] != "") {
                            $location = $locationRepository->findOneByName(strtolower($data[10]));
                        }
                        if ($location) {
                            $game->setLocation($location);
                        } else {
                            //$location = $locationRepository->findById(1);
                            //$game->setLocation($data[10]);
                        }
                        $game->setStatus(Station::STATUS_ACTIVE);
                        $game->setGrade($grade);
                        $game->setPasscode(md5($data[0]));
                        $game->setGameAnswer($data[7]);

                        if ($data[3] != "") {
                            $game->setMaxPlayRounds($data[3]);
                        } else {
                            $game->setMaxPlayRounds(1);
                        }

                        if ($data[11] != "") {
                            $game->setPriority($data[11]);
                        } else {
                            $game->setPriority(1);
                        }

                        $em->persist($game);
                        $em->flush();

                    }
                }
                fclose($handle);
            }
        } catch (IOExceptionInterface $e) {
            $output->writeln("<fg=red>Cannot read file!</fg=red>");
        }
        $output->writeln("<fg=green>Done!</fg=green>");
    }
}