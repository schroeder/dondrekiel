<?php

namespace DondrekielAdminBundle\Command;

use DondrekielAppBundle\Entity\TeamLevelGame;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use DondrekielAdminBundle\Repository\MemberRepository;
use DondrekielAdminBundle\Repository\TeamRepository;
use DondrekielAppBundle\Entity\Member;
use DondrekielAppBundle\Entity\Team;
use DondrekielAdminBundle\Game\GameLogic;

class CreateAdminCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dondrekiel:create_admin')
            ->setDescription('Let the game begin!')
            ->addOption(
                'name',
                false,
                InputOption::VALUE_REQUIRED,
                'Let the game begin!'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adminName = $input->getOption('name');

        if ($adminName) {
            $output->writeln('<fg=green>Creating admin ' . $adminName . '</fg=green>');

            $em = $this->getContainer()->get('doctrine')->getEntityManager();

            $team = new Team();
            $team->setPasscode(md5(time() + rand(0, 1000) . rand(0, 1000)));
            $team->setStatus(Team::STATUS_ADMIN);
            $team->setGrade('a');
            $em->persist($team);


            $member = new Member();
            $member->setPasscode(md5(time() + rand(0, 1000) . rand(0, 1000)));
            $member->setGrade('a');
            $member->setName($adminName);
            $member->setFirstName($adminName);
            $member->setTeam($team);

            $em->persist($member);
            $em->flush();


        }
        $output->writeln("<fg=green>Done!</fg=green>");
    }
}