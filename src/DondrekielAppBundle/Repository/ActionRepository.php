<?php

namespace DondrekielAppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use DondrekielAppBundle\Entity\Station;
use DondrekielAppBundle\Entity\Team;
use DondrekielAppBundle\Entity\Action;

class ActionRepository extends EntityRepository
{
    public function findAll()
    {
        $result = $this->_em->createQuery("SELECT t FROM DondrekielAppBundle\Entity\Action t LIMIT 0,1000")
            ->execute();
        if (count($result) == 1) {
            return $result;
        }
        return false;
    }

    public function findAllUnread()
    {
        return $this->findBy(array('sendTime' => NULL), array('createTime' => 'ASC'));
    }
}