<?php

namespace DondrekielAppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use DondrekielAppBundle\Entity\Team;

class TeamMemberRepository extends EntityRepository
{

}
