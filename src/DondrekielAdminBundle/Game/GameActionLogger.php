<?php

namespace DondrekielAdminBundle\Game;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use DondrekielAppBundle\Entity\Actionlog;
use Symfony\Component\HttpKernel\Tests\Fixtures\Controller\NullableController;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\Station;

class GameActionLogger
{
    /* @var EntityManager $em */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    function logAction($message, $logLevel = Actionlog::LOGLEVEL_TEAM_INFO, $team = NULL, $game = NULL, $log_time = 0)
    {
        if ($log_time === 0) {
            $log_time = time();
        }

        if ($team == false) {
            $team = NULL;
        }
        if ($game == false) {
            $game = NULL;
        }

        try {
            $log = new Actionlog();
            $log->setLogLevel($logLevel);
            $log->setLogText($message);
            $log->setTeam($team);
            $log->setGame($game);
            $log->setTimestamp($log_time);

            $this->em->persist($log);
            $this->em->flush();

        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }
}