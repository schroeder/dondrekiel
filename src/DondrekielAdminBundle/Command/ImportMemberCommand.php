<?php

namespace DondrekielAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use DondrekielAdminBundle\Repository\MemberRepository;
use DondrekielAdminBundle\Repository\TeamRepository;
use DondrekielAppBundle\Entity\Member;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\Level;
use DondrekielAdminBundle\Game\GameLogic;

class ImportMemberCommand extends ContainerAwareCommand
{
    /*
     * TODO: Run final import
     * */
    protected function configure()
    {
        $this
            ->setName('dondrekiel:importmember')
            ->setDescription('Import the member.')
            ->addOption(
                'file',
                'f',
                InputOption::VALUE_REQUIRED,
                'File to import',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getOption('file');

        $fs = new Filesystem();
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        $output->writeln('Importing file ' . $file);

        try {
            $fs->exists($file);
        } catch (IOExceptionInterface $e) {
            $output->writeln("<fg=red>File not found!</fg=red>");
        }

        $memberRepository = $em->getRepository("DondrekielAdminBundle:Member");
        $teamRepository = $em->getRepository("DondrekielAdminBundle:Team");

        $teamBuilderMemberList = [];

        try {
            $row = 1;
            if (($handle = fopen($file, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    if ($row > 1) {
                        $member = $memberRepository->findOneByPasscode($data[5]);

                        if (!$member) {
                            $output->writeln('<fg=yellow>Importing member ' . $data[5] . '</fg=yellow>');
                            $member = new Member();
                            $member->setPasscode($data[5]);
                        } else {
                            $output->writeln('<fg=blue>Updating member ' . $data[5] . '</fg=blue>');
                        }

                        $member->setName($data[1]);
                        $member->setFirstName($data[2]);
                        $grade = false;
                        switch ($data[3]) {
                            case "W":
                            case "Wölfling":
                                $grade = 'w';
                                break;
                            case "J":
                            case "Juffi":
                                $grade = 'j';
                                break;
                            case "Pf":
                            case "Pfadi":
                                $grade = 'p';
                                break;
                            case "R":
                            case "Rover":
                                $grade = 'r';
                                break;
                            case "Wö-Leiter":
                            case "Mitarbeiter":
                            case "Pfadi-Leiter":
                            case "Rover-Begleiter":
                            case "Juffi-Leiter":
                            case "LW":
                            case "LJ":
                            case "LPf":
                            case "LR":
                                $grade = 'l';
                                break;
                            default:
                                $grade = 's';
                                break;
                        }
                        $member->setGrade($grade);
                        $group = $data[0];
                        $member->setGroup($group);
                        $village = trim(substr($data[6], 0, strpos($data[6], '/')));
                        $member->setVillage($village);

                        $em->persist($member);
                        $em->flush();

                        if (!array_key_exists($village, $teamBuilderMemberList)) {
                            $teamBuilderMemberList[$village] = [];
                        }
                        if (!array_key_exists($group, $teamBuilderMemberList[$village])) {
                            $teamBuilderMemberList[$village][$group] = [];
                        }
                        if (!array_key_exists($grade, $teamBuilderMemberList[$village][$group])) {
                            $teamBuilderMemberList[$village][$group][$grade] = [];
                        }
                        $teamBuilderMemberList[$village][$group][$grade][] = $member;

                    }
                    $row++;
                }
                fclose($handle);

            }
        } catch (IOExceptionInterface $e) {
            $output->writeln("<fg=red>Cannot read file!</fg=red>");
        }
        $output->writeln("<fg=green>Done!</fg=green>");
    }
}