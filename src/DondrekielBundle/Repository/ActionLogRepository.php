<?php

namespace DondrekielBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use DondrekielBundle\Entity\Game;
use DondrekielBundle\Entity\Team;
use DondrekielBundle\Entity\Actionlog;

class ActionLogRepository extends EntityRepository
{
    public function findAll()
    {
        $result = $this->_em->createQuery("SELECT t FROM DondrekielBundle\Entity\ActionLog t LIMIT 0,1000")
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }

    public function findByGame($gameId)
    {
        $result = $this->_em->createQuery("SELECT t FROM DondrekielBundle\Entity\ActionLog t WHERE t.game_id= :gameId")
            ->setParameters(array('gameId' => $gameId))
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }

    public function findByTeam($teamId)
    {
        $result = $this->_em->createQuery("SELECT t FROM DondrekielBundle\Entity\ActionLog t WHERE t.team_id= :teamId")
            ->setParameters(array('teamId' => $teamId))
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }

    public function findByLogLevel($logLevel)
    {
        $result = $this->_em->createQuery("SELECT t FROM DondrekielBundle\Entity\ActionLog t WHERE t.log_level= :logLevel")
            ->setParameters(array('logLevel' => $logLevel))
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }
}