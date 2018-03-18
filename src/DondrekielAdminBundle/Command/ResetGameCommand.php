<?php

namespace DondrekielAdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\EntityManager;
use DondrekielAppBundle\Entity\Member;

class ResetGameCommand extends ContainerAwareCommand
{

    /*
     * TODO: Run final joker import
     *
     * */
    protected function configure()
    {
        $this
            ->setName('dondrekiel:reset')
            ->setDescription('Import the games.')
            ->addOption(
                'reset',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Reset member, teams, games, played, log',
                []
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resetOptions = $input->getOption('reset');
        $output->writeln("<fg=green>Starting reset process</fg=green>");

        foreach ($resetOptions as $type) {
            switch ($type) {
                case "member": {
                    $output->write("<fg=blue>Reset member</fg=blue> ", false);
                    if ($this->truncateTable(["DondrekielAppBundle\Entity\Member"])) {
                        $output->write("<fg=green>OK</fg=green>", true);
                    } else {
                        $output->write("<fg=red>Failed</fg=red>", true);
                    }
                }
                    break;
                case "teams": {
                    $output->write("<fg=blue>Reset teams</fg=blue> ", false);
                    if ($this->truncateTable(["DondrekielAppBundle\Entity\TeamLevelGame", "DondrekielAppBundle\Entity\TeamLevel", "DondrekielAppBundle\Entity\Team"])) {
                        $output->write("<fg=green>OK</fg=green>", true);
                    } else {
                        $output->write("<fg=red>Failed</fg=red>", true);
                    }
                }
                    break;
                case "games": {
                    $output->write("<fg=blue>Reset games</fg=blue> ", false);
                    if ($this->truncateTable(["DondrekielAppBundle\Entity\Game"])) {
                        $output->write("<fg=green>OK</fg=green>", true);
                    } else {
                        $output->write("<fg=red>Failed</fg=red>", true);
                    }
                }
                    break;
                case "played": {
                    $output->write("<fg=blue>Reset played games</fg=blue> ", false);
                    if ($this->truncateTable(["DondrekielAppBundle\Entity\TeamLevelGame", "DondrekielAppBundle\Entity\TeamLevel"])) {
                        $output->write("<fg=green>OK</fg=green>", true);
                    } else {
                        $output->write("<fg=red>Failed</fg=red>", true);
                    }
                }
                    break;
                case "log": {
                    $output->write("<fg=blue>Reset log</fg=blue> ", false);
                    if ($this->truncateTable(["DondrekielAppBundle\Entity\Actionlog"])) {
                        $output->write("<fg=green>OK</fg=green>", true);
                    } else {
                        $output->write("<fg=red>Failed</fg=red>", true);
                    }
                }
                    break;
            }
        }

        $output->write("<fg=green>Done!</fg=green>");
    }

    private function truncateTable($classList)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        try {
            $connection = $em->getConnection();
            $connection->beginTransaction();
            foreach ($classList as $className) {
                $cmd = $em->getClassMetadata($className);
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $connection->query('DELETE FROM ' . $cmd->getTableName());
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
            return false;
        }
        return true;
    }
}