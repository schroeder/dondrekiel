<?php

namespace DondrekielBundle\Periodic;

use Gos\Bundle\WebSocketBundle\Periodic\PeriodicInterface;

class DondrekielPeriodic implements PeriodicInterface
{
    /**
     * This function is executed every 5 seconds.
     *
     * For more advanced functionality, try injecting a Topic Service to perform actions on your connections every x seconds.
     */
    public function tick()
    {
        //   echo "Executed once every 5 seconds" . PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeout()
    {
//        return 5;
    }
}