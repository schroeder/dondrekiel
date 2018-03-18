<?php

namespace DondrekielAppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use DondrekielAppBundle\Entity\Station;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\Actionlog;

class ActionLogRepository extends EntityRepository
{
    public function findByTeam($teamId)
    {
        $result = $this->_em->createQuery("SELECT p FROM DondrekielAppBundle\Entity\Position p WHERE p.team_id= :teamId")
            ->setParameters(array('teamId' => $teamId))
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }

    public function currentPositionByTeam($teamId)
    {
        $result = $this->_em->createQuery("SELECT p FROM DondrekielAppBundle\Entity\Position p WHERE p.team_id= :teamId")
            ->setParameters(array('teamId' => $teamId))
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }

    public function findByLogLevel($logLevel)
    {
        $result = $this->_em->createQuery("SELECT t FROM DondrekielAppBundle\Entity\ActionLog t WHERE t.log_level= :logLevel")
            ->setParameters(array('logLevel' => $logLevel))
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }
}