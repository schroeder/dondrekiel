<?php

namespace DondrekielAdminBundle\Extensions;

use Doctrine\ORM\EntityManager;

class TwigExtensions extends \Twig_Extension
{
    public function getFunctions()
    {
        // Register the function in twig :
        // In your template you can use it as : {{find(123)}}
        return array(
            new \Twig_SimpleFunction('getActiveGames', array($this, 'getActiveGames')),
            new \Twig_SimpleFunction('getFinishedGames', array($this, 'getFinishedGames')),
        );
    }

    protected $doctrine;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getActiveGames($id)
    {
        $myRepo = $this->em->getRepository('DondrekielAdminBundle:Game');

        return $myRepo->countActiveGames($id);
    }

    public function getFinishedGames($id)
    {
        $myRepo = $this->em->getRepository('DondrekielAdminBundle:Game');

        return $myRepo->countFinishedGames($id);
    }

    public function getName()
    {
        return 'Twig myCustomName Extensions';
    }
}