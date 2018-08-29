<?php

namespace DondrekielAppBundle\Repository;

use Avanzu\AdminThemeBundle\EventListener\NavbarShowUserDemoListener;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use DondrekielAppBundle\Entity\Message;

class MessageRepository extends EntityRepository
{

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return true;
    }


    public function findAllUnread()
    {
        return $this->findBy(array('sendTime' => NULL), array('createTime' => 'ASC'));
    }

    public function findAll()
    {
        return $this->findBy(array(), array('sendTime' => 'DESC'));
    }
}
