<?php

namespace DondrekielAppBundle\EventListener;

use DondrekielAppBundle\Entity\Station;
use DondrekielAppBundle\Entity\Action;
use Gos\Bundle\WebSocketBundle\Event\ClientEvent;
use Gos\Bundle\WebSocketBundle\Event\ClientErrorEvent;
use Gos\Bundle\WebSocketBundle\Event\ServerEvent;
use Gos\Bundle\WebSocketBundle\Event\ClientRejectedEvent;
use DondrekielAppBundle\Entity\Team;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DondrekielLoginEventListener
{
    private $entityManager;
    protected $encoderFactory;

    public function __construct($doctrine, EncoderFactoryInterface $encoderFactory)
    {
        $this->entityManager = $doctrine->getEntityManager();
        $this->encoderFactory = $encoderFactory;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /* @var Team $user */
        $user = $event->getAuthenticationToken()->getUser();

        $user->setLastLogin(new \DateTime());
        if ($user->getIsTeam()) {
            $user->setStatus(Team::STATUS_ACTIVE);
        } else {
            $user->setStatus(Team::STATUS_STATION);

        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function getEncoder(Team $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }

    public function updateUser(Team $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (!empty($plainPassword)) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($plainPassword, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Team) {
            $this->updateUser($entity);
        }
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Team) {
            $this->updateUser($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Station) {
            /* @var $entity Station */
            $action = new Action();
            $action->setStation($entity);
            if ($entity->getStatus() == 1) {
                $action->setAction(Action::ACTION_STATION_ACTIVE);
            } else {
                $action->setAction(Action::ACTION_STATION_INACTIVE);
            }
            $action->setCreateTime(time());

            $this->entityManager->persist($action);
            $this->entityManager->flush();
        }
    }
}